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
});
