import { readdirSync, statSync } from 'node:fs'
import { dirname, extname, join, relative, resolve } from 'node:path'
import { spawnSync } from 'node:child_process'
import { fileURLToPath } from 'node:url'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')

function collectPhp(path, recursive) {
  const absolutePath = resolve(projectRoot, path)
  const stats = statSync(absolutePath)

  if (stats.isFile()) {
    return extname(absolutePath).toLowerCase() === '.php' ? [absolutePath] : []
  }

  return readdirSync(absolutePath, { withFileTypes: true }).flatMap((entry) => {
    if (entry.name.startsWith('.') || entry.name === 'node_modules' || entry.name === 'dist' || entry.name === 'release') {
      return []
    }

    const child = join(absolutePath, entry.name)
    if (entry.isDirectory()) {
      return recursive ? collectPhp(child, true) : []
    }

    return collectPhp(child, false)
  })
}

const files = [...new Set([
  ...collectPhp('.', false),
  ...collectPhp('inc', true),
  ...collectPhp('template-parts', true),
])].sort()

const phpVersion = spawnSync('php', ['--version'], { cwd: projectRoot, encoding: 'utf8' })
if (phpVersion.error?.code === 'ENOENT') {
  console.error('PHP is required in PATH to run lint:php.')
  process.exit(1)
}
if (phpVersion.status !== 0) {
  process.stderr.write(phpVersion.stderr || phpVersion.stdout)
  process.exit(phpVersion.status ?? 1)
}

let failed = false
for (const file of files) {
  const result = spawnSync('php', ['-l', file], {
    cwd: projectRoot,
    encoding: 'utf8',
  })

  if (result.status !== 0) {
    failed = true
    process.stderr.write(`\n${relative(projectRoot, file)}\n`)
    process.stderr.write(result.stderr || result.stdout)
  }
}

if (failed) {
  process.exitCode = 1
} else {
  const versionLine = phpVersion.stdout.split(/\r?\n/, 1)[0]
  console.log(`PHP syntax OK (${files.length} files, ${versionLine}).`)
}
