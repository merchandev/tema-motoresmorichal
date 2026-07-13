<?php
/**
 * AJAX Handlers
 */

// ---------------------------------------------
// AJAX: Load more vehicles (returns rendered article cards)
// ---------------------------------------------
add_action('wp_ajax_toyota_load_more_vehiculos', 'toyota_load_more_vehiculos');
add_action('wp_ajax_nopriv_toyota_load_more_vehiculos', 'toyota_load_more_vehiculos');

function toyota_load_more_vehiculos(){
    // nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'toyota_front_nonce')){
        wp_send_json_error('nonce_invalid');
    }
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $cat  = isset($_POST['cat']) ? sanitize_text_field($_POST['cat']) : '';
    $term_map = array('cars'=>'camioneta','trucks'=>'pasajero','crossovers'=>'pick-ups','electrified'=>'comercial');
    $args = array(
        'post_type' => 'vehiculo',
        'posts_per_page' => 9,
        'post_status' => 'publish',
        'paged' => $page,
    );
    if (!empty($cat) && $cat !== 'all' && isset($term_map[$cat])){
        $args['tax_query'] = array(array(
            'taxonomy' => 'vehiculo_categoria',
            'field'    => 'slug',
            'terms'    => $term_map[$cat],
        ));
    }
    $q = new WP_Query($args);
    $html = '';
    if ($q->have_posts()){
        while ($q->have_posts()){ $q->the_post();
            $pid = get_the_ID();
            $title = get_the_title();
            $content = get_the_content();
            $thumb = get_the_post_thumbnail_url($pid, 'large');
            if (!$thumb) {
                $cols = get_post_meta($pid, 'veh_colores', true);
                if (!empty($cols) && is_array($cols) && !empty($cols[0]['img'])) $thumb = esc_url($cols[0]['img']);
            }
            if (!$thumb) $thumb = 'https://picsum.photos/seed/'.intval($pid).'/800/600';
            $terms = wp_get_post_terms($pid, 'vehiculo_categoria', array('fields'=>'names'));
            $term_name = (!empty($terms) && is_array($terms)) ? $terms[0] : '';
            $cat_map = array('Camioneta'=>'cars','Pasajero'=>'trucks','Pick Ups'=>'crossovers','Comercial'=>'electrified');
            $data_cat = isset($cat_map[$term_name]) ? $cat_map[$term_name] : 'cars';
            ob_start();
            ?>
            <article class="toyota-card" data-cat="<?php echo esc_attr($data_cat); ?>" role="listitem">
              <figure class="toyota-imgbox">
                <a href="<?php echo esc_url(get_permalink($pid)); ?>">
                  <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" decoding="async" loading="lazy" referrerpolicy="no-referrer">
                </a>
              </figure>
              <div class="toyota-info">
                <header class="toyota-info-text">
                  <span class="toyota-year"><?php echo esc_html(get_post_meta($pid,'veh_subtitulo',true)?:''); ?></span>
                  <h3><?php echo esc_html($title); ?></h3>
                  <p><?php echo wp_kses_post( wp_trim_words( $content, 24, '…' ) ); ?></p>
                </header>
                <div class="toyota-buttons">
                  <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="toyota-btn">Más información <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
                  <span class="toyota-contact"><a href="#contacto">Contáctanos &gt;</a></span>
                </div>
              </div>
            </article>
            <?php
            $html .= ob_get_clean();
        }
        wp_reset_postdata();
    }
    wp_send_json_success(array('html'=>$html, 'max'=> (int)$q->max_num_pages));
}

// ---------------------------------------------
// AJAX: Load more used vehicles (returns rendered article cards)
// ---------------------------------------------
add_action('wp_ajax_toyota_load_more_usados', 'toyota_load_more_usados');
add_action('wp_ajax_nopriv_toyota_load_more_usados', 'toyota_load_more_usados');

function toyota_load_more_usados(){
    // nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'toyota_front_nonce')){
        wp_send_json_error('nonce_invalid');
    }
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $cat  = isset($_POST['cat']) ? sanitize_text_field($_POST['cat']) : '';
    $term_map = array('cars'=>'camioneta','trucks'=>'pasajero','crossovers'=>'pick-ups','electrified'=>'comercial');
    $args = array(
        'post_type' => 'vehiculo_usado',
        'posts_per_page' => 9,
        'post_status' => 'publish',
        'paged' => $page,
    );
    if (!empty($cat) && $cat !== 'all' && isset($term_map[$cat])){
        $args['tax_query'] = array(array(
            'taxonomy' => 'vehiculo_categoria',
            'field'    => 'slug',
            'terms'    => $term_map[$cat],
        ));
    }
    $q = new WP_Query($args);
    $html = '';
    if ($q->have_posts()){
        while ($q->have_posts()){ $q->the_post();
            $pid = get_the_ID();
            $title = get_the_title();
            $content = get_the_content();
            $thumb = get_the_post_thumbnail_url($pid, 'large');
            if (!$thumb) {
                $cols = get_post_meta($pid, 'veh_colores', true);
                if (!empty($cols) && is_array($cols) && !empty($cols[0]['img'])) $thumb = esc_url($cols[0]['img']);
            }
            if (!$thumb) $thumb = 'https://picsum.photos/seed/'.intval($pid).'/800/600';
            $terms = wp_get_post_terms($pid, 'vehiculo_categoria', array('fields'=>'names'));
            $term_name = (!empty($terms) && is_array($terms)) ? $terms[0] : '';
            $cat_map = array('Camioneta'=>'cars','Pasajero'=>'trucks','Pick Ups'=>'crossovers','Comercial'=>'electrified');
            $data_cat = isset($cat_map[$term_name]) ? $cat_map[$term_name] : 'cars';
            ob_start();
            ?>
            <article class="toyota-card" data-cat="<?php echo esc_attr($data_cat); ?>" role="listitem">
              <figure class="toyota-imgbox">
                <a href="<?php echo esc_url(get_permalink($pid)); ?>">
                  <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" decoding="async" loading="lazy" referrerpolicy="no-referrer">
                </a>
              </figure>
              <div class="toyota-info">
                <header class="toyota-info-text">
                  <span class="toyota-year"><?php echo esc_html(get_post_meta($pid,'veh_subtitulo',true)?:''); ?></span>
                  <h3><?php echo esc_html($title); ?></h3>
                  <p><?php echo wp_kses_post( wp_trim_words( $content, 24, '…' ) ); ?></p>
                </header>
                <div class="toyota-buttons">
                  <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="toyota-btn">Más información <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
                  <span class="toyota-contact"><a href="#contacto">Consultar disponibilidad &gt;</a></span>
                </div>
              </div>
            </article>
            <?php
            $html .= ob_get_clean();
        }
        wp_reset_postdata();
    }
    wp_send_json_success(array('html'=>$html, 'max'=> (int)$q->max_num_pages));
}

// ---------------------------------------------
// AJAX: Load more blog posts (for blog page)
// ---------------------------------------------
add_action('wp_ajax_toyota_load_more_posts', 'toyota_load_more_posts');
add_action('wp_ajax_nopriv_toyota_load_more_posts', 'toyota_load_more_posts');

function toyota_load_more_posts(){
  // nonce
  if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'toyota_front_nonce')){
    wp_send_json_error('nonce_invalid');
  }

  $page = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
  $ppp  = 10; // items per page after featured

  // Calculate offset to skip featured on first page
  $offset = 1 + ($page - 1) * $ppp;

  $q = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $ppp,
    'offset'         => $offset,
    'orderby'        => 'date',
    'order'          => 'DESC',
  ));

  ob_start();
  if ($q->have_posts()){
    while ($q->have_posts()){ $q->the_post();
      $img_alt = esc_attr(get_the_title());
      $has_thumb = has_post_thumbnail();
      ?>
      <article class="blog-mini">
        <a class="blog-mini-link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
          <figure class="blog-mini-img">
            <?php if ($has_thumb) { the_post_thumbnail('large', array('alt'=>$img_alt)); } else { ?>
              <img src="https://picsum.photos/seed/<?php echo (int)get_the_ID(); ?>/600/400" alt="<?php echo $img_alt; ?>" />
            <?php } ?>
          </figure>
          <div class="blog-mini-body">
            <h4 class="blog-mini-title"><?php the_title(); ?></h4>
            <p class="blog-mini-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
          </div>
        </a>
      </article>
      <?php
    }
    wp_reset_postdata();
  }
  $html = ob_get_clean();

  // compute max pages
  $total = (int) wp_count_posts('post')->publish;
  $remaining = max(0, $total - 1); // minus featured
  $max_pages = (int) ceil($remaining / $ppp);

  wp_send_json_success(array('html' => $html, 'max' => $max_pages));
}

// ---------------------------------------------
// AJAX: Search posts (autocomplete)
// ---------------------------------------------
add_action('wp_ajax_toyota_search_posts', 'toyota_search_posts');
add_action('wp_ajax_nopriv_toyota_search_posts', 'toyota_search_posts');

function toyota_search_posts(){
  if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'toyota_front_nonce')){
    wp_send_json_error('nonce_invalid');
  }
  $q = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
  if ($q === '' || strlen($q) < 2) { wp_send_json_success(array('items'=>array())); }
  $args = array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    's'              => $q,
    'posts_per_page' => 8,
    'orderby'        => 'relevance',
    'order'          => 'DESC',
  );
  $qobj = new WP_Query($args);
  $items = array();
  if ($qobj->have_posts()){
    while($qobj->have_posts()){ $qobj->the_post();
      $items[] = array(
        'title' => html_entity_decode( wp_strip_all_tags( get_the_title() ) ),
        'url'   => get_permalink(),
        'date'  => get_the_date(),
        'thumb' => get_the_post_thumbnail_url(get_the_ID(),'thumbnail') ?: '',
      );
    }
    wp_reset_postdata();
  }
  wp_send_json_success(array('items'=>$items));
}
