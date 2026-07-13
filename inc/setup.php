<?php
/**
 * Theme Setup and Scripts
 */

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

        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

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
function toyota_monagas_scripts() {
    wp_enqueue_style('toyota-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap', array(), null);
    
    // ============================================
    // FONT AWESOME - CDN DIRECTO (SOLUCIÓN SIMPLE)
    // ============================================
    wp_enqueue_style('font-awesome', 
        'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css', 
        array(), 
        '6.5.1'
    );
    
    wp_enqueue_style('toyota-style', get_stylesheet_uri(), array('toyota-google-fonts', 'font-awesome'), wp_get_theme()->get('Version'));

  // Extra assets for front/contact/vehiculos pages when available
  if (
      is_front_page()
      || is_page_template('contactanos.php') || is_page('contactanos')
      || is_page('vehiculos') || is_page('vehiculos-usados')
      || is_singular('vehiculo') || is_singular('vehiculo_usado')
      || is_page_template('blog.php') || is_page('blog') || is_home() || is_archive()
    ) {
        $dist_dir = get_template_directory() . '/dist';
        $has_dist_css = file_exists($dist_dir . '/style.css');
        $has_dist_js  = file_exists($dist_dir . '/app.js');

        if ($has_dist_css && $has_dist_js) {
          // Always provide Swiper globally (app.ts uses global constructor)
          wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11');
          wp_enqueue_style('toyota-front', get_template_directory_uri() . '/dist/style.css', array('toyota-style','swiper'), filemtime($dist_dir . '/style.css'));
          wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true);
          wp_enqueue_script('toyota-front', get_template_directory_uri() . '/dist/app.js', array('swiper'), filemtime($dist_dir . '/app.js'), true);
        } else {
            wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11');
            $front_css_path = get_template_directory() . '/assets/css/front.css';
            $front_js_path  = get_template_directory() . '/assets/js/front.js';
            $front_css_ver  = file_exists($front_css_path) ? filemtime($front_css_path) : '1.2';
            $front_js_ver   = file_exists($front_js_path) ? filemtime($front_js_path) : '1.2';
            if (file_exists($front_css_path)) {
                wp_enqueue_style('toyota-front', get_template_directory_uri() . '/assets/css/front.css', array('toyota-style'), $front_css_ver);
            }
            if (file_exists($front_js_path)) {
              // Ensure Swiper is present globally before front.js
              wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true);
              wp_enqueue_script('toyota-front', get_template_directory_uri() . '/assets/js/front.js', array('swiper'), $front_js_ver, true);
                // expose ajax URL and nonce for front.js
                wp_localize_script('toyota-front', 'toyota_front_ajax', array(
                  'ajax_url' => admin_url('admin-ajax.php'),
                  'nonce'    => wp_create_nonce('toyota_front_nonce'),
                ));
            }
        }
    }

    // Contact System Script
    wp_enqueue_script('mm-contact-js', get_template_directory_uri() . '/js/contact-form.js', array(), '1.0', true);
    wp_localize_script('mm-contact-js', 'mm_ajax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'toyota_monagas_scripts');

// Title
add_filter('pre_get_document_title', function($title){
    return 'Motores Morichal - Concesionario Toyota en Maturín';
}, 20);

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
