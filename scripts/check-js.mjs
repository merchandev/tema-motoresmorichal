import { readdirSync, statSync } from 'node:fs'
import { dirname, extname, join, relative, resolve } from 'node:path'
import { spawnSync } from 'node:child_process'
import { fileURLToPath } from 'node:url'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const inputs = [
  'vite.config.js',
  'tailwind.config.js',
  'postcss.config.js',
  'release.config.mjs',
  'assets/js',
  'js',
  'src/js',
  'scripts',
]

function collectJavaScript(path) {
  const absolutePath = resolve(projectRoot, path)
  const stats = statSync(absolutePath)

  if (stats.isFile()) {
    return ['.js', '.mjs'].includes(extname(absolutePath)) ? [absolutePath] : []
  }

  return readdirSync(absolutePath, { withFileTypes: true }).flatMap((entry) => {
    const child = join(absolutePath, entry.name)
    return entry.isDirectory() ? collectJavaScript(child) : collectJavaScript(child)
  })
}

const files = inputs.flatMap(collectJavaScript).sort()
let failed = false

for (const file of files) {
  const result = spawnSync(process.execPath, ['--check', file], {
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
  console.log(`JavaScript syntax OK (${files.length} files).`)
}
