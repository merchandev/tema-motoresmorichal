import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { build } from 'vite'
import { createToyotaViteConfig } from '../vite.config.js'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
process.chdir(projectRoot)
const entries = [
  ['style', 'src/css/input.css'],
  ['swiper', 'src/ts/swiper.ts'],
  ['front', 'src/js/front.js'],
  ['app', 'src/ts/app.ts'],
]

// WordPress loads these files as classic scripts. Building each entry on its
// own lets Rolldown inline helpers instead of emitting cross-entry ESM chunks.
for (const [index, [name, source]] of entries.entries()) {
  await build({
    configFile: false,
    ...createToyotaViteConfig({
      input: { [name]: resolve(projectRoot, source) },
      emptyOutDir: index === 0,
      codeSplitting: false,
      format: name === 'style' ? 'es' : 'iife',
    }),
  })
}
