add_action('wp_footer', function() {
    // Solo ejecutar una vez
    if (get_option('fontawesome_auto_downloaded')) {
        return;
    }
    
    $theme_dir = get_template_directory();
    $fontawesome_dir = $theme_dir . '/assets/fontawesome';
    $css_dir = $fontawesome_dir . '/css';
    $webfonts_dir = $fontawesome_dir . '/webfonts';
    
    // Verificar si ya existe
    if (file_exists($css_dir . '/all.min.css')) {
        update_option('fontawesome_auto_downloaded', true);
        return;
    }
    
    // Crear directorios
    if (!file_exists($css_dir)) {
        mkdir($css_dir, 0755, true);
    }
    if (!file_exists($webfonts_dir)) {
        mkdir($webfonts_dir, 0755, true);
    }
    
    // Descargar en segundo plano
    $css_url = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css';
    $css_content = @file_get_contents($css_url);
    
    if ($css_content) {
        file_put_contents($css_dir . '/all.min.css', $css_content);
        
        // Descargar fuentes
        $fonts = [
            'fa-solid-900.woff2',
            'fa-solid-900.ttf',
            'fa-regular-400.woff2',
            'fa-regular-400.ttf',
            'fa-brands-400.woff2',
            'fa-brands-400.ttf'
        ];
        
        foreach ($fonts as $font) {
            $font_url = "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/webfonts/" . $font;
            $font_content = @file_get_contents($font_url);
            
            if ($font_content) {
                file_put_contents($webfonts_dir . '/' . $font, $font_content);
            }
        }
        
        // Marcar como descargado
        update_option('fontawesome_auto_downloaded', true);
        
        // Opcional: mostrar notificación de admin
        if (current_user_can('administrator')) {
            echo '<script>console.log("✅ Font Awesome descargado localmente");</script>';
        }
    }
});
