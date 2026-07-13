<?php
/**
 * Admin Tools
 */

// Admin: page to reimport images for vehiculos missing featured image
add_action('admin_menu', function(){
  add_submenu_page('edit.php?post_type=vehiculo', 'Reimportar imágenes', 'Reimportar imágenes', 'manage_options', 'toyota-reimport-images', 'toyota_reimport_images_page');

  // Shortcut to Hero Slider Customizer from Sidebar Menu
  add_menu_page(
      'Hero Slider', 
      'Hero Slider', 
      'edit_theme_options', 
      'customize.php?autofocus[section]=hero_slider_section', 
      '', 
      'dashicons-images-alt2', 
      30
  );
});

function toyota_reimport_images_page(){
  if (!current_user_can('manage_options')) return;
  echo '<div class="wrap"><h1>Reimportar imágenes de Vehículos</h1>';
  if (isset($_POST['toyota_reimport_action'])){
    if (!isset($_POST['toyota_reimport_nonce']) || !wp_verify_nonce($_POST['toyota_reimport_nonce'],'toyota_reimport')){
      echo '<div class="notice notice-error"><p>Nonce no válido.</p></div>';
    } else {
      $items = get_posts(array('post_type'=>'vehiculo','posts_per_page'=>-1,'post_status'=>'publish'));
      $done = 0; $failed = 0; $skipped = 0;
      foreach($items as $it){
        if (has_post_thumbnail($it->ID)){ $skipped++; continue; }
        $cols = get_post_meta($it->ID,'veh_colores',true);
        $url = '';
        if (!empty($cols) && is_array($cols)){
          foreach($cols as $c){ if (!empty($c['img'])){ $url = $c['img']; break; } }
        }
        if (!$url) { $failed++; continue; }
        $res = false;
        if (function_exists('toyota_ss_set_featured')) $res = toyota_ss_set_featured($url, $it->ID);
        if ($res) $done++; else $failed++;
      }
      echo '<div class="notice notice-success"><p>Importación completada. <strong>'.$done.'</strong> importadas, <strong>'.$failed.'</strong> fallidas, <strong>'.$skipped.'</strong> ya tenían imagen.</p></div>';
    }
  }
  // Show list of vehicles without featured image (sample)
  $noimg = get_posts(array('post_type'=>'vehiculo','posts_per_page'=>200,'post_status'=>'publish'));
  $count_noimg = 0;
  foreach($noimg as $ni) if (!has_post_thumbnail($ni->ID)) $count_noimg++;
  echo '<p>Vehículos publicados: <strong>'.count($noimg).'</strong>. Sin imagen destacada: <strong>'.$count_noimg.'</strong>.</p>';
  ?>
  <form method="post">
    <?php wp_nonce_field('toyota_reimport','toyota_reimport_nonce'); ?>
    <input type="hidden" name="toyota_reimport_action" value="1" />
    <p><button class="button button-primary" type="submit">Reintentar importación de imágenes</button></p>
  </form>
  <?php
  echo '</div>';
}
