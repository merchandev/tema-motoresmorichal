import { createHash } from 'node:crypto'
import { mkdirSync, readFileSync, writeFileSync } from 'node:fs'
import { basename, dirname, relative, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { themeSlug } from '../release.config.mjs'
import { getReleasePaths } from './release-files.mjs'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const packageJson = JSON.parse(readFileSync(resolve(projectRoot, 'package.json'), 'utf8'))
const releaseDir = resolve(projectRoot, 'release')
const outputPath = resolve(releaseDir, `${themeSlug}-${packageJson.version}.zip`)
const checksumPath = `${outputPath}.sha256`
const maxBytesText = process.env.RELEASE_MAX_BYTES ?? String(96 * 1024 * 1024)
if (!/^\d+$/.test(maxBytesText)) throw new Error('RELEASE_MAX_BYTES must be a positive integer.')
const maxBytes = Number(maxBytesText)
function sha256(buffer) {
  return createHash('sha256').update(buffer).digest('hex')
}

const crcTable = Array.from({ length: 256 }, (_, index) => {
  let value = index
  for (let bit = 0; bit < 8; bit += 1) {
    value = (value & 1) ? (0xedb88320 ^ (value >>> 1)) : (value >>> 1)
  }
  return value >>> 0
})

function crc32(buffer) {
  let crc = 0xffffffff
  for (const byte of buffer) crc = crcTable[(crc ^ byte) & 0xff] ^ (crc >>> 8)
  return (crc ^ 0xffffffff) >>> 0
}

function dosTimestamp(date) {
  const safeYear = Math.max(1980, Math.min(2107, date.getUTCFullYear()))
  const time = (date.getUTCHours() << 11) | (date.getUTCMinutes() << 5) | Math.floor(date.getUTCSeconds() / 2)
  const day = Math.max(1, date.getUTCDate())
  const month = Math.max(1, date.getUTCMonth() + 1)
  const dosDate = ((safeYear - 1980) << 9) | (month << 5) | day
  return { time, date: dosDate }
}

function createZip(entries, timestamp) {
  const localParts = []
  const centralParts = []
  const { time, date } = dosTimestamp(timestamp)
  let offset = 0

  for (const entry of entries) {
    const name = Buffer.from(`${themeSlug}/${entry.path}`, 'utf8')
    const data = entry.data
    if (data.length > 0xffffffff) throw new Error(`File exceeds ZIP32 limit: ${entry.path}`)
    const checksum = crc32(data)
    const localHeader = Buffer.alloc(30)
    localHeader.writeUInt32LE(0x04034b50, 0)
    localHeader.writeUInt16LE(20, 4)
    localHeader.writeUInt16LE(0x0800, 6)
    localHeader.writeUInt16LE(0, 8)
    localHeader.writeUInt16LE(time, 10)
    localHeader.writeUInt16LE(date, 12)
    localHeader.writeUInt32LE(checksum, 14)
    localHeader.writeUInt32LE(data.length, 18)
    localHeader.writeUInt32LE(data.length, 22)
    localHeader.writeUInt16LE(name.length, 26)
    localHeader.writeUInt16LE(0, 28)

    localParts.push(localHeader, name, data)

    const centralHeader = Buffer.alloc(46)
    centralHeader.writeUInt32LE(0x02014b50, 0)
    centralHeader.writeUInt16LE((3 << 8) | 20, 4)
    centralHeader.writeUInt16LE(20, 6)
    centralHeader.writeUInt16LE(0x0800, 8)
    centralHeader.writeUInt16LE(0, 10)
    centralHeader.writeUInt16LE(time, 12)
    centralHeader.writeUInt16LE(date, 14)
    centralHeader.writeUInt32LE(checksum, 16)
    centralHeader.writeUInt32LE(data.length, 20)
    centralHeader.writeUInt32LE(data.length, 24)
    centralHeader.writeUInt16LE(name.length, 28)
    centralHeader.writeUInt16LE(0, 30)
    centralHeader.writeUInt16LE(0, 32)
    centralHeader.writeUInt16LE(0, 34)
    centralHeader.writeUInt16LE(0, 36)
    centralHeader.writeUInt32LE((0o100644 << 16) >>> 0, 38)
    centralHeader.writeUInt32LE(offset, 42)
    centralParts.push(centralHeader, name)

    offset += localHeader.length + name.length + data.length
  }

  if (entries.length > 0xffff) throw new Error('Release exceeds ZIP32 entry limit.')
  const centralDirectory = Buffer.concat(centralParts)
  const end = Buffer.alloc(22)
  end.writeUInt32LE(0x06054b50, 0)
  end.writeUInt16LE(0, 4)
  end.writeUInt16LE(0, 6)
  end.writeUInt16LE(entries.length, 8)
  end.writeUInt16LE(entries.length, 10)
  end.writeUInt32LE(centralDirectory.length, 12)
  end.writeUInt32LE(offset, 16)
  end.writeUInt16LE(0, 20)

  return Buffer.concat([...localParts, centralDirectory, end])
}

const comparePaths = (left, right) => (left < right ? -1 : left > right ? 1 : 0)
const selectedPaths = getReleasePaths()

const sourceDateEpochText = process.env.SOURCE_DATE_EPOCH ?? '315532800'
if (!/^\d+$/.test(sourceDateEpochText)) throw new Error('SOURCE_DATE_EPOCH must be a non-negative integer Unix timestamp.')
const sourceDateEpoch = Number(sourceDateEpochText)
if (!Number.isSafeInteger(sourceDateEpoch)) throw new Error('SOURCE_DATE_EPOCH is outside the supported integer range.')
const timestamp = new Date(sourceDateEpoch * 1000)
if (Number.isNaN(timestamp.getTime())) throw new Error('SOURCE_DATE_EPOCH is outside the supported date range.')
const entries = selectedPaths.map((path) => {
  const data = readFileSync(resolve(projectRoot, path))
  return { path, data, sha256: sha256(data) }
})
const sourceBytes = entries.reduce((total, entry) => total + entry.data.length, 0)

if (!Number.isSafeInteger(maxBytes) || maxBytes <= 0) throw new Error('RELEASE_MAX_BYTES must be a positive safe integer.')
if (sourceBytes > maxBytes) {
  throw new Error(`Allowlisted release is ${sourceBytes} bytes, above RELEASE_MAX_BYTES=${maxBytes}.`)
}

const manifest = {
  name: themeSlug,
  version: packageJson.version,
  sourceDateEpoch,
  files: entries.map(({ path, data, sha256: checksum }) => ({ path, bytes: data.length, sha256: checksum })),
}
const manifestData = Buffer.from(`${JSON.stringify(manifest, null, 2)}\n`, 'utf8')
entries.push({ path: 'RELEASE-MANIFEST.json', data: manifestData, sha256: sha256(manifestData) })
entries.sort((left, right) => comparePaths(left.path, right.path))

const zip = createZip(entries, timestamp)
mkdirSync(dirname(outputPath), { recursive: true })
writeFileSync(outputPath, zip)
writeFileSync(checksumPath, `${sha256(zip)}  ${basename(outputPath)}\n`)

console.log(`Created ${relative(projectRoot, outputPath)} (${zip.length} bytes, ${entries.length} files).`)
console.log(`Checksum: ${relative(projectRoot, checksumPath)}`)
