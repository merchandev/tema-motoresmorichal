# Plan de QA

## Gates obligatorios

1. `npm ci` funciona desde un clon limpio.
2. `npm run audit:dependencies` no informa vulnerabilidades conocidas.
3. `npm run qa` termina sin errores.
4. `npm run package` produce un ZIP y checksum válidos.
5. El ZIP se instala y activa en una copia limpia de WordPress.
6. El responsable funcional aprueba staging antes de producción.

CI automatiza sintaxis PHP/JS/CSS, TypeScript, versiones, build, checksum e integridad del ZIP. Como este repositorio no incorpora una base de datos ni una instalación WordPress, los puntos 4 y 5 son gates manuales obligatorios y deben conservar evidencia.

## Matriz mínima

| Área | Casos |
| --- | --- |
| Compatibilidad | PHP 7.4 y versiones soportadas actuales; WordPress mínimo declarado y versión objetivo; Node 20.19 para build. |
| Páginas | Inicio, vehículos nuevos/usados, fichas, blog/archivo/búsqueda, contacto, atención, sugerencias y 404. |
| Roles | Visitante, usuario autenticado y administrador. |
| Hero | Imagen/video desktop y móvil, cambio de slide, progreso, pausa y fallback cuando el video falla. |
| Catálogos | Tabs, flechas, responsive, carga incremental y estado vacío/error. |
| Blog | Tarjetas, búsqueda, sugerencias y scroll/carga incremental. |
| Formularios | Validación, consentimiento, nonce válido/inválido, doble envío, error de red, lead guardado y correo. |
| Privacidad | Página de privacidad revisada, publicada y asignada; enlace visible; exportación/borrado de leads por correo; retención de buzón/SMTP y transferencia a WhatsApp documentadas. |
| Vehículo | Selector de color, imagen, CTA de WhatsApp y galería/lightbox con teclado. |
| Assets | Sin 404, contenido mixto ni Swiper duplicado; `toyota_front_ajax` disponible antes de `front.js`. |
| Responsive | 375, 768, 1024 y 1440 px en Chrome/Edge/Firefox y Safari disponible. |
| Accesibilidad | Teclado, foco visible, Escape, etiquetas, alt, contraste y movimiento reducido. |
| Producción | Permalinks, caché, SSL, logs PHP/JS, AJAX y envío de contacto. |

## Evidencia

Cada ejecución debe registrar versión, entorno, navegador, resultado y evidencia. Ningún defecto crítico o alto puede quedar abierto para aprobar la release.

## Smoke test posterior al despliegue

- Cargar inicio sin errores en consola ni red.
- Abrir menú móvil y navegar a catálogos.
- Cambiar tabs/slides y abrir una galería.
- Enviar un formulario de prueba y confirmar recepción/persistencia.
- Probar búsqueda/carga AJAX.
- Revisar logs y recursos 404 durante la ventana de observación.
