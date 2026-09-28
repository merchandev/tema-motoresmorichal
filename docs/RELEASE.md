# Runbook de release y rollback

## Preparación

1. Confirmar que el árbol rastreado contiene únicamente los cambios aprobados.
2. Crear un tag o referencia recuperable del baseline.
3. Respaldar tema activo, base de datos, uploads, opciones del tema y lista de plugins.
4. Restaurar el backup al menos una vez en staging.
5. Confirmar que todos los cambios aprobados están versionados y que `git status --short` no muestra cambios.
6. Ejecutar `npm ci` y `npm run package`; el empaquetado vuelve a ejecutar todo el QA de forma obligatoria.
7. Revisar `RELEASE-MANIFEST.json`, tamaño del ZIP y checksum.
8. Comparar el tamaño del ZIP con el límite real de subida de WordPress antes de intentar instalarlo desde el panel.

El paquete usa la allowlist de `release.config.mjs`. Exige un checkout Git limpio y solo admite archivos versionados, excepto los artefactos recién generados de `dist/`. Los nuevos archivos de runtime deben añadirse explícitamente y revisarse; nunca se debe reemplazar este proceso por comprimir la raíz del repositorio.

## Reproducibilidad y tamaño del paquete

CI fija `SOURCE_DATE_EPOCH=315532800` (1980-01-01 UTC). Para reproducir exactamente un artefacto se debe usar el mismo commit, `package-lock.json`, Node 20.19.0 (fijado en `.nvmrc`) y ese mismo valor; el manifiesto interno también registra el epoch utilizado. Ejemplos equivalentes:

```bash
SOURCE_DATE_EPOCH=315532800 npm run package
```

```powershell
$env:SOURCE_DATE_EPOCH = '315532800'
npm run package
```

Después se debe comparar el SHA-256 generado, no solo el nombre del archivo. Cualquier diferencia indica que cambiaron las entradas, el entorno o el epoch y exige revisar el manifiesto antes del despliegue.

El ZIP ronda actualmente los 50 MiB porque conserva videos que pueden estar referenciados desde la base de datos. Antes de instalarlo desde WordPress, consultar el límite efectivo del servidor:

```bash
wp eval 'echo size_format(wp_max_upload_size()) . PHP_EOL;'
```

Si el límite es inferior al ZIP, usar el canal de despliegue versionado/atómico aprobado o coordinar el aumento de los límites de PHP y del proxy; no retirar medios de la allowlist sin buscar antes sus referencias en la base de datos.

## Despliegue

1. Instalar el ZIP en staging y ejecutar toda la matriz de `docs/QA.md`.
2. Obtener aprobación funcional y técnica explícita.
3. Generar un backup fresco de producción.
4. Desplegar el mismo ZIP validado, preferiblemente en una carpeta versionada o mediante cambio atómico.
5. Purgar cachés y ejecutar el smoke test.
6. Observar logs, 404, AJAX y formularios durante 30–60 minutos.

Subir commits a GitHub no actualiza el sitio: el servidor solo cambia cuando se instala el ZIP. La carpeta raíz del ZIP es `toyota-monagas/`; debe coincidir con la carpeta del tema activo, y en *Apariencia → Temas → Añadir nuevo → Subir tema* hay que confirmar **Reemplazar el actual con el subido**. Si WordPress lo instala como un tema aparte, el sitio sigue mostrando el anterior. Tras purgar cachés, confirmar el despliegue en *Apariencia → Temas* (la versión debe coincidir con `style.css`) y en el código fuente de la portada (`style.css?ver=<versión>` y `assets/css/design-system.css`).

## Umbrales de rollback

Revertir inmediatamente ante un fatal PHP/pantalla blanca, respuestas 5xx sostenidas, assets críticos 404, navegación principal inutilizable o pérdida de formularios/leads.

## Procedimiento de rollback

1. Restaurar el ZIP anterior del tema.
2. Purgar cachés de WordPress, servidor y CDN.
3. Verificar inicio, login, navegación, AJAX y un formulario.
4. Restaurar la base de datos solo si la release ejecutó una migración de datos.
5. Registrar causa, hora, versión y evidencia antes de reabrir el trabajo.

No eliminar el backup ni la release anterior hasta que la ventana de observación haya finalizado.
