<?php
/**
 * Script para descargar Font Awesome desde CDN y guardarlo localmente
 * Ejecutar una sola vez desde el navegador: /wp-content/themes/tu-tema/download-fontawesome.php
 */

// Directorio base del tema
$theme_dir = get_template_directory();
$fontawesome_dir = $theme_dir . '/assets/fontawesome';
$css_dir = $fontawesome_dir . '/css';
$webfonts_dir = $fontawesome_dir . '/webfonts';

// Crear directorios si no existen
if (!file_exists($css_dir)) {
    mkdir($css_dir, 0755, true);
}
if (!file_exists($webfonts_dir)) {
    mkdir($webfonts_dir, 0755, true);
}

echo "<h1>🚀 Descargando Font Awesome...</h1>";

// URL del CSS de Font Awesome
$css_url = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css';

// Descargar CSS
echo "<p>📥 Descargando CSS...</p>";
$css_content = file_get_contents($css_url);

if ($css_content) {
    file_put_contents($css_dir . '/all.min.css', $css_content);
    echo "<p>✅ CSS descargado: " . strlen($css_content) . " bytes</p>";
    
    // Extraer URLs de fuentes del CSS
    preg_match_all('/url\((\.\.\/webfonts\/[^)]+)\)/', $css_content, $matches);
    
    $fonts_to_download = [
        'fa-solid-900.woff2',
        'fa-solid-900.ttf',
        'fa-regular-400.woff2',
        'fa-regular-400.ttf',
        'fa-brands-400.woff2',
        'fa-brands-400.ttf'
    ];
    
    echo "<h2>📦 Descargando fuentes...</h2>";
    
    foreach ($fonts_to_download as $font_file) {
        $font_url = "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/webfonts/" . $font_file;
        $local_path = $webfonts_dir . '/' . $font_file;
        
        echo "<p>⏳ Descargando: $font_file...</p>";
        
        $font_content = file_get_contents($font_url);
        
        if ($font_content) {
            file_put_contents($local_path, $font_content);
            echo "<p>✅ Guardado: $font_file - " . number_format(strlen($font_content)) . " bytes</p>";
        } else {
            echo "<p>❌ Error descargando: $font_file</p>";
        }
        
        flush();
        ob_flush();
    }
    
    echo "<h2>✅ ¡COMPLETADO!</h2>";
    echo "<p>Font Awesome ha sido descargado y guardado localmente en:</p>";
    echo "<ul>";
    echo "<li><code>$css_dir/all.min.css</code></li>";
    echo "<li><code>$webfonts_dir/</code> (6 archivos de fuentes)</li>";
    echo "</ul>";
    
    echo "<h3>📝 Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>Ve a <code>inc/setup.php</code></li>";
    echo "<li>Cambia la línea del CDN a:<br><code>get_template_directory_uri() . '/assets/fontawesome/css/all.min.css'</code></li>";
    echo "<li>Recarga tu sitio con Ctrl+F5</li>";
    echo "</ol>";
    
    echo "<p><strong>⚠️ IMPORTANTE:</strong> Elimina este archivo (download-fontawesome.php) después de usarlo por seguridad.</p>";
    
} else {
    echo "<p>❌ Error descargando el CSS desde el CDN</p>";
}
?>
