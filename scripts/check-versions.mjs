import { readFileSync } from 'node:fs'
import { fileURLToPath, pathToFileURL } from 'node:url'
import { dirname, resolve } from 'node:path'

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..')
const packageJson = JSON.parse(readFileSync(resolve(projectRoot, 'package.json'), 'utf8'))
const styleCss = readFileSync(resolve(projectRoot, 'style.css'), 'utf8')
const documentation = readFileSync(resolve(projectRoot, 'DOCUMENTACION.md'), 'utf8')
const readme = readFileSync(resolve(projectRoot, 'README.md'), 'utf8')
const functionsPhp = readFileSync(resolve(projectRoot, 'functions.php'), 'utf8')

const themeVersion = styleCss.match(/^Version:\s*(\S+)\s*$/m)?.[1]
const documentedVersion = documentation.match(/^\*\*Versión:\*\*\s*(\S+)/m)?.[1]
const readmeVersion = readme.match(/La versión actual es \*\*(\S+)\*\*/)?.[1]
const documentedArtifactVersion = documentation.match(/release\/toyota-monagas-(\d+\.\d+\.\d+)\.zip/m)?.[1]
const runtimeVersion = functionsPhp.match(/define\(\s*['"]TOYOTA_MONAGAS_VERSION['"]\s*,\s*['"]([^'"]+)['"]\s*\)/)?.[1]
const errors = []

if (!themeVersion) errors.push('style.css does not declare a Version header.')
if (!documentedVersion) errors.push('DOCUMENTACION.md does not declare **Versión:**.')
if (!readmeVersion) errors.push('README.md does not declare the current version.')
if (!runtimeVersion) errors.push('functions.php does not declare TOYOTA_MONAGAS_VERSION.')
if (packageJson.version !== themeVersion) {
  errors.push(`package.json (${packageJson.version}) does not match style.css (${themeVersion ?? 'missing'}).`)
}
if (documentedVersion !== themeVersion) {
  errors.push(`DOCUMENTACION.md (${documentedVersion ?? 'missing'}) does not match style.css (${themeVersion ?? 'missing'}).`)
}
if (runtimeVersion !== themeVersion) {
  errors.push(`functions.php (${runtimeVersion ?? 'missing'}) does not match style.css (${themeVersion ?? 'missing'}).`)
}
if (readmeVersion !== themeVersion) {
  errors.push(`README.md (${readmeVersion ?? 'missing'}) does not match style.css (${themeVersion ?? 'missing'}).`)
}
if (documentedArtifactVersion && documentedArtifactVersion !== themeVersion) {
  errors.push(`DOCUMENTACION.md release filename (${documentedArtifactVersion}) does not match style.css (${themeVersion ?? 'missing'}).`)
}

for (const [groupName, dependencies] of Object.entries({
  dependencies: packageJson.dependencies,
  devDependencies: packageJson.devDependencies,
})) {
  for (const [name, version] of Object.entries(dependencies ?? {})) {
    if (!/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/.test(version)) {
      errors.push(`${groupName}.${name} must use an exact version; found ${version}.`)
    }
  }
}

for (const filename of ['postcss.config.js', 'tailwind.config.js']) {
  const moduleUrl = `${pathToFileURL(resolve(projectRoot, filename)).href}?qa=${Date.now()}`
  const config = (await import(moduleUrl)).default
  if (!config || typeof config !== 'object') {
    errors.push(`${filename} must provide an ESM default export.`)
  }
}

if (errors.length > 0) {
  for (const error of errors) console.error(`- ${error}`)
  process.exitCode = 1
} else {
  console.log(`Version ${themeVersion} and ESM configuration are consistent.`)
}
