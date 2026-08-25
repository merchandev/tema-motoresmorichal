<?php
/**
 * Theme Setup and Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}

// ---------------------------------------------
// Theme setup
// ---------------------------------------------
function toyota_monagas_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    // Hero vehiculo size: 1368x764, sin recorte (respeta proporciones)
    add_image_size('veh-hero', 1368, 764, false);
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width'  => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    register_nav_menus(array(
        'menu-principal' => __('Menú principal', 'toyota-monagas'),
        'menu-movil'     => __('Menú Móvil', 'toyota-monagas'),
    ));
}
add_action('after_setup_theme', 'toyota_monagas_setup');

// Fix menu item text typo "Contactanos" -> "Contáctanos"
add_filter('wp_nav_menu_objects', function($items) {
    foreach ($items as $item) {
        if (trim($item->title) === 'Contactanos') {
            $item->title = 'Contáctanos';
        }
    }
    return $items;
});

// ---------------------------------------------
// Custom Walker for Menu with dropdown support
// ---------------------------------------------
class Toyota_Walker_Nav_Menu extends Walker_Nav_Menu {
    
    // Start the list before the elements are added
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    // End the list after the elements are added
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    // Start the element output
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $rel_values = preg_split('/\s+/', trim((string) $item->xfn), -1, PREG_SPLIT_NO_EMPTY);
        if ($item->target === '_blank') {
            $rel_values[] = 'noopener';
            $rel_values[] = 'noreferrer';
        }
        $rel_values = array_values(array_unique($rel_values));
        $atts = array(
            'title'  => !empty($item->attr_title) ? $item->attr_title : '',
            'target' => !empty($item->target) ? $item->target : '',
            'rel'    => !empty($rel_values) ? implode(' ', $rel_values) : '',
            'href'   => !empty($item->url) ? $item->url : '',
        );
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        if (!is_array($atts)) $atts = array();
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!is_scalar($value) || (string) $value === '') continue;
            $attr = sanitize_key($attr);
            if ($attr === '') continue;
            $escaped = $attr === 'href' ? esc_url($value) : esc_attr($value);
            $attributes .= ' ' . $attr . '="' . $escaped . '"';
        }

        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        $item_output .= '</a>';

        $item_output .= isset($args->after) ? $args->after : '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    // End the element output
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

// ---------------------------------------------
// Assets
// ---------------------------------------------
function toyota_monagas_dist_files() {
    return array('style.css', 'swiper.js', 'front.js', 'app.js', 'yaris-cross-thumb.png');
}

/**
 * Stable local placeholder used when editorial content has no image assigned.
 * Keeping this local avoids leaking visitor requests to placeholder services.
 */
function toyota_monagas_placeholder_image_url() {
    return get_theme_file_uri('/assets/img/home/agya-thumb.jpg');
}

/**
 * Canonical WhatsApp destination. A deployment can override it with the
 * mmorichal_whatsapp_number filter without editing templates or scripts.
 */
function toyota_monagas_whatsapp_number() {
    $filtered = apply_filters('mmorichal_whatsapp_number', '584249090679');
    $value = is_scalar($filtered) ? (string) $filtered : '';
    $number = preg_replace('/\D+/', '', $value);

    return is_string($number) && $number !== '' ? $number : '584249090679';
}

function toyota_monagas_whatsapp_url($message = '') {
    $url = 'https://wa.me/' . toyota_monagas_whatsapp_number();
    if (is_scalar($message) && (string) $message !== '') {
        $url .= '?text=' . rawurlencode((string) $message);
    }

    return $url;
}

/**
 * Read a scalar request value without allowing array-shaped input to reach
 * WordPress sanitizers that expect strings.
 */
function toyota_monagas_request_scalar($source, $key, $default = '') {
    if (!is_array($source) || !array_key_exists($key, $source)) {
        return $default;
    }

    $value = wp_unslash($source[$key]);
    return is_scalar($value) ? (string) $value : $default;
}

/**
 * Small interface icons stay inline so critical controls never depend on an
 * icon font. Font Awesome remains available locally for legacy decoration.
 */
function toyota_monagas_icon($name) {
    $icons = array(
        'zoom' => '<svg class="tm-icon" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-4v8m-4-4h8"/></svg>',
        'close' => '<svg class="tm-icon" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>',
    );

    return isset($icons[$name]) ? $icons[$name] : '';
}

function toyota_monagas_dist_is_complete() {
    $dist_dir = get_template_directory() . '/dist';

    foreach (toyota_monagas_dist_files() as $filename) {
        if (!file_exists($dist_dir . '/' . $filename)) {
            return false;
        }
    }

    return true;
}

function toyota_monagas_missing_dist_notice() {
    if (toyota_monagas_dist_is_complete() || !current_user_can('manage_options')) {
        return;
    }

    echo '<div class="notice notice-warning"><p>';
    echo esc_html__('El build frontend de Toyota Monagas está incompleto. El tema usa temporalmente los assets fuente; ejecuta "npm ci && npm run build" antes de publicar el paquete.', 'toyota-monagas');
    echo '</p></div>';
}
add_action('admin_notices', 'toyota_monagas_missing_dist_notice');

function toyota_monagas_needs_front_assets() {
    return is_front_page()
        || is_page_template('contactanos.php') || is_page('contactanos')
        || is_page_template('sobre-nosotros.php') || is_page('sobre-nosotros')
        || is_page_template('page-atencion-al-cliente.php') || is_page('atencion-al-cliente')
        || is_page_template('page-buzon-de-sugerencia.php') || is_page('buzon-de-sugerencia')
        || is_page('vehiculos') || is_page('vehiculos-usados')
        || is_singular('vehiculo') || is_singular('vehiculo_usado') || is_singular('post')
        || is_page_template('blog.php') || is_page('blog') || is_home() || is_archive() || is_search();
}

function toyota_monagas_scripts() {
    $theme_version = wp_get_theme()->get('Version');
    $style_dependencies = array();
    $fontawesome_path = get_template_directory() . '/assets/fontawesome/css/all.min.css';

    // Keep icons self-hosted. Typography already has a system-font fallback in style.css.
    if (file_exists($fontawesome_path)) {
        wp_enqueue_style(
            'font-awesome',
            get_template_directory_uri() . '/assets/fontawesome/css/all.min.css',
            array(),
            (string) filemtime($fontawesome_path)
        );
        $style_dependencies[] = 'font-awesome';
    }

    wp_enqueue_style('toyota-style', get_stylesheet_uri(), $style_dependencies, $theme_version);

    $navigation_path = get_template_directory() . '/assets/js/navigation.js';
    if (file_exists($navigation_path)) {
        wp_enqueue_script(
            'toyota-navigation',
            get_template_directory_uri() . '/assets/js/navigation.js',
            array(),
            (string) filemtime($navigation_path),
            true
        );
    }

    if (toyota_monagas_needs_front_assets()) {
        $dist_dir = get_template_directory() . '/dist';
        $dist_uri = get_template_directory_uri() . '/dist';

        if (toyota_monagas_dist_is_complete()) {
            wp_enqueue_style(
                'toyota-front',
                $dist_uri . '/style.css',
                array('toyota-style'),
                (string) filemtime($dist_dir . '/style.css')
            );
            wp_enqueue_script(
                'toyota-swiper',
                $dist_uri . '/swiper.js',
                array(),
                (string) filemtime($dist_dir . '/swiper.js'),
                true
            );
            wp_enqueue_script(
                'toyota-front',
                $dist_uri . '/front.js',
                array('toyota-swiper'),
                (string) filemtime($dist_dir . '/front.js'),
                true
            );
            wp_enqueue_script(
                'toyota-app',
                $dist_uri . '/app.js',
                array('toyota-front'),
                (string) filemtime($dist_dir . '/app.js'),
                true
            );
        } else {
            // Development-only safety path. Production packages require a complete dist/.
            wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@14.1.0/swiper-bundle.min.css', array(), '14.1.0');
            $front_css_path = get_template_directory() . '/assets/css/front.css';
            $front_js_path  = get_template_directory() . '/assets/js/front.js';

            if (file_exists($front_css_path)) {
                wp_enqueue_style(
                    'toyota-front',
                    get_template_directory_uri() . '/assets/css/front.css',
                    array('toyota-style', 'swiper'),
                    (string) filemtime($front_css_path)
                );
            }
            if (file_exists($front_js_path)) {
                wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@14.1.0/swiper-bundle.min.js', array(), '14.1.0', true);
                wp_enqueue_script(
                    'toyota-front',
                    get_template_directory_uri() . '/assets/js/front.js',
                    array('swiper'),
                    (string) filemtime($front_js_path),
                    true
                );
            }
        }

        if (wp_script_is('toyota-front', 'enqueued')) {
            wp_localize_script('toyota-front', 'toyota_front_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('toyota_front_nonce'),
                'empty_inventory_url' => toyota_monagas_whatsapp_url(
                    'Hola, quisiera consultar cuándo llegan nuevos vehículos.'
                ),
            ));
        }
    }

    // The design-system layer is intentionally last so it can normalize both
    // the legacy stylesheet and the compiled/fallback component stylesheet.
    $design_system_path = get_template_directory() . '/assets/css/design-system.css';
    if (file_exists($design_system_path)) {
        $design_system_dependencies = array('toyota-style');
        if (wp_style_is('toyota-front', 'enqueued')) {
            $design_system_dependencies[] = 'toyota-front';
        }
        wp_enqueue_style(
            'toyota-design-system',
            get_template_directory_uri() . '/assets/css/design-system.css',
            $design_system_dependencies,
            (string) filemtime($design_system_path)
        );
    }

    // Contact System Script (Load only when needed)
    $current_post = get_post();
    $has_contact_shortcode = $current_post instanceof WP_Post
        && has_shortcode((string) $current_post->post_content, 'mm_contact_form');

    if (is_page_template('contactanos.php') || is_page('contactanos') || is_front_page() || $has_contact_shortcode) {
        $contact_script_path = get_template_directory() . '/js/contact-form.js';
        $contact_script_version = file_exists($contact_script_path)
            ? (string) filemtime($contact_script_path)
            : $theme_version;

        wp_enqueue_script('mm-contact-js', get_template_directory_uri() . '/js/contact-form.js', array(), $contact_script_version, true);
        wp_localize_script('mm-contact-js', 'mm_ajax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('mm_contact_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'toyota_monagas_scripts');

// ---------------------------------------------
// Ensure templates by slug
// ---------------------------------------------
add_filter('template_include', function($template){
    // Force front-page.php for the homepage
    if (is_front_page()) {
        $t = locate_template('front-page.php');
        if ($t) return $t;
    }

    if (is_page('vehiculos-usados')) {
        $t = locate_template('vehiculos-usados.php');
        if ($t) return $t;
    }
    if (is_page('vehiculos')) {
        $t = locate_template('vehiculos.php');
        if ($t) return $t;
    }
    if (is_page('sobre-nosotros')) {
        $t = locate_template('sobre-nosotros.php');
        if ($t) return $t;
    }
    if (is_page('contactanos')) {
        $t = locate_template('contactanos.php');
        if ($t) return $t;
    }
    if (is_page('blog') || is_home()) {
        $t = locate_template('blog-archive.php');
        if ($t) return $t;
    }
    return $template;
}, 20);
