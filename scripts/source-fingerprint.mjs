import { createHash } from 'node:crypto'
import { existsSync, readFileSync, readdirSync, statSync } from 'node:fs'
import { dirname, join, relative, resolve, sep } from 'node:path'
import { fileURLToPath } from 'node:url'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const inputs = [
  'src',
  'inc',
  'template-parts',
  'assets/css/front.css',
  'assets/img/home/yaris-cross-thumb.jpg',
  'assets/js',
  'js',
  'package.json',
  'package-lock.json',
  'postcss.config.js',
  'scripts/build.mjs',
  'scripts/source-fingerprint.mjs',
  'tailwind.config.js',
  'tsconfig.json',
  'vite.config.js',
]

function collect(path) {
  const absolutePath = resolve(projectRoot, path)
  if (!existsSync(absolutePath)) throw new Error(`Fingerprint input is missing: ${path}`)
  if (statSync(absolutePath).isFile()) return [absolutePath]

  return readdirSync(absolutePath, { withFileTypes: true }).flatMap((entry) => {
    const child = join(absolutePath, entry.name)
    return entry.isDirectory() ? collect(child) : [child]
  })
}

export function getBundleSourceFingerprint() {
  const hash = createHash('sha256')
  const rootPhpFiles = readdirSync(projectRoot, { withFileTypes: true })
    .filter((entry) => entry.isFile() && entry.name.endsWith('.php'))
    .map((entry) => resolve(projectRoot, entry.name))
  const files = [...inputs.flatMap(collect), ...rootPhpFiles]
    .map((file) => ({ file, path: relative(projectRoot, file).split(sep).join('/') }))
    .sort((left, right) => (left.path < right.path ? -1 : left.path > right.path ? 1 : 0))
  for (const { file, path } of files) {
    hash.update(path)
    hash.update('\0')
    hash.update(readFileSync(file))
    hash.update('\0')
  }
  return hash.digest('hex').slice(0, 20)
}
