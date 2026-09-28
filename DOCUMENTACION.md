# Documentación técnica: Toyota Monagas

**Versión:** 1.2.0

**Autor:** Merchan.Dev & Espressivo Venezuela

**Tipo:** Tema personalizado de WordPress

## Objetivo y alcance

El proyecto combina plantillas PHP de WordPress con un frontend compilado por Vite, Tailwind CSS y TypeScript. El artefacto de producción es un ZIP generado desde una allowlist; el directorio de trabajo completo no es desplegable.

## Compatibilidad declarada

- PHP 7.4 o superior.
- WordPress 6.0 o superior.
- Node.js 20.19.x o Node.js 22.12 o superior para desarrollo y build; `.nvmrc` fija la referencia reproducible.
- npm 9 o superior.

CI comprueba sintaxis en PHP 7.4, 8.1, 8.3 y 8.4. La instalación/activación y la compatibilidad funcional con WordPress se validan manualmente en staging con la matriz de `docs/QA.md` antes de cualquier despliegue.

## Instalación de desarrollo

```bash
npm ci
npm run qa
npm run dev
```

`npm ci` es obligatorio en CI y para reproducir una release. Si cambia `package.json`, se debe actualizar y revisar también `package-lock.json`.

## Frontend de producción

El núcleo compilado que consume el navegador se genera en `dist/`:

| Artefacto | Fuente | Responsabilidad |
| --- | --- | --- |
| `dist/style.css` | `src/css/input.css` | Swiper CSS, estilos frontend existentes y Tailwind. |
| `dist/swiper.js` | `src/ts/swiper.ts` | Swiper empaquetado y puente global para el runtime heredado. |
| `dist/front.js` | `src/js/front.js` | Slider, catálogos, carga AJAX, blog y galería. |
| `dist/app.js` | `src/ts/app.ts` | Funcionalidad TypeScript adicional. |
| `dist/yaris-cross-thumb.jpg` | `assets/img/home/yaris-cross-thumb.jpg` | Medio local referenciado por el CSS compilado. |

`src/css/input.css` y `src/js/front.js` importan temporalmente los assets funcionales existentes. Esto preserva paridad durante la migración sin servir directamente esos archivos en producción.

Hay dos capas runtime intencionalmente externas a Vite: `assets/js/navigation.js`, cargada en todas las páginas, y `assets/css/design-system.css`, cargada al final de la cascada. Ambas están en la allowlist, se validan durante `npm run qa` y no deben editarse dentro de `dist/`.

`inc/setup.php` exige que estén presentes los cinco artefactos antes de activar `dist/`. Si el conjunto está incompleto, el entorno de desarrollo carga los assets fuente completos y muestra una advertencia administrativa. `npm run package` no permite publicar ese estado.

### Orden de carga

1. `style.css` (y Font Awesome local, no bloqueante, solo si el contenido usa sus clases).
2. `dist/style.css`.
3. `assets/css/design-system.css`, como última capa CSS.
4. `assets/js/navigation.js`, disponible globalmente.
5. `dist/swiper.js`.
6. Datos AJAX localizados antes de `dist/front.js`.
7. `dist/front.js`.
8. `dist/app.js`.

No se debe añadir de nuevo Swiper por CDN ni inicializar sus sliders desde `app.ts`; el runtime frontend actual es quien administra esas instancias.

## Configuración

- `vite.config.js`: entradas, nombres deterministas y salida `dist/`.
- `tailwind.config.js`: rutas PHP/JS/TS analizadas para generar utilidades.
- `postcss.config.js`: Tailwind y Autoprefixer en formato ESM.
- `tsconfig.json`: typecheck estricto sin emisión.
- `release.config.mjs`: allowlist explícita del ZIP de producción.
- `package.json`: comandos y versiones exactas de herramientas.

### Slider principal y layout

- `toyota_monagas_get_home_slides()` centraliza los slides; `front-page.php` y la precarga en `<head>` usan los mismos datos.
- El primer video se declara con `<source>` y `autoplay`, así el navegador lo descarga mientras analiza el HTML. Los siguientes quedan en `preload="none"` y se precargan cuando el actual ya puede reproducirse completo (salvo con ahorro de datos o 2G).
- Cada slide de video admite un video móvil opcional (`slide_video_mobile`, ≤ 768 px) y una imagen de portada (`slide_video_poster`), que se muestra al instante y se precarga para el primer slide.
- El video se pausa cuando el slider sale de la pantalla.
- `assets/css/design-system.css` define el layout único: el contenido ocupa el 80% del ancho con 10% de margen a cada lado en todos los dispositivos (solo el slider es a pantalla completa) y una sola escala tipográfica para kicker, títulos y párrafos de sección.

### Contacto, privacidad y SMTP

- El destino de WhatsApp se centraliza en `toyota_monagas_whatsapp_number()` y puede cambiarse con el filtro `mmorichal_whatsapp_number`.
- Los formularios conectados al sistema de leads exigen consentimiento, registran su fecha UTC y se integran con los exportadores/borradores de datos personales de WordPress.
- Los leads permanecen privados hasta que un administrador los elimina o se atiende una solicitud verificada de borrado. El buzón de sugerencias se envía por correo y no crea un lead; el formulario de atención abre WhatsApp directamente.
- WordPress ofrece un texto sugerido que debe revisarse, publicarse y asignarse como política de privacidad antes del despliegue.
- La contraseña SMTP puede definirse como constante PHP o variable de entorno `MM_SMTP_PASSWORD`; ese valor tiene prioridad y no se copia a `wp_options`.
- Cuando se activa ese override, la pantalla de configuración ofrece una acción explícita para eliminar una contraseña heredada de `wp_options` después de probar el correo.
- El rate limit usa un contador atómico por ventana e IP. Detrás de un proxy, el filtro `mm_contact_rate_limit_identifier` solo debe confiar en cabeceras que el proxy elimine y reconstruya.

`style.css` es la versión canónica del tema. `package.json` y esta documentación deben coincidir con su cabecera; `npm run lint:versions` impide publicar una divergencia.

## Estructura relevante

- `/inc`: configuración, CPT, metaboxes, AJAX, contacto y herramientas administrativas.
- `/template-parts`: fragmentos reutilizables de plantillas.
- `/assets`: medios locales y fuentes de compatibilidad que alimentan el build.
- `/src`: entradas canónicas de Vite y módulos TypeScript.
- `/dist`: salida generada y validada; no se versiona.
- `/scripts`: validación, empaquetado y controles de release.
- `/docs`: matriz QA y runbook de release.

## Comandos de calidad

```bash
npm run typecheck
npm run lint:css
npm run lint:js
npm run lint:php
npm run lint:versions
npm run lint
npm run build
npm run qa
npm run package
```

`npm run build` incorpora una huella SHA-256 de las fuentes y falla si `dist/` no corresponde al estado actual, además de comprobar marcadores funcionales en CSS y JS. Esta verificación protege contra una salida sintácticamente válida pero incompleta o desactualizada.

## Assets externos y portabilidad

- Los iconos de interfaz son SVG en línea generados por `toyota_monagas_icon()`, visibles desde el primer render.
- Font Awesome se sirve desde `assets/fontawesome` con versionado por `filemtime`, `font-display: swap` y carga no bloqueante; solo se encola cuando el contenido usa sus clases o el filtro `toyota_monagas_needs_fontawesome` devuelve `true`.
- La interfaz conserva fallbacks de tipografía del sistema; Google Fonts no es requisito de ejecución.
- Las URLs de medios de producción que aún estén codificadas deben inventariarse y migrarse de forma gradual a Media Library, opciones del tema o assets locales.
- Los endpoints AJAX siempre se inyectan con `admin_url()`; no deben construirse desde rutas raíz fijas.

La activación del tema registra tipos de contenido, instala capacidades de administrador y refresca reglas de enlaces permanentes. Nunca crea, republica ni sobrescribe páginas, vehículos o slides. La reimportación de imágenes es una acción administrativa manual, protegida por capacidad y nonce.

## Release y despliegue

```bash
npm ci
npm run qa
npm run package
```

El último comando crea:

- `release/toyota-monagas-1.2.0.zip`
- `release/toyota-monagas-1.2.0.zip.sha256`

El ZIP tiene un directorio raíz `toyota-monagas/`, un `RELEASE-MANIFEST.json` con hashes por archivo y timestamps deterministas. El propio comando valida SHA-256, CRC/estructura ZIP, coincidencia exacta del manifiesto, contenido prohibido y sintaxis de todos los PHP extraídos. No incluye `.git`, `node_modules`, `src`, scripts de desarrollo, backups, temporales ni el video local de AGYA que no usa el runtime actual.

Los videos Corolla, Fortuner y Yaris se conservan por compatibilidad con slides existentes que pueden referenciarlos desde la base de datos; retirar uno exige buscar primero sus referencias en la instalación objetivo.

Consulta [docs/RELEASE.md](docs/RELEASE.md) antes de desplegar y [docs/QA.md](docs/QA.md) para los criterios de aceptación.

## Mantenimiento

- Cambiar código fuente, no archivos de `dist/` a mano.
- No editar simultáneamente el runtime heredado y su salida compilada.
- Mantener commits separados para código, artefactos y documentación.
- No guardar credenciales, exports de base de datos ni archivos de usuario en el repositorio.
- Conservar el ZIP y el checksum de la versión anterior hasta cerrar la ventana de observación.
