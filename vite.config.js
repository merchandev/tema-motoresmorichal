import { defineConfig } from 'vite'
import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { getBundleSourceFingerprint } from './scripts/source-fingerprint.mjs'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)))
const sourceFingerprint = getBundleSourceFingerprint()
const defaultInputs = {
  swiper: resolve(projectRoot, 'src/ts/swiper.ts'),
  front: resolve(projectRoot, 'src/js/front.js'),
  app: resolve(projectRoot, 'src/ts/app.ts'),
  style: resolve(projectRoot, 'src/css/input.css'),
}

function sourceFingerprintPlugin() {
  return {
    name: 'toyota-source-fingerprint',
    generateBundle(_options, bundle) {
      const marker = `/* toyota-source:${sourceFingerprint} */`
      for (const output of Object.values(bundle)) {
        if (output.type === 'chunk') {
          output.code = `${output.code}\n${marker}\n`
        } else if (typeof output.source === 'string' && /\.(?:css|js)$/.test(output.fileName)) {
          output.source = `${output.source}\n${marker}\n`
        }
      }
    },
  }
}

export function createToyotaViteConfig({
  input = defaultInputs,
  emptyOutDir = true,
  codeSplitting = true,
  format = 'es',
} = {}) {
  return {
    root: projectRoot,
    base: './',
    plugins: [sourceFingerprintPlugin()],
    build: {
      target: 'es2018',
      outDir: resolve(projectRoot, 'dist'),
      emptyOutDir,
      sourcemap: false,
      rolldownOptions: {
        input,
        output: {
          codeSplitting,
          format,
          entryFileNames: '[name].js',
          chunkFileNames: 'chunks/[name]-[hash].js',
          assetFileNames: '[name].[ext]',
        },
      },
    },
  }
}

export default defineConfig(({ command }) => {
  if (command === 'build') {
    throw new Error('Direct Vite builds are disabled; use "npm run build" to emit standalone WordPress assets.')
  }
  return createToyotaViteConfig()
})
