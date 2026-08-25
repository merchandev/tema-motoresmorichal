# Tema Toyota Monagas

Tema personalizado de WordPress para Motores Morichal, C.A. La versión actual es **1.2.0**. El núcleo compilado del frontend vive en `dist/`; navegación global y sistema de diseño se mantienen como capas runtime explícitas y verificadas.

## Requisitos

### Producción

- WordPress 6.0 o superior.
- PHP 7.4 o superior.
- El ZIP generado por `npm run package`; no se debe comprimir toda la carpeta de trabajo.

### Desarrollo

- Node.js 20.19.x o Node.js 22.12 o superior; `.nvmrc` fija la referencia reproducible.
- npm 9 o superior.
- PHP disponible en `PATH` para ejecutar el lint de las plantillas.

## Inicio rápido

```bash
npm ci
npm run qa
npm run dev
```

Comandos disponibles:

- `npm run typecheck`: comprueba TypeScript sin emitir archivos.
- `npm run lint`: valida JavaScript, PHP, versiones y TypeScript.
- `npm run audit:dependencies`: consulta vulnerabilidades conocidas en dependencias de producción y desarrollo.
- `npm run build`: genera `dist/` y comprueba paridad funcional y huella de fuentes.
- `npm run qa`: ejecuta lint y un build completo.
- `npm run package`: ejecuta todo el QA, crea un ZIP reproducible y valida checksum, manifiesto, estructura y PHP empaquetado.

`dist/`, `release/` y `node_modules/` son generados y no se versionan. El lockfile sí debe permanecer versionado y las instalaciones reproducibles deben usar `npm ci`.

## Arquitectura de assets

Vite produce cinco artefactos con nombres estables:

- `dist/style.css`: Swiper CSS, los estilos frontend comprobados y Tailwind.
- `dist/swiper.js`: Swiper empaquetado y expuesto como global para el runtime existente.
- `dist/front.js`: runtime funcional actual, compilado desde la entrada `src/js/front.js`.
- `dist/app.js`: módulos TypeScript adicionales, actualmente el selector de color.
- `dist/yaris-cross-thumb.png`: medio local importado por la hoja compilada.

WordPress usa `dist/` solo cuando los cinco artefactos están presentes. Si falta alguno, usa el fallback fuente para no dejar el sitio inutilizable y muestra una advertencia visible a administradores. El paquete de producción exige un `dist/` completo, de modo que el fallback no forma parte del flujo normal de despliegue.

Dos assets pequeños se sirven directamente y también forman parte de la allowlist de release:

- `assets/js/navigation.js`: menú, altura de cabecera y estado de scroll en todas las plantillas.
- `assets/css/design-system.css`: tokens, componentes compartidos, foco y capa visual final.

Ambos pasan las comprobaciones de sintaxis de `npm run qa`; `design-system.css` se carga después del CSS compilado para mantener una cascada predecible.

Font Awesome se sirve desde `assets/fontawesome/`; no depende de un CDN. La tipografía usa la pila de sistema definida en `style.css`, por lo que una fuente remota nunca bloquea la interfaz.

Al activar el tema se registran tipos de contenido, capacidades administrativas y reglas de enlaces permanentes. La activación no crea, publica, repone ni sobrescribe contenido editorial.

## Instalación de una release

1. Descarga el ZIP producido por CI o por `npm run package`.
2. Verifica el archivo `.sha256` adjunto.
3. En WordPress, instala o sustituye el tema con ese ZIP.
4. Activa el tema y ejecuta los smoke tests descritos en [docs/QA.md](docs/QA.md).

No se deben incluir `.git`, archivos del IDE, fuentes de desarrollo, backups, otros ZIP ni herramientas locales en el paquete.

## Documentación

- [DOCUMENTACION.md](DOCUMENTACION.md): arquitectura y mantenimiento.
- [docs/QA.md](docs/QA.md): matriz de pruebas y criterios de aceptación.
- [docs/RELEASE.md](docs/RELEASE.md): backups, empaquetado, despliegue y rollback.

Desarrollado por [Merchan.Dev](https://github.com/merchandev) y Espressivo Venezuela.
