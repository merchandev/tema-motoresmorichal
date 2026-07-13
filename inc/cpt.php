<?php
/**
 * Custom Post Types and Taxonomies
 */

// ---------------------------------------------
// Vehicles CPT + Taxonomy (clean labels)
// ---------------------------------------------
add_action('init', function(){
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
        'capability_type' => 'post',
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
});

// ---------------------------------------------
// Default terms + example vehicles + create pages
// ---------------------------------------------
add_action('init', function(){
  // Ensure default categories exist
  $defaults = array(
    'Camioneta' => 'camioneta',
    'Pasajero'  => 'pasajero',
    'Pick Ups'  => 'pick-ups',
    'Comercial' => 'comercial',
  );
  foreach($defaults as $name => $slug){
    if (!term_exists($name, 'vehiculo_categoria') && !term_exists($slug, 'vehiculo_categoria')){
      wp_insert_term($name, 'vehiculo_categoria', array('slug' => $slug));
    }
  }

  // Create vehiculos-usados page if it doesn't exist
  $vehiculos_usados_page = get_page_by_path('vehiculos-usados');
  if (!$vehiculos_usados_page) {
    $page_data = array(
      'post_title'    => 'Vehículos Usados',
      'post_content'  => 'Esta es la página de Vehículos usados de Motores Morichal.',
      'post_status'   => 'publish',
      'post_type'     => 'page',
      'post_name'     => 'vehiculos-usados',
      'post_author'   => 1,
    );
    wp_insert_post($page_data);
  }

  // Auto-create Buzon and Atencion pages
  $new_pages = array(
      'buzon-de-sugerencia' => array('title'=>'Buzón de Sugerencia', 'tpl'=>'page-buzon-de-sugerencia.php'),
      'atencion-al-cliente' => array('title'=>'Atención al Cliente', 'tpl'=>'page-atencion-al-cliente.php'),
  );
  foreach($new_pages as $slug => $info){
      $pg = get_page_by_path($slug);
      $pid = $pg ? $pg->ID : 0;
      if (!$pid){
          $pid = wp_insert_post(array(
              'post_title'   => $info['title'],
              'post_name'    => $slug,
              'post_content' => '', 
              'post_status'  => 'publish',
              'post_type'    => 'page',
              'post_author'  => 1,
          ));
      }
      // Ensure template is set
      if ($pid && !empty($info['tpl'])){
          $curr = get_post_meta($pid, '_wp_page_template', true);
          if ($curr !== $info['tpl']) update_post_meta($pid, '_wp_page_template', $info['tpl']);
      }
  }

  // Helper: sideload image and set as featured if successful
  if (!function_exists('toyota_ss_set_featured')){
    function toyota_ss_set_featured($img_url, $post_id){
      if (empty($img_url) || empty($post_id)) return false;
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      // Attempt to sideload; suppress warnings
      $tmp = media_sideload_image($img_url, $post_id, null, 'src');
      if (is_wp_error($tmp) || !$tmp) return false;
      // Find the most recent attachment for this post
      $attachments = get_children(array(
        'post_parent' => $post_id,
        'post_type'   => 'attachment',
        'post_mime_type' => 'image',
        'orderby'     => 'ID',
        'order'       => 'DESC',
        'numberposts' => 1,
      ));
      if (empty($attachments)) return false;
      $att = reset($attachments);
      set_post_thumbnail($post_id, $att->ID);
      return $att->ID;
    }
  }

  // Example vehicles to ensure exist in CPT
  $examples = array(
    array(
      'post_name' => 'toyota-agya-2025',
      'post_title'=> 'Toyota Agya',
      'content'   => 'Compacto, ágil y diseñado para la ciudad.',
      'year'      => '2025',
      'categoria' => 'Pasajero',
      'image'     => 'https://arturomerchan.com/wp-content/uploads/2025/09/d0c4a468-d12a-49e4-b38b-6b959d16b818.jpeg',
    ),
  );

  foreach($examples as $ex){
    // Skip if a post with that slug exists
    $exists = get_page_by_path($ex['post_name'], OBJECT, 'vehiculo');
    if ($exists) continue;

    $post_data = array(
      'post_title'   => $ex['post_title'],
      'post_name'    => $ex['post_name'],
      'post_content' => $ex['content'],
      'post_status'  => 'publish',
      'post_type'    => 'vehiculo',
    );
    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id) || !$post_id) continue;
    // Set subtitle/year as meta
    update_post_meta($post_id, 'veh_subtitulo', $ex['year'] . ' ' . $ex['post_title']);
    // Assign taxonomy term if exists
    $term = get_term_by('name', $ex['categoria'], 'vehiculo_categoria');
    if ($term) wp_set_object_terms($post_id, intval($term->term_id), 'vehiculo_categoria');
    // Try to sideload image and set featured
    if (!empty($ex['image'])){
      toyota_ss_set_featured($ex['image'], $post_id);
    }
  }

    // Default Slides (Auto-migrate)
    $slides = get_posts(array('post_type' => 'slide', 'posts_per_page' => 1));
    if (empty($slides)) {
        $default_slides = array(
            array(
                'title' => 'Visita nuestra sede',
                'desc' => 'Av. Alirio Ugarte Pelayo, Maturín, Monagas, Venezuela',
                'type' => 'video',
                'video' => 'https://mmorichal.com/wp-content/uploads/2026/03/toyota-monagas-maturin-venezuela-actulizacion-de-edificio.mp4',
                'btn_text' => 'Conócenos',
                'btn_link' => 'https://mmorichal.com/blog/',
                'btn_target' => '0',
                'menu_order' => 0
            ),
            array(
                'title' => 'Bienvenidos a Motores Morichal',
                'desc' => 'Tu concesionario Oficial Toyota en Maturín',
                'type' => 'video',
                'video' => get_template_directory_uri().'/assets/video/home/video-fortuner.mp4',
                'btn_text' => 'Ver vehículos',
                'btn_link' => '#',
                'btn_target' => '0',
                'menu_order' => 1
            ),
            array(
                'title' => 'Toyota APP',
                'desc' => 'Toda la información de tu vehículo al alcance de tu mano.',
                'type' => 'image',
                'img_desk' => get_template_directory_uri().'/assets/img/home/banner-app-desktop.jpg',
                'img_mob' => get_template_directory_uri().'/assets/img/home/banner-app-mobile.png',
                'btn_text' => 'Más información',
                'btn_link' => 'https://www.toyota.com.ve/mi-toyota/app-toyota',
                'btn_target' => '1',
                'menu_order' => 2
            ),
            array(
                'title' => 'Nuevo Yaris Cross',
                'desc' => 'Nuevo diseño moderno y sofisticado',
                'type' => 'video',
                'video' => get_template_directory_uri().'/assets/video/home/video-yaris.mp4',
                'btn_text' => 'Explorar',
                'btn_link' => 'https://mmorichal.com/vehiculo/toyota-yaris-cross-2025/',
                'btn_target' => '0',
                'menu_order' => 3
            ),
            array(
                'title' => 'Un legado de confianza que se mide en décadas',
                'desc' => '',
                'type' => 'video',
                'video' => get_template_directory_uri().'/assets/video/home/video-corolla.mp4',
                'btn_text' => 'Descubrir',
                'btn_link' => 'https://mmorichal.com/sobre-nosotros/',
                'btn_target' => '0',
                'menu_order' => 4
            ),
            array(
                'title' => 'AGYA 2025',
                'desc' => 'El Toyota Agya está diseñado priorizando la ergonomía, ofreciendo un amplio espacio interior y una posición de conducción enfocada en comodidad que te permitirá hacer los viajes que necesites sin sentirte fatigado.',
                'type' => 'video',
                'video' => 'https://mmorichal.com/wp-content/uploads/2025/09/AGYA-TOYOTA-MONAGAS.mp4',
                'btn_text' => 'Conócelo',
                'btn_link' => 'https://mmorichal.com/vehiculo/toyota-agya-2025/',
                'btn_target' => '0',
                'menu_order' => 5
            ),
            array(
                'title' => 'TU VIDA ESTÁ EN RIESGO',
                'desc' => 'Verifica si tu Toyota está en Campaña.',
                'type' => 'image',
                'img_desk' => get_template_directory_uri().'/assets/img/home/banner-recall-desktop.png',
                'img_mob' => get_template_directory_uri().'/assets/img/home/banner-recall-mobile.jpg',
                'btn_text' => 'Verifica aquí',
                'btn_link' => 'https://www.toyota.com.ve/mi-toyota/servicios/recall',
                'btn_target' => '1',
                'menu_order' => 6
            ),
        );
        
        foreach ($default_slides as $s) {
            $post_id = wp_insert_post(array(
                'post_title' => $s['title'],
                'post_status' => 'publish',
                'post_type' => 'slide',
                'menu_order' => $s['menu_order']
            ));
            
            if ($post_id) {
                update_post_meta($post_id, 'slide_type', $s['type']);
                update_post_meta($post_id, 'slide_desc', $s['desc']);
                update_post_meta($post_id, 'slide_btn_text', $s['btn_text']);
                update_post_meta($post_id, 'slide_btn_link', $s['btn_link']);
                update_post_meta($post_id, 'slide_btn_target', $s['btn_target']);
                
                if ($s['type'] === 'video') {
                    update_post_meta($post_id, 'slide_video_url', $s['video']);
                } else {
                    update_post_meta($post_id, 'slide_img_desktop', $s['img_desk']);
                    update_post_meta($post_id, 'slide_img_mobile', $s['img_mob']);
                }
            }
        }
    }
});

// ---------------------------------------------
// Drag and Drop Slide Ordering
// ---------------------------------------------
add_action('admin_enqueue_scripts', function($hook) {
    global $post_type;
    if ($hook == 'edit.php' && $post_type == 'slide') {
        wp_enqueue_script('jquery-ui-sortable');
        wp_add_inline_script('jquery-ui-sortable', '
            jQuery(document).ready(function($) {
                $("table.wp-list-table tbody").sortable({
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
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'update_slide_order_nonce')) {
        wp_send_json_error();
    }
    if (!current_user_can('edit_posts')) {
        wp_send_json_error();
    }
    
    $order = isset($_POST['order']) ? (array) $_POST['order'] : array();
    if (!empty($order)) {
        foreach ($order as $menu_order => $post_id) {
            wp_update_post(array(
                'ID' => intval($post_id),
                'menu_order' => $menu_order
            ));
        }
        wp_send_json_success();
    }
    wp_send_json_error();
});

// Respect menu_order on edit.php for slides
add_action('pre_get_posts', function($query) {
    global $pagenow;
    if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'slide') {
        if (!isset($_GET['orderby'])) {
            $query->set('orderby', 'menu_order');
            $query->set('order', 'ASC');
        }
    }
});
