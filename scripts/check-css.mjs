import { readFileSync } from 'node:fs'
import { dirname, relative, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import postcss from 'postcss'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const files = [
  'style.css',
  'assets/css/front.css',
  'assets/css/design-system.css',
  'src/css/input.css',
]

let failed = false
for (const filename of files) {
  const absolutePath = resolve(projectRoot, filename)
  try {
    const source = readFileSync(absolutePath, 'utf8')
    postcss.parse(source, { from: absolutePath })
    if (filename === 'assets/css/design-system.css') {
      const definitions = new Set([...source.matchAll(/--([\w-]+)\s*:/g)].map((match) => match[1]))
      const references = new Set([...source.matchAll(/var\(--([\w-]+)/g)].map((match) => match[1]))
      const missing = [...references].filter((name) => !definitions.has(name)).sort()
      if (missing.length > 0) throw new Error(`undefined custom properties: ${missing.join(', ')}`)
    }
  } catch (error) {
    failed = true
    console.error(`${relative(projectRoot, absolutePath)}: ${error.message}`)
  }
}

if (failed) {
  process.exitCode = 1
} else {
  console.log(`CSS syntax OK (${files.length} files).`)
}
