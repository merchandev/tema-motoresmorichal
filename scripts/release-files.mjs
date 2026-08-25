import { existsSync, lstatSync, readdirSync } from 'node:fs'
import { spawnSync } from 'node:child_process'
import { dirname, isAbsolute, join, relative, resolve, sep } from 'node:path'
import { fileURLToPath } from 'node:url'
import { releaseDirectories, releaseFiles } from '../release.config.mjs'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const bannedSegments = new Set([
  '.git', '.github', '.idea', '.vscode', 'node_modules', 'release', 'scripts', 'src', 'tools',
])
const bannedExtensions = new Set([
  '.bak', '.env', '.key', '.log', '.p12', '.pem', '.pfx', '.sql', '.sqlite', '.sqlite3', '.tmp', '.txt', '.zip',
])

export function normalizeReleasePath(path) {
  return String(path).split(sep).join('/')
}

export function isBannedReleasePath(path) {
  const normalized = normalizeReleasePath(path)
  const segments = normalized.split('/')
  const basename = segments[segments.length - 1].toLowerCase()
  const extension = basename.includes('.') ? `.${basename.split('.').pop()}` : ''

  return segments.some((segment) => bannedSegments.has(segment.toLowerCase()) || segment.startsWith('.'))
    || /^id_(?:rsa|dsa|ecdsa|ed25519)$/.test(basename)
    || basename.startsWith('.env')
    || bannedExtensions.has(extension)
}

function collect(path) {
  if (isBannedReleasePath(path)) throw new Error(`Banned path found in release allowlist: ${path}`)
  const absolutePath = resolve(projectRoot, path)
  const relativePath = relative(projectRoot, absolutePath)
  if (relativePath.startsWith('..') || isAbsolute(relativePath)) {
    throw new Error(`Release allowlist path escapes the project root: ${path}`)
  }
  if (!existsSync(absolutePath)) throw new Error(`Allowlisted path does not exist: ${path}`)

  const stats = lstatSync(absolutePath)
  if (stats.isSymbolicLink()) throw new Error(`Symlinks are not allowed in releases: ${path}`)
  if (stats.isFile()) return [normalizeReleasePath(relativePath)]

  return readdirSync(absolutePath, { withFileTypes: true }).flatMap((entry) => collect(join(path, entry.name)))
}

function runGit(args, description) {
  const result = spawnSync('git', args, {
    cwd: projectRoot,
    encoding: 'utf8',
    maxBuffer: 16 * 1024 * 1024,
  })
  if (result.error || result.status !== 0) {
    throw new Error(`Unable to ${description}.\n${result.error?.message || result.stderr || result.stdout}`)
  }
  return result.stdout
}

function validatePortablePaths(paths) {
  const portablePaths = new Map()
  const reservedWindowsName = /^(?:con|prn|aux|nul|com[1-9]|lpt[1-9])$/i

  for (const path of paths) {
    const normalized = normalizeReleasePath(path)
    const segments = normalized.split('/')
    if (
      normalized.startsWith('/')
      || segments.some((segment) => segment === '' || segment === '.' || segment === '..')
    ) {
      throw new Error(`Release path contains unsafe segments: ${path}`)
    }

    for (const segment of segments) {
      if (
        segment !== segment.normalize('NFC')
        || /[<>:"\\|?*\u0000-\u001f]/u.test(segment)
        || /[. ]$/u.test(segment)
        || reservedWindowsName.test(segment.split('.')[0])
      ) {
        throw new Error(`Release path is not portable across supported filesystems: ${path}`)
      }
    }

    const portableKey = normalized.normalize('NFC').toLowerCase()
    const collision = portablePaths.get(portableKey)
    if (collision && collision !== normalized) {
      throw new Error(`Case-insensitive or Unicode-normalized release path collision: ${collision} / ${normalized}`)
    }
    portablePaths.set(portableKey, normalized)
  }
}

function validateGitState(paths) {
  const status = runGit(['status', '--porcelain=v1', '--untracked-files=all'], 'inspect the Git worktree')
  if (status.trim()) {
    throw new Error('Release packaging requires a clean, committed Git worktree.')
  }

  const tracked = new Set(
    runGit(['ls-files', '-z'], 'list tracked release files')
      .split('\0')
      .filter(Boolean)
      .map(normalizeReleasePath),
  )
  const untrackedReleasePaths = paths.filter((path) => !path.startsWith('dist/') && !tracked.has(path))
  if (untrackedReleasePaths.length > 0) {
    throw new Error(`Release contains non-dist files that are not tracked by Git:\n${untrackedReleasePaths.join('\n')}`)
  }
}

export function getReleasePaths() {
  const paths = [...new Set([
    ...releaseFiles.flatMap(collect),
    ...releaseDirectories.flatMap(collect),
  ])].sort((left, right) => (left < right ? -1 : left > right ? 1 : 0))

  validatePortablePaths(paths)
  validateGitState(paths)
  return paths
}
