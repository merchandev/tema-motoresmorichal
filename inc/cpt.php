<?php
/**
 * Custom Post Types and Taxonomies
 */

if (!defined('ABSPATH')) {
    exit;
}

// ---------------------------------------------
// Vehicles CPT + Taxonomy (clean labels)
// ---------------------------------------------
function toyota_register_content_types() {
    static $registered = false;
    if ($registered) return;
    $registered = true;

    // CPT Vehículos nuevos
    $labels = array(
        'name'               => 'Vehículos',
        'singular_name'      => 'Vehículo',
        'menu_name'          => 'Vehículos',
        'add_new'            => 'Cargar nuevo',
        'add_new_item'       => 'Cargar nuevo Vehículo',
        'edit_item'          => 'Editar Vehículo',
        'new_item'           => 'Nuevo Vehículo',
        'view_item'          => 'Ver Vehículo',
        'search_items'       => 'Buscar Vehículos',
        'not_found'          => 'No se encontraron Vehículos',
        'not_found_in_trash' => 'No hay Vehículos en la papelera',
    );
    register_post_type('vehiculo', array(
        'labels' => $labels,
        'public' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'delete_with_user' => false,
        'menu_position' => 22,
        'menu_icon' => 'dashicons-car',
        'supports' => array('title','editor','thumbnail'),
        'has_archive' => false,
        'rewrite' => array('slug' => 'vehiculo'),
        'show_in_rest' => true,
    ));

    // CPT Vehículos usados
    $labels_usados = array(
        'name'               => 'Vehículos Usados',
        'singular_name'      => 'Vehículo Usado',
        'menu_name'          => 'Vehículos Usados',
        'add_new'            => 'Cargar nuevo',
        'add_new_item'       => 'Cargar nuevo Vehículo usado',
        'edit_item'          => 'Editar Vehículo usado',
        'new_item'           => 'Nuevo Vehículo usado',
        'view_item'          => 'Ver Vehículo usado',
        'search_items'       => 'Buscar Vehículos usados',
        'not_found'          => 'No se encontraron Vehículos usados',
        'not_found_in_trash' => 'No hay Vehículos usados en la papelera',
    );
    register_post_type('vehiculo_usado', array(
        'labels' => $labels_usados,
        'public' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'delete_with_user' => false,
        'menu_position' => 23,
        'menu_icon' => 'dashicons-car',
        'supports' => array('title','editor','thumbnail'),
        'has_archive' => false,
        'rewrite' => array('slug' => 'vehiculo-usado'),
        'show_in_rest' => true,
    ));

    // CPT Slides Home
    $labels_slide = array(
        'name'               => 'Slides Home',
        'singular_name'      => 'Slide',
        'menu_name'          => 'Slides Home',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo Slide',
        'edit_item'          => 'Editar Slide',
        'new_item'           => 'Nuevo Slide',
        'view_item'          => 'Ver Slide',
        'search_items'       => 'Buscar Slides',
        'not_found'          => 'No se encontraron Slides',
        'not_found_in_trash' => 'No hay Slides en la papelera',
    );
    register_post_type('slide', array(
        'labels' => $labels_slide,
        'public' => false,
        'show_ui' => true,
        'capability_type' => array('toyota_slide', 'toyota_slides'),
        'map_meta_cap' => true,
        'menu_position' => 24,
        'menu_icon' => 'dashicons-images-alt2',
        'supports' => array('title', 'page-attributes'), // title for heading, page-attributes for menu_order
        'has_archive' => false,
        'show_in_rest' => false,
    ));

    register_taxonomy('vehiculo_categoria', array('vehiculo', 'vehiculo_usado'), array(
        'label'        => 'Categorías de vehículo',
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array('slug' => 'categoria-vehiculo'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'toyota_register_content_types');

function toyota_flush_rewrite_rules_on_activation() {
    toyota_register_content_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'toyota_flush_rewrite_rules_on_activation', 20);

/**
 * Grant the complete primitive capability set required by the slide CPT.
 * The versioned admin migration prevents existing installations from losing
 * access when moving away from the built-in post capabilities.
 */
function toyota_install_slide_capabilities() {
    $version = '2026-08-24.1';
    if (get_option('toyota_slide_caps_version') === $version) return;

    $role = get_role('administrator');
    if (!$role) return;

    $caps = array(
        'edit_toyota_slides',
        'edit_others_toyota_slides',
        'edit_private_toyota_slides',
        'edit_published_toyota_slides',
        'publish_toyota_slides',
        'read_private_toyota_slides',
        'delete_toyota_slides',
        'delete_others_toyota_slides',
        'delete_private_toyota_slides',
        'delete_published_toyota_slides',
    );
    foreach ($caps as $cap) {
        $role->add_cap($cap);
    }

    update_option('toyota_slide_caps_version', $version, false);
}
add_action('after_switch_theme', 'toyota_install_slide_capabilities');
add_action('admin_init', 'toyota_install_slide_capabilities');

// ---------------------------------------------
// Explicit admin helper for manually reimporting missing featured images.
// Theme activation never creates, republishes, or overwrites site content.
// ---------------------------------------------
if (!function_exists('toyota_ss_set_featured')) {
  /**
   * Sideload an image and assign the resulting attachment as featured image.
   */
  function toyota_ss_set_featured($img_url, $post_id) {
    $img_url = esc_url_raw($img_url);
    $post_id = absint($post_id);
    if (!$img_url || !$post_id || !current_user_can('upload_files')) return false;

    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = media_sideload_image($img_url, $post_id, null, 'src');
    if (is_wp_error($tmp) || !$tmp) return false;

    $attachments = get_children(array(
      'post_parent'    => $post_id,
      'post_type'      => 'attachment',
      'post_mime_type' => 'image',
      'orderby'        => 'ID',
      'order'          => 'DESC',
      'numberposts'    => 1,
    ));
    if (empty($attachments)) return false;

    $att = reset($attachments);
    set_post_thumbnail($post_id, $att->ID);
    return $att->ID;
  }
}


// ---------------------------------------------
// Drag and Drop Slide Ordering
// ---------------------------------------------
function toyota_slide_ordering_view_is_complete() {
    $search = sanitize_text_field(toyota_monagas_request_scalar($_GET, 's'));
    $status = sanitize_key(toyota_monagas_request_scalar($_GET, 'post_status'));
    $month = absint(toyota_monagas_request_scalar($_GET, 'm'));
    $author = absint(toyota_monagas_request_scalar($_GET, 'author'));
    $paged = absint(toyota_monagas_request_scalar($_GET, 'paged'));
    $orderby = sanitize_key(toyota_monagas_request_scalar($_GET, 'orderby'));

    return $search === ''
        && ($status === '' || $status === 'all')
        && $month === 0
        && $author === 0
        && $paged <= 1
        && ($orderby === '' || $orderby === 'menu_order');
}

add_action('admin_enqueue_scripts', function($hook) {
    global $post_type;
    if ($hook === 'edit.php' && $post_type === 'slide' && toyota_slide_ordering_view_is_complete()) {
        wp_enqueue_script('jquery-ui-sortable');
        wp_add_inline_script('jquery-ui-sortable', '
            jQuery(document).ready(function($) {
                var $rows = $("table.wp-list-table tbody");
                $rows.sortable({
                    items: "tr",
                    cursor: "move",
                    axis: "y",
                    update: function(e, ui) {
                        var order = [];
                        $("table.wp-list-table tbody tr").each(function() {
                            var id = $(this).attr("id");
                            if (id) {
                                order.push(id.replace("post-", ""));
                            }
                        });
                        // Visual feedback
                        ui.item.css("background-color", "#f0f0f0");
                        
                        $.post(ajaxurl, {
                            action: "update_slide_order",
                            order: order,
                            nonce: "' . wp_create_nonce('update_slide_order_nonce') . '"
                        }, function(response) {
                            ui.item.animate({"background-color": "transparent"}, 500);
                            if (!response || !response.success) {
                                $rows.sortable("cancel");
                                window.alert(response && response.data && response.data.message ? response.data.message : "No se pudo guardar el orden.");
                            }
                        }).fail(function() {
                            $rows.sortable("cancel");
                            window.alert("No se pudo guardar el orden. Recarga la página e intenta nuevamente.");
                        });
                    }
                });
                // Make the rows visually draggable
                $("table.wp-list-table tbody tr").css("cursor", "move");
            });
        ');
    }
});

add_action('wp_ajax_update_slide_order', function() {
    $nonce = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'nonce'));
    if (!$nonce || !wp_verify_nonce($nonce, 'update_slide_order_nonce')) {
        wp_send_json_error(array('message' => 'Nonce no válido.'), 403);
    }
    if (!current_user_can('edit_toyota_slides')) {
        wp_send_json_error(array('message' => 'No tienes permiso para ordenar slides.'), 403);
    }

    $raw_order = isset($_POST['order']) ? (array) wp_unslash($_POST['order']) : array();
    $order = array_values(array_unique(array_filter(array_map('absint', $raw_order))));
    if (empty($order) || count($order) > 500 || count($order) !== count($raw_order)) {
        wp_send_json_error(array('message' => 'Orden no válido.'), 400);
    }

    $expected_order = get_posts(array(
        'post_type'      => 'slide',
        'post_status'    => array('publish', 'future', 'draft', 'pending', 'private'),
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'menu_order ID',
        'order'          => 'ASC',
    ));
    $expected_order = array_values(array_filter(array_map('absint', $expected_order), function($post_id) {
        return current_user_can('edit_post', $post_id);
    }));

    if (count($expected_order) !== count($order)
        || array_diff($expected_order, $order)
        || array_diff($order, $expected_order)) {
        wp_send_json_error(array(
            'message' => 'La lista cambió o está filtrada. Recarga la vista completa antes de ordenar.',
        ), 409);
    }

    foreach ($order as $menu_order => $post_id) {
        if (get_post_type($post_id) !== 'slide' || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(array('message' => 'El orden contiene un slide no autorizado.'), 403);
        }
    }

    foreach ($order as $menu_order => $post_id) {
        $updated = wp_update_post(array(
            'ID'         => $post_id,
            'menu_order' => $menu_order,
        ), true);
        if (is_wp_error($updated)) {
            wp_send_json_error(array('message' => 'No se pudo guardar el orden.'), 500);
        }
    }

    wp_send_json_success();
});

// Respect menu_order on edit.php for slides
add_action('pre_get_posts', function($query) {
    global $pagenow;
    $requested_type = sanitize_key(toyota_monagas_request_scalar($_GET, 'post_type'));
    if (is_admin() && $query->is_main_query() && $pagenow === 'edit.php' && $requested_type === 'slide') {
        if (!isset($_GET['orderby'])) {
            $query->set('orderby', 'menu_order');
            $query->set('order', 'ASC');
        }
        if (toyota_slide_ordering_view_is_complete()) {
            $query->set('posts_per_page', -1);
        }
    }
});
