import { createHash } from 'node:crypto'
import { existsSync, mkdirSync, mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs'
import { tmpdir } from 'node:os'
import { basename, dirname, isAbsolute, join, relative, resolve } from 'node:path'
import { spawnSync } from 'node:child_process'
import { fileURLToPath } from 'node:url'
import { themeSlug } from '../release.config.mjs'
import { getReleasePaths } from './release-files.mjs'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const packageJson = JSON.parse(readFileSync(resolve(projectRoot, 'package.json'), 'utf8'))
const zipPath = resolve(projectRoot, 'release', `${themeSlug}-${packageJson.version}.zip`)
const checksumPath = `${zipPath}.sha256`
const sourceDateEpochText = process.env.SOURCE_DATE_EPOCH ?? '315532800'
if (!/^\d+$/.test(sourceDateEpochText)) throw new Error('SOURCE_DATE_EPOCH must be a non-negative integer Unix timestamp.')
const expectedSourceDateEpoch = Number(sourceDateEpochText)
if (!Number.isSafeInteger(expectedSourceDateEpoch)) throw new Error('SOURCE_DATE_EPOCH is outside the supported integer range.')
const expectedReleaseDate = new Date(expectedSourceDateEpoch * 1000)
if (Number.isNaN(expectedReleaseDate.getTime())) throw new Error('SOURCE_DATE_EPOCH is outside the supported date range.')

const distVerification = spawnSync(process.execPath, [resolve(projectRoot, 'scripts', 'verify-dist.mjs')], {
  cwd: projectRoot,
  encoding: 'utf8',
})
if (distVerification.status !== 0) {
  throw new Error(`Release verification requires a current dist/.\n${distVerification.stderr || distVerification.stdout}`)
}

if (!existsSync(zipPath) || !existsSync(checksumPath)) {
  throw new Error('Release ZIP or checksum is missing. Run npm run package first.')
}

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

function validateEntryName(name) {
  if (!name.startsWith(`${themeSlug}/`) || name.includes('\\') || isAbsolute(name)) {
    throw new Error(`Unsafe ZIP entry: ${name}`)
  }
  const segments = name.split('/')
  if (segments.some((segment) => segment === '' || segment === '.' || segment === '..')) {
    throw new Error(`Unsafe ZIP entry segments: ${name}`)
  }
}

function dosTimestamp(date) {
  const safeYear = Math.max(1980, Math.min(2107, date.getUTCFullYear()))
  const time = (date.getUTCHours() << 11) | (date.getUTCMinutes() << 5) | Math.floor(date.getUTCSeconds() / 2)
  const day = Math.max(1, date.getUTCDate())
  const month = Math.max(1, date.getUTCMonth() + 1)
  return {
    time,
    date: ((safeYear - 1980) << 9) | (month << 5) | day,
  }
}

function readStoredZip(buffer) {
  const localEntries = new Map()
  const localNames = []
  const expectedTimestamp = dosTimestamp(expectedReleaseDate)
  let offset = 0
  let centralOffset = -1

  while (offset + 4 <= buffer.length) {
    const signature = buffer.readUInt32LE(offset)
    if (signature === 0x02014b50) {
      centralOffset = offset
      break
    }
    if (signature !== 0x04034b50 || offset + 30 > buffer.length) {
      throw new Error(`Invalid local ZIP header at byte ${offset}.`)
    }

    const localOffset = offset
    const versionNeeded = buffer.readUInt16LE(offset + 4)
    const flags = buffer.readUInt16LE(offset + 6)
    const method = buffer.readUInt16LE(offset + 8)
    const modifiedTime = buffer.readUInt16LE(offset + 10)
    const modifiedDate = buffer.readUInt16LE(offset + 12)
    const expectedCrc = buffer.readUInt32LE(offset + 14)
    const compressedSize = buffer.readUInt32LE(offset + 18)
    const uncompressedSize = buffer.readUInt32LE(offset + 22)
    const nameLength = buffer.readUInt16LE(offset + 26)
    const extraLength = buffer.readUInt16LE(offset + 28)
    if (
      versionNeeded !== 20
      || flags !== 0x0800
      || method !== 0
      || compressedSize !== uncompressedSize
      || extraLength !== 0
      || modifiedTime !== expectedTimestamp.time
      || modifiedDate !== expectedTimestamp.date
    ) {
      throw new Error('Release verifier only accepts deterministic stored ZIP entries.')
    }

    const nameStart = offset + 30
    const dataStart = nameStart + nameLength + extraLength
    const dataEnd = dataStart + compressedSize
    if (dataEnd > buffer.length) throw new Error('ZIP entry exceeds archive bounds.')

    const nameBytes = buffer.subarray(nameStart, nameStart + nameLength)
    const name = nameBytes.toString('utf8')
    if (!Buffer.from(name, 'utf8').equals(nameBytes)) throw new Error('ZIP entry name is not valid UTF-8.')
    validateEntryName(name)
    if (localEntries.has(name)) throw new Error(`Duplicate ZIP entry: ${name}`)
    const data = buffer.subarray(dataStart, dataEnd)
    if (crc32(data) !== expectedCrc) throw new Error(`CRC mismatch: ${name}`)
    localEntries.set(name, {
      data,
      crc: expectedCrc,
      size: uncompressedSize,
      localOffset,
    })
    localNames.push(name)
    offset = dataEnd
  }

  const eocdOffset = buffer.length - 22
  if (
    eocdOffset < 0
    || buffer.readUInt32LE(eocdOffset) !== 0x06054b50
    || centralOffset < 0
    || eocdOffset < centralOffset
  ) {
    throw new Error('ZIP central directory is incomplete.')
  }
  const diskNumber = buffer.readUInt16LE(eocdOffset + 4)
  const centralDisk = buffer.readUInt16LE(eocdOffset + 6)
  const diskEntries = buffer.readUInt16LE(eocdOffset + 8)
  const expectedEntries = buffer.readUInt16LE(eocdOffset + 10)
  const declaredCentralSize = buffer.readUInt32LE(eocdOffset + 12)
  const declaredCentralOffset = buffer.readUInt32LE(eocdOffset + 16)
  const commentLength = buffer.readUInt16LE(eocdOffset + 20)
  if (
    diskNumber !== 0
    || centralDisk !== 0
    || diskEntries !== expectedEntries
    || expectedEntries !== localEntries.size
    || declaredCentralOffset !== centralOffset
    || declaredCentralSize !== eocdOffset - centralOffset
    || commentLength !== 0
  ) {
    throw new Error('ZIP directory metadata does not match local entries.')
  }

  const centralNames = new Set()
  const centralOrder = []
  let centralCursor = centralOffset
  for (let index = 0; index < expectedEntries; index += 1) {
    if (centralCursor + 46 > eocdOffset || buffer.readUInt32LE(centralCursor) !== 0x02014b50) {
      throw new Error(`Invalid central ZIP header at byte ${centralCursor}.`)
    }

    const versionMadeBy = buffer.readUInt16LE(centralCursor + 4)
    const versionNeeded = buffer.readUInt16LE(centralCursor + 6)
    const flags = buffer.readUInt16LE(centralCursor + 8)
    const method = buffer.readUInt16LE(centralCursor + 10)
    const modifiedTime = buffer.readUInt16LE(centralCursor + 12)
    const modifiedDate = buffer.readUInt16LE(centralCursor + 14)
    const expectedCrc = buffer.readUInt32LE(centralCursor + 16)
    const compressedSize = buffer.readUInt32LE(centralCursor + 20)
    const uncompressedSize = buffer.readUInt32LE(centralCursor + 24)
    const nameLength = buffer.readUInt16LE(centralCursor + 28)
    const extraLength = buffer.readUInt16LE(centralCursor + 30)
    const entryCommentLength = buffer.readUInt16LE(centralCursor + 32)
    const entryDisk = buffer.readUInt16LE(centralCursor + 34)
    const internalAttributes = buffer.readUInt16LE(centralCursor + 36)
    const externalAttributes = buffer.readUInt32LE(centralCursor + 38)
    const localOffset = buffer.readUInt32LE(centralCursor + 42)
    const centralEnd = centralCursor + 46 + nameLength + extraLength + entryCommentLength
    if (centralEnd > eocdOffset) throw new Error('Central ZIP entry exceeds directory bounds.')

    const nameBytes = buffer.subarray(centralCursor + 46, centralCursor + 46 + nameLength)
    const name = nameBytes.toString('utf8')
    if (!Buffer.from(name, 'utf8').equals(nameBytes)) throw new Error('Central ZIP entry name is not valid UTF-8.')
    validateEntryName(name)
    const local = localEntries.get(name)
    if (
      centralNames.has(name)
      || !local
      || name !== localNames[index]
      || versionMadeBy !== ((3 << 8) | 20)
      || versionNeeded !== 20
      || flags !== 0x0800
      || method !== 0
      || modifiedTime !== expectedTimestamp.time
      || modifiedDate !== expectedTimestamp.date
      || expectedCrc !== local.crc
      || compressedSize !== local.size
      || uncompressedSize !== local.size
      || extraLength !== 0
      || entryCommentLength !== 0
      || entryDisk !== 0
      || internalAttributes !== 0
      || externalAttributes !== ((0o100644 << 16) >>> 0)
      || localOffset !== local.localOffset
    ) {
      throw new Error(`Central ZIP entry does not match its local entry: ${name}`)
    }
    centralNames.add(name)
    centralOrder.push(name)
    centralCursor = centralEnd
  }
  if (centralCursor !== eocdOffset || centralNames.size !== localEntries.size) {
    throw new Error('ZIP central directory contains trailing or missing entries.')
  }

  const sortedNames = [...localNames].sort((left, right) => (left < right ? -1 : left > right ? 1 : 0))
  if (
    localNames.some((name, index) => name !== sortedNames[index])
    || centralOrder.some((name, index) => name !== localNames[index])
  ) {
    throw new Error('ZIP entries are not in canonical order.')
  }

  return new Map(Array.from(localEntries, ([name, entry]) => [name, entry.data]))
}

const zip = readFileSync(zipPath)
const checksumText = readFileSync(checksumPath, 'utf8').trim()
const checksumMatch = checksumText.match(/^([0-9a-f]{64})\s{2}([^\s]+)$/i)
if (!checksumMatch || checksumMatch[2] !== basename(zipPath)) {
  throw new Error('Release checksum file has an invalid hash or filename.')
}
const expectedChecksum = checksumMatch[1].toLowerCase()
const actualChecksum = sha256(zip)
if (expectedChecksum !== actualChecksum) {
  throw new Error(`Release checksum mismatch: expected ${expectedChecksum}, got ${actualChecksum}.`)
}

const entries = readStoredZip(zip)
const manifestName = `${themeSlug}/RELEASE-MANIFEST.json`
const manifestData = entries.get(manifestName)
if (!manifestData) throw new Error('RELEASE-MANIFEST.json is missing from the ZIP.')
const manifest = JSON.parse(manifestData.toString('utf8'))
if (
  manifest.name !== themeSlug
  || manifest.version !== packageJson.version
  || manifest.sourceDateEpoch !== expectedSourceDateEpoch
  || !Array.isArray(manifest.files)
) {
  throw new Error('Release manifest identity is invalid.')
}

const currentPaths = getReleasePaths()
if (!manifest.files.every((entry) => (
  entry
  && typeof entry.path === 'string'
  && Number.isSafeInteger(entry.bytes)
  && entry.bytes >= 0
  && typeof entry.sha256 === 'string'
  && /^[0-9a-f]{64}$/.test(entry.sha256)
))) {
  throw new Error('Release manifest contains an invalid file record.')
}
const manifestPaths = manifest.files.map((entry) => entry.path)
if (manifestPaths.length !== new Set(manifestPaths).size) throw new Error('Release manifest contains duplicate paths.')
const sortedManifestPaths = [...manifestPaths].sort((left, right) => (left < right ? -1 : left > right ? 1 : 0))
if (
  manifestPaths.some((path, index) => path !== sortedManifestPaths[index])
  ||
  currentPaths.length !== sortedManifestPaths.length
  || currentPaths.some((path, index) => path !== sortedManifestPaths[index])
) {
  throw new Error('Release manifest paths do not match the current allowlisted source tree.')
}

const expectedNames = new Set(currentPaths.map((path) => `${themeSlug}/${path}`))
if (entries.size !== expectedNames.size + 1) throw new Error('ZIP and manifest file counts differ.')
const expectedArchiveNames = [...expectedNames, manifestName]
  .sort((left, right) => (left < right ? -1 : left > right ? 1 : 0))
const archiveNames = [...entries.keys()]
if (archiveNames.some((name, index) => name !== expectedArchiveNames[index])) {
  throw new Error('ZIP entry order does not match the canonical release manifest order.')
}

for (const entry of manifest.files) {
  const name = `${themeSlug}/${entry.path}`
  const data = entries.get(name)
  if (!data) throw new Error(`Manifest file is missing: ${entry.path}`)
  if (data.length !== entry.bytes || sha256(data) !== entry.sha256) {
    throw new Error(`Manifest verification failed: ${entry.path}`)
  }
  const sourceData = readFileSync(resolve(projectRoot, entry.path))
  if (sourceData.length !== data.length || sha256(sourceData) !== entry.sha256 || !sourceData.equals(data)) {
    throw new Error(`Packaged file differs from the current source tree: ${entry.path}`)
  }
}
for (const name of entries.keys()) {
  if (name !== manifestName && !expectedNames.has(name)) throw new Error(`Unmanifested ZIP entry: ${name}`)
}

const validationRoot = mkdtempSync(join(tmpdir(), 'toyota-release-check-'))
try {
  let phpCount = 0
  for (const [name, data] of entries) {
    if (!name.endsWith('.php')) continue
    const relativeName = name.slice(`${themeSlug}/`.length)
    const outputPath = resolve(validationRoot, relativeName)
    const relativeOutput = relative(validationRoot, outputPath)
    if (relativeOutput.startsWith('..') || isAbsolute(relativeOutput)) throw new Error(`Unsafe extraction target: ${name}`)
    mkdirSync(dirname(outputPath), { recursive: true })
    writeFileSync(outputPath, data)
    const lint = spawnSync('php', ['-l', outputPath], { encoding: 'utf8' })
    if (lint.status !== 0) throw new Error(`Packaged PHP lint failed: ${relativeName}\n${lint.stderr || lint.stdout}`)
    phpCount += 1
  }
  console.log(`Release verified: ${basename(zipPath)}, ${entries.size} files, ${phpCount} PHP files, SHA-256 ${actualChecksum}.`)
} finally {
  const resolvedTemp = resolve(tmpdir())
  const relativeTemp = relative(resolvedTemp, validationRoot)
  if (relativeTemp.startsWith('..') || isAbsolute(relativeTemp) || !basename(validationRoot).startsWith('toyota-release-check-')) {
    throw new Error(`Unsafe validation cleanup target: ${validationRoot}`)
  }
  rmSync(validationRoot, { recursive: true, force: true })
}
