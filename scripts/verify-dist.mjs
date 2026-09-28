import { existsSync, readFileSync, readdirSync, statSync } from 'node:fs'
import { dirname, isAbsolute, relative, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { Script } from 'node:vm'
import { getBundleSourceFingerprint } from './source-fingerprint.mjs'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const distDir = resolve(projectRoot, 'dist')
const requirements = {
  'style.css': ['.cs-progress-bar', '.gallery-lightbox', '.blog-search', '.vp-color-btn'],
  'swiper.js': ['Swiper'],
  'front.js': ['toyota_front_ajax', '#custom-slider', 'gallery-lightbox', 'blog-search'],
  'app.js': ['.vp-color-btn'],
}
const minimumSizes = {
  'style.css': 20_000,
  'swiper.js': 20_000,
  'front.js': 10_000,
  'app.js': 500,
}
const errors = []
const expectedFingerprint = `toyota-source:${getBundleSourceFingerprint()}`
const expectedFiles = new Set([...Object.keys(requirements), 'yaris-cross-thumb.jpg'])

function collectDistFiles(directory, prefix = '') {
  if (!existsSync(directory)) return []
  return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const relativePath = prefix ? `${prefix}/${entry.name}` : entry.name
    return entry.isDirectory()
      ? collectDistFiles(resolve(directory, entry.name), relativePath)
      : [relativePath]
  })
}

for (const path of collectDistFiles(distDir)) {
  if (!expectedFiles.has(path)) errors.push(`Unexpected dist artifact: ${path}`)
}

for (const [filename, markers] of Object.entries(requirements)) {
  const absolutePath = resolve(distDir, filename)
  if (!existsSync(absolutePath)) {
    errors.push(`Missing dist/${filename}.`)
    continue
  }

  const stats = statSync(absolutePath)
  if (stats.size < minimumSizes[filename]) {
    errors.push(`dist/${filename} is unexpectedly small (${stats.size} bytes).`)
  }

  const content = readFileSync(absolutePath, 'utf8')
  if (!content.includes(expectedFingerprint)) {
    errors.push(`dist/${filename} is stale; rebuild it from the current sources.`)
  }
  for (const marker of markers) {
    if (!content.includes(marker)) {
      errors.push(`dist/${filename} is missing parity marker: ${marker}`)
    }
  }

  if (filename.endsWith('.js') && /(^|\n)\s*(?:import|export)\b/m.test(content)) {
    errors.push(`dist/${filename} contains a static ESM import/export but WordPress enqueues it as a classic script.`)
  }
  if (filename.endsWith('.js')) {
    try {
      new Script(content, { filename: `dist/${filename}` })
    } catch (error) {
      errors.push(`dist/${filename} is not valid classic JavaScript: ${error.message}`)
    }
    const executable = content.replace(/^(?:\s|\/\*[\s\S]*?\*\/|\/\/[^\n]*(?:\n|$))*/u, '')
    if (!/^(?:\(\(\)\s*=>\s*\{|\(function(?:\s+[A-Za-z_$][\w$]*)?\s*\(|!function\s*\()/u.test(executable)) {
      errors.push(`dist/${filename} is not wrapped as an isolated IIFE.`)
    }
  }
}

const stylePath = resolve(distDir, 'style.css')
if (existsSync(stylePath)) {
  const styleContent = readFileSync(stylePath, 'utf8')
  for (const match of styleContent.matchAll(/url\(([^)]+)\)/g)) {
    const value = match[1].trim().replace(/^['"]|['"]$/g, '')
    if (!value || /^(?:data:|https?:|#)/i.test(value)) continue
    const resourcePath = resolve(distDir, value.split(/[?#]/, 1)[0])
    const relativePath = relative(distDir, resourcePath)
    if (relativePath.startsWith('..') || isAbsolute(relativePath) || !existsSync(resourcePath)) {
      errors.push(`dist/style.css references a missing or unsafe local resource: ${value}`)
    }
  }
}

const sourceImagePath = resolve(projectRoot, 'assets', 'img', 'home', 'yaris-cross-thumb.jpg')
const distImagePath = resolve(distDir, 'yaris-cross-thumb.jpg')
if (!existsSync(sourceImagePath) || !existsSync(distImagePath)) {
  errors.push('The source or emitted Yaris Cross thumbnail is missing.')
} else if (!readFileSync(sourceImagePath).equals(readFileSync(distImagePath))) {
  errors.push('dist/yaris-cross-thumb.jpg differs from its source asset.')
}

if (errors.length > 0) {
  for (const error of errors) console.error(`- ${error}`)
  process.exitCode = 1
} else {
  console.log(`dist/ is complete, current (${expectedFingerprint}), and contains the expected runtime parity markers.`)
}
