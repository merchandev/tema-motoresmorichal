<?php
/*
Template Name: Vehículos Usados
*/
get_header(); ?>

<main id="site-main" class="vehiculo-premium-white">
  <h1 class="sr-only">Vehículos Toyota usados</h1>
  <?php
    // Destacado principal (vehículo usado - aleatorio)
    $highlight_q = new WP_Query(array(
      'post_type' => 'vehiculo_usado',
      'posts_per_page' => 1,
      'post_status' => 'publish',
      'orderby' => 'rand',
    ));
    $highlight = $highlight_q->have_posts() ? $highlight_q->posts[0] : null;
    $hero_img = '';
    $hero_title = '';
    $hero_sub = '';
    $hero_price = '';
    $hero_ficha = '';
    $hero_legal = '';
    $hero_cols = array();

    if ($highlight) {
      $pid = $highlight->ID;
      $hero_title = get_the_title($pid);
      $hero_sub = get_post_meta($pid, 'veh_subtitulo', true);
      $hero_price = get_post_meta($pid, 'veh_precio', true);
      $fid = get_post_meta($pid, 'veh_ficha_id', true);
      $hero_ficha = $fid ? wp_get_attachment_url((int)$fid) : get_post_meta($pid, 'veh_ficha_url', true);
      $hero_legal = get_post_meta($pid, 'veh_legal_url', true);

      $hero_img = get_the_post_thumbnail_url($pid, 'full');
      $hero_cols = get_post_meta($pid, 'veh_colores', true);
      if (!is_array($hero_cols)) {
        $tmp = json_decode((string)$hero_cols, true);
        $hero_cols = is_array($tmp) ? $tmp : array();
      }
      if (!$hero_img && !empty($hero_cols[0]['img'])) $hero_img = $hero_cols[0]['img'];
      if (!$hero_img) $hero_img = toyota_monagas_placeholder_image_url();
    }
  ?>

  <?php if ($highlight): ?>
  <section class="veh-hero" style="background-image: linear-gradient(120deg, rgba(0,0,0,0.55), rgba(0,0,0,0.35)), url('<?php echo esc_url($hero_img); ?>');">
    <div class="container veh-hero__inner">
      <div class="veh-hero__badge">Línea Toyota Usados</div>
      <h2 class="veh-hero__title"><?php echo esc_html($hero_title); ?></h2>
      <?php if($hero_sub && strcasecmp(trim($hero_sub), trim($hero_title)) !== 0): ?><p class="veh-hero__subtitle"><?php echo esc_html($hero_sub); ?></p><?php endif; ?>
      <div class="veh-hero__cta">
        <a class="veh-cta-primary" href="<?php echo esc_url(get_permalink($pid)); ?>">Descubrir modelo</a>
        <?php if($hero_legal): ?><a class="veh-cta-link" href="<?php echo esc_url($hero_legal); ?>" target="_blank" rel="noopener noreferrer">Texto legal</a><?php endif; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>


  <section class="veh-catalogo" aria-label="Modelos Toyota usados">
    <div class="container">
      <header class="veh-section-head">
        <div>
          <span class="veh-tag">Modelos usados</span>
          <h2>Explora la línea de vehículos usados</h2>
          <p>Camionetas, pasajeros, pick ups y comerciales listos para tu próxima ruta.</p>
        </div>
        <div class="veh-filter-wrap">
          <?php
          $base_url = remove_query_arg('paged', get_permalink());
          $filters = array(
            'all' => array('label'=>'Todos','slug'=>''),
            'cars' => array('label'=>'Camioneta','slug'=>'camioneta'),
            'trucks' => array('label'=>'Pasajero','slug'=>'pasajero'),
            'crossovers' => array('label'=>'Pick Ups','slug'=>'pick-ups'),
            'electrified' => array('label'=>'Comercial','slug'=>'comercial'),
          );
          $active_cat = sanitize_key(toyota_monagas_request_scalar($_GET, 'cat', 'all'));
          if (!isset($filters[$active_cat])) $active_cat = 'all';
          foreach($filters as $key=>$f){
            $url = $key === 'all' ? $base_url : add_query_arg('cat', $key, $base_url);
            $active = ($key === $active_cat) ? ' is-active' : '';
            $aria_current = ($key === $active_cat) ? ' aria-current="page"' : '';
            echo '<a class="veh-filter'.esc_attr($active).'" href="'.esc_url($url).'"'.$aria_current.'>'.esc_html($f['label']).'</a>';
          }
          ?>
        </div>
      </header>

      <div class="veh-grid" role="list" aria-live="polite">
        <?php
        $cat_map = array(
          'Camioneta' => 'cars',
          'Pasajero'  => 'trucks',
          'Pick Ups'  => 'crossovers',
          'Comercial' => 'electrified',
        );
        $term_map = array('cars'=>'camioneta','trucks'=>'pasajero','crossovers'=>'pick-ups','electrified'=>'comercial');
        $paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
        $args = array(
          'post_type' => 'vehiculo_usado',
          'posts_per_page' => 9,
          'post_status' => 'publish',
          'paged' => $paged,
        );
        if (!empty($active_cat) && $active_cat !== 'all' && isset($term_map[$active_cat])){
          $args['tax_query'] = array(array(
            'taxonomy' => 'vehiculo_categoria',
            'field'    => 'slug',
            'terms'    => $term_map[$active_cat],
          ));
        }
        $q = new WP_Query($args);
        if ($q->have_posts()) :
          while ($q->have_posts()) : $q->the_post();
            $pid = get_the_ID();
            $title = get_the_title();
            $content = get_the_content();
            $thumb = get_the_post_thumbnail_url($pid, 'large');
            if (!$thumb) {
              $cols = get_post_meta($pid, 'veh_colores', true);
              if (!empty($cols) && is_array($cols) && !empty($cols[0]['img'])) $thumb = esc_url($cols[0]['img']);
            }
            if (!$thumb) $thumb = toyota_monagas_placeholder_image_url();
            $terms = wp_get_post_terms($pid, 'vehiculo_categoria', array('fields'=>'names'));
            $term_name = (!empty($terms) && is_array($terms)) ? $terms[0] : '';
            $data_cat = isset($cat_map[$term_name]) ? $cat_map[$term_name] : 'cars';
            $price = get_post_meta($pid,'veh_precio',true);
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
            <p class="veh-card__excerpt"><?php echo wp_kses_post( wp_trim_words( $content, 20, '…' ) ); ?></p>
            <div class="veh-card__meta">
              <?php if($price): ?><span class="veh-card__price">$<?php echo esc_html($price); ?></span><?php endif; ?>
              <a class="veh-card__link" href="<?php echo esc_url(get_permalink($pid)); ?>">Ver modelo</a>
            </div>
          </div>
        </article>
        <?php
          endwhile;
          wp_reset_postdata();
        else:
        ?>
          <p>No hay vehículos usados publicados aún.</p>
        <?php endif; ?>
      </div>

      <?php if ($q->max_num_pages > $paged) : ?>
      <div class="veh-pagination">
        <?php
          $next_page = $paged + 1;
          $max_page = $q->max_num_pages;
          $fallback_url = add_query_arg('paged', $next_page, get_permalink(get_queried_object_id()));
          if ($active_cat !== 'all') {
            $fallback_url = add_query_arg('cat', $active_cat, $fallback_url);
          }
        ?>
        <a class="veh-cta-secondary veh-loadmore" id="toyota-loadmore-usados"
          href="<?php echo esc_url($fallback_url); ?>"
          data-action="toyota_load_more_usados"
          data-target=".veh-grid"
          data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
          data-nonce="<?php echo esc_attr(wp_create_nonce('toyota_front_nonce')); ?>"
          data-page="<?php echo esc_attr($next_page); ?>"
          data-max="<?php echo esc_attr($max_page); ?>"
          data-cat="<?php echo esc_attr($active_cat); ?>"
          aria-label="Cargar más vehículos usados" aria-live="polite">
          Cargar más
        </a>
      </div>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php get_footer(); ?>
