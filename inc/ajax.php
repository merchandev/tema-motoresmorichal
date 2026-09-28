<?php
/**
 * AJAX Handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

// ---------------------------------------------
// AJAX: Load more inventory (new and used vehicles)
// ---------------------------------------------
add_action('wp_ajax_toyota_load_more_vehiculos', 'toyota_load_more_vehiculos');
add_action('wp_ajax_nopriv_toyota_load_more_vehiculos', 'toyota_load_more_vehiculos');
add_action('wp_ajax_toyota_load_more_usados', 'toyota_load_more_usados');
add_action('wp_ajax_nopriv_toyota_load_more_usados', 'toyota_load_more_usados');

function toyota_render_inventory_cards($query) {
    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        $pid     = get_the_ID();
        $title   = get_the_title();
        $content = get_the_content();
        $thumb   = get_the_post_thumbnail_url($pid, 'large');

        if (!$thumb) {
            $cols = get_post_meta($pid, 'veh_colores', true);
            if (!empty($cols) && is_array($cols)) {
                if (!empty($cols[0]['img_id'])) {
                    $thumb = wp_get_attachment_image_url((int) $cols[0]['img_id'], 'large');
                } elseif (!empty($cols[0]['img'])) {
                    $thumb = $cols[0]['img'];
                }
            }
        }
        if (!$thumb) {
            $thumb = function_exists('toyota_monagas_placeholder_image_url')
                ? toyota_monagas_placeholder_image_url()
                : '';
        }

        $terms     = wp_get_post_terms($pid, 'vehiculo_categoria', array('fields' => 'names'));
        $term_name = (!is_wp_error($terms) && !empty($terms)) ? $terms[0] : '';
        $cat_map   = array(
            'Camioneta' => 'cars',
            'Pasajero'  => 'trucks',
            'Pick Ups'  => 'crossovers',
            'Comercial' => 'electrified',
        );
        $data_cat = isset($cat_map[$term_name]) ? $cat_map[$term_name] : 'cars';
        $price    = get_post_meta($pid, 'veh_precio', true);
        ?>
        <article class="veh-card" data-cat="<?php echo esc_attr($data_cat); ?>" role="listitem">
          <figure class="veh-card__img">
            <a href="<?php echo esc_url(get_permalink($pid)); ?>">
              <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
            </a>
          </figure>
          <div class="veh-card__body">
            <div class="veh-card__top">
              <h3><?php echo esc_html($title); ?></h3>
            </div>
            <p class="veh-card__excerpt"><?php echo wp_kses_post(wp_trim_words($content, 20, '…')); ?></p>
            <div class="veh-card__meta">
              <?php if ($price) : ?><span class="veh-card__price">$<?php echo esc_html($price); ?></span><?php endif; ?>
              <a class="veh-card__link" href="<?php echo esc_url(get_permalink($pid)); ?>">Ver modelo</a>
            </div>
          </div>
        </article>
        <?php
    }

    wp_reset_postdata();
    return ob_get_clean();
}

function toyota_load_more_inventory($post_type) {
    $nonce = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'nonce'));
    if (!$nonce || !wp_verify_nonce($nonce, 'toyota_front_nonce')) {
        wp_send_json_error(array('message' => 'La sesión expiró. Recarga la página e intenta nuevamente.'), 403);
    }

    $page = max(1, min(1000, absint(toyota_monagas_request_scalar($_POST, 'page', '1'))));
    $cat  = sanitize_key(toyota_monagas_request_scalar($_POST, 'cat', 'all'));
    $term_map = array(
        'cars'        => 'camioneta',
        'trucks'      => 'pasajero',
        'crossovers'  => 'pick-ups',
        'electrified' => 'comercial',
    );
    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => 9,
        'post_status'    => 'publish',
        'paged'          => $page,
    );

    if ($cat !== 'all' && isset($term_map[$cat])) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'vehiculo_categoria',
            'field'    => 'slug',
            'terms'    => $term_map[$cat],
        ));
    }

    $query     = new WP_Query($args);
    $max_pages = (int) $query->max_num_pages;
    $html      = toyota_render_inventory_cards($query);
    $next_page = $page + 1;

    wp_send_json_success(array(
        'html'      => $html,
        'page'      => $page,
        'next_page' => $next_page,
        'max_pages' => $max_pages,
        'max'       => $max_pages,
        'has_more'  => $next_page <= $max_pages,
    ));
}

function toyota_load_more_vehiculos() {
    toyota_load_more_inventory('vehiculo');
}

function toyota_load_more_usados() {
    toyota_load_more_inventory('vehiculo_usado');
}

// ---------------------------------------------
// AJAX: Load more blog posts (for blog page)
// ---------------------------------------------
add_action('wp_ajax_toyota_load_more_posts', 'toyota_load_more_posts');
add_action('wp_ajax_nopriv_toyota_load_more_posts', 'toyota_load_more_posts');

function toyota_load_more_posts(){
  $nonce = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'nonce'));
  if (!$nonce || !wp_verify_nonce($nonce, 'toyota_front_nonce')){
    wp_send_json_error(array('message' => 'La sesión expiró. Recarga la página.'), 403);
  }

  $page = max(2, min(1000, absint(toyota_monagas_request_scalar($_POST, 'page', '2'))));
  $ppp  = 10; // items per page after featured

  // Initial HTML contains one featured post plus the following nine posts.
  $initial_items = 9;
  $offset = 1 + $initial_items + (($page - 2) * $ppp);

  $q = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $ppp,
    'offset'         => $offset,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'ignore_sticky_posts' => true,
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
              <img src="<?php echo esc_url(toyota_monagas_placeholder_image_url()); ?>" alt="<?php echo $img_alt; ?>" loading="lazy" decoding="async" />
            <?php } ?>
          </figure>
          <div class="blog-mini-body">
            <h2 class="blog-mini-title"><?php the_title(); ?></h2>
            <p class="blog-mini-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
          </div>
        </a>
      </article>
      <?php
    }
    wp_reset_postdata();
  }
  $html = ob_get_clean();

  // Initial HTML is logical page 1 and subsequent AJAX requests start at page 2.
  $total = (int) wp_count_posts('post')->publish;
  $remaining = max(0, $total - 1 - $initial_items);
  $max_pages = 1 + (int) ceil($remaining / $ppp);

  wp_send_json_success(array(
    'html'      => $html,
    'page'      => $page,
    'next_page' => $page + 1,
    'max'       => $max_pages,
    'has_more'  => $page < $max_pages,
  ));
}

// ---------------------------------------------
// AJAX: Search posts (autocomplete)
// ---------------------------------------------
add_action('wp_ajax_toyota_search_posts', 'toyota_search_posts');
add_action('wp_ajax_nopriv_toyota_search_posts', 'toyota_search_posts');

function toyota_search_posts(){
  $nonce = sanitize_text_field(toyota_monagas_request_scalar($_GET, 'nonce'));
  if (!$nonce || !wp_verify_nonce($nonce, 'toyota_front_nonce')){
    wp_send_json_error(array('message' => 'La sesión expiró. Recarga la página.'), 403);
  }
  $q = sanitize_text_field(toyota_monagas_request_scalar($_GET, 'q'));
  $query_length = function_exists('mb_strlen') ? mb_strlen($q, 'UTF-8') : strlen($q);
  if ($q === '' || $query_length < 2) { wp_send_json_success(array('items'=>array())); }
  if ($query_length > 100) {
    wp_send_json_error(array('message' => 'La búsqueda es demasiado larga.'), 400);
  }
  $args = array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    's'              => $q,
    'posts_per_page' => 8,
    'orderby'        => 'relevance',
    'order'          => 'DESC',
    'ignore_sticky_posts' => true,
  );
  $qobj = new WP_Query($args);
  $items = array();
  if ($qobj->have_posts()){
    while($qobj->have_posts()){ $qobj->the_post();
      $items[] = array(
        'title' => wp_strip_all_tags(get_the_title(), true),
        'url'   => esc_url_raw(get_permalink()),
        'date'  => get_the_date(),
        'thumb' => esc_url_raw(get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: ''),
      );
    }
    wp_reset_postdata();
  }
  wp_send_json_success(array('items'=>$items));
}
