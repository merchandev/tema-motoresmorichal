<?php
get_header();
$tm_repuestos_url = toyota_monagas_whatsapp_url(
  'Hola, necesito solicitar repuestos originales Toyota. ¿Podrían ayudarme?'
);
$tm_servicio_url = toyota_monagas_whatsapp_url(
  'Hola, me gustaría agendar una cita para el servicio de mi Toyota. ¿Cuál es la disponibilidad?'
);
?>

<main id="site-main" class="vehiculo-premium-white">
  <h1 class="sr-only">Motores Morichal, concesionario Toyota en Maturín</h1>
  <!-- Slider de videos -->
  <?php $home_slides = toyota_monagas_get_home_slides(); ?>
  <div id="custom-slider" class="custom-slider swiper" role="region" aria-label="Slider principal Toyota">
    <div class="swiper-wrapper">
      <?php foreach ($home_slides as $slide_index => $slide) :
          $is_first = $slide_index === 0;
          $image_loading = $is_first ? 'eager' : 'lazy';
          $image_priority = $is_first ? 'high' : 'low';
          ?>
          <div class="swiper-slide cs-slide" data-index="<?php echo esc_attr($slide_index); ?>" data-type="<?php echo esc_attr($slide['type']); ?>">
            <?php if ($slide['type'] === 'video' && $slide['video'] !== '') : ?>
              <?php if ($is_first) : ?>
                <?php // First slide: the browser starts downloading while parsing, before any script runs. ?>
                <video class="cs-video-bg" muted playsinline autoplay preload="auto"
                  data-src="<?php echo esc_url($slide['video']); ?>"
                  <?php if ($slide['video_mobile'] !== '') : ?>data-src-mobile="<?php echo esc_url($slide['video_mobile']); ?>"<?php endif; ?>
                  <?php if ($slide['poster'] !== '') : ?>poster="<?php echo esc_url($slide['poster']); ?>"<?php endif; ?>>
                  <?php if ($slide['video_mobile'] !== '') : ?>
                    <source src="<?php echo esc_url($slide['video']); ?>"<?php echo toyota_monagas_video_type_attr($slide['video']); ?> media="(min-width: 769px)">
                    <source src="<?php echo esc_url($slide['video_mobile']); ?>"<?php echo toyota_monagas_video_type_attr($slide['video_mobile']); ?>>
                  <?php else : ?>
                    <source src="<?php echo esc_url($slide['video']); ?>"<?php echo toyota_monagas_video_type_attr($slide['video']); ?>>
                  <?php endif; ?>
                </video>
              <?php else : ?>
                <video class="cs-video-bg" muted playsinline preload="none"
                  data-src="<?php echo esc_url($slide['video']); ?>"
                  <?php if ($slide['video_mobile'] !== '') : ?>data-src-mobile="<?php echo esc_url($slide['video_mobile']); ?>"<?php endif; ?>
                  <?php if ($slide['poster'] !== '') : ?>data-poster="<?php echo esc_url($slide['poster']); ?>"<?php endif; ?>></video>
              <?php endif; ?>
            <?php elseif ($slide['type'] === 'image' && $slide['img_desktop'] !== '') : ?>
              <?php if ($slide['img_mobile'] !== $slide['img_desktop']) : ?>
                <picture class="cs-picture-bg">
                  <source media="(max-width: 768px)" srcset="<?php echo esc_url($slide['img_mobile']); ?>">
                  <img class="cs-video-bg" src="<?php echo esc_url($slide['img_desktop']); ?>" alt="<?php echo esc_attr($slide['title']); ?>" loading="<?php echo esc_attr($image_loading); ?>" fetchpriority="<?php echo esc_attr($image_priority); ?>" decoding="async">
                </picture>
              <?php else : ?>
                <img class="cs-video-bg desktop-media" src="<?php echo esc_url($slide['img_desktop']); ?>" alt="<?php echo esc_attr($slide['title']); ?>" loading="<?php echo esc_attr($image_loading); ?>" fetchpriority="<?php echo esc_attr($image_priority); ?>" decoding="async">
              <?php endif; ?>
            <?php endif; ?>

            <div class="cs-slide-content cs-left animate-in">
              <h2><?php echo esc_html($slide['title']); ?></h2>
              <?php if ($slide['desc'] !== '') : ?><p><?php echo esc_html($slide['desc']); ?></p><?php endif; ?>
              <?php if ($slide['btn_text'] !== '' && $slide['btn_link'] !== '') : ?>
                <a href="<?php echo esc_url($slide['btn_link']); ?>" class="cs-btn-slide" target="<?php echo esc_attr($slide['btn_target']); ?>"<?php echo $slide['btn_target'] === '_blank' ? ' rel="noopener noreferrer"' : ''; ?>><?php echo esc_html($slide['btn_text']); ?></a>
              <?php endif; ?>
            </div>
          </div>
      <?php endforeach; ?>
    </div>

    <!-- Flechas -->
    <div class="cs-swiper-button-prev swiper-button-prev" role="button" tabindex="0" aria-label="Slide anterior"><?php echo toyota_monagas_icon('chevron-left'); ?></div>
    <div class="cs-swiper-button-next swiper-button-next" role="button" tabindex="0" aria-label="Slide siguiente"><?php echo toyota_monagas_icon('chevron-right'); ?></div>
    <button type="button" class="cs-play-toggle" aria-pressed="false" aria-label="Pausar slider">Pausar</button>

    <!-- Barras de progreso -->
    <div class="cs-progress-bars">
      <?php foreach ($home_slides as $slide_index => $slide) : ?>
        <div class="cs-progress-bar" data-index="<?php echo esc_attr($slide_index); ?>"><span></span></div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Veh&iacute;culos por categor&iacute;as -->
  <section id="vehiculos" class="toyota-section">
    <header class="section-header">
      <span class="kicker">Modelos</span>
      <h2>Descubre la línea Toyota</h2>
      <p>Explora por categoría el Toyota ideal para ti</p>
    </header>
    <nav class="toyota-nav" aria-label="Categor&iacute;as de veh&iacute;culos">
      <div class="toyota-tabs" role="tablist" aria-label="Tipo de vehículo">
        <button type="button" id="toyota-tab-cars" class="toyota-tab is-active" role="tab" data-cat="cars" aria-controls="toyota-vehicle-panel" aria-selected="true" tabindex="0">Camioneta</button>
        <button type="button" id="toyota-tab-trucks" class="toyota-tab" role="tab" data-cat="trucks" aria-controls="toyota-vehicle-panel" aria-selected="false" tabindex="-1">Pasajero</button>
        <button type="button" id="toyota-tab-crossovers" class="toyota-tab" role="tab" data-cat="crossovers" aria-controls="toyota-vehicle-panel" aria-selected="false" tabindex="-1">Pick Ups</button>
        <button type="button" id="toyota-tab-electrified" class="toyota-tab" role="tab" data-cat="electrified" aria-controls="toyota-vehicle-panel" aria-selected="false" tabindex="-1">Comercial</button>
        <span class="toyota-tab-indicator"></span>
      </div>
    </nav>

    <div id="toyota-vehicle-panel" class="toyota-slider swiper" role="tabpanel" aria-labelledby="toyota-tab-cars">
      <div class="swiper-wrapper"></div>
    </div>
    <div class="toyota-slider-controls">
      <button type="button" class="toyota-arrow toyota-arrow--prev" aria-label="Vehículo anterior"><?php echo toyota_monagas_icon('chevron-left'); ?></button>
      <button type="button" class="toyota-arrow toyota-arrow--next" aria-label="Vehículo siguiente"><?php echo toyota_monagas_icon('chevron-right'); ?></button>
    </div>

    <div class="toyota-templates" hidden aria-hidden="true">
      <?php
      // Map taxonomy term names to frontend data-cat identifiers
      $cat_map = array(
        'Camioneta' => 'cars',
        'Pasajero'  => 'trucks',
        'Pick Ups'  => 'crossovers',
        'Comercial' => 'electrified',
    );
      // Keep homepage HTML bounded; full inventory remains on catalog pages.
      $home_vehicle_limit = absint(apply_filters('toyota_home_vehicle_limit', 48));
      $home_vehicle_limit = max(4, min(100, $home_vehicle_limit));
      $veh_q = new WP_Query(array(
          'post_type' => 'vehiculo',
          'posts_per_page' => $home_vehicle_limit,
          'post_status' => 'publish',
          'no_found_rows' => true,
      ));
      if ($veh_q->have_posts()) :
        while ($veh_q->have_posts()) : $veh_q->the_post();
          $post_id = get_the_ID();
          $title = get_the_title();
          $content = get_the_content();
          $thumb = get_the_post_thumbnail_url($post_id, 'large');
          if (!$thumb) {
            // Try first color image saved in meta
            $cols = get_post_meta($post_id, 'veh_colores', true);
            if (!empty($cols) && is_array($cols) && !empty($cols[0]['img'])) $thumb = esc_url($cols[0]['img']);
          }
          if (!$thumb) $thumb = toyota_monagas_placeholder_image_url();
          // Determine category
          $terms = wp_get_post_terms($post_id, 'vehiculo_categoria', array('fields'=>'names'));
          $term_name = (!empty($terms) && is_array($terms)) ? $terms[0] : '';
          $data_cat = isset($cat_map[$term_name]) ? $cat_map[$term_name] : 'cars';
          $wa_fallback = toyota_monagas_whatsapp_url('Hola, quisiera cotizar el ' . $title . '.');
          $subtitle = trim((string) get_post_meta($post_id, 'veh_subtitulo', true));
          if (strcasecmp($subtitle, trim($title)) === 0) $subtitle = '';
      ?>
      <template data-cat="<?php echo esc_attr($data_cat); ?>">
        <article class="toyota-card">
          <figure class="toyota-imgbox">
            <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
              <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" decoding="async" loading="lazy" referrerpolicy="no-referrer">
            </a>
          </figure>
          <div class="toyota-info">
            <header class="toyota-info-text">
              <?php if ($subtitle !== '') : ?><span class="toyota-year"><?php echo esc_html($subtitle); ?></span><?php endif; ?>
              <h3><?php echo esc_html($title); ?></h3>
              <p><?php echo wp_kses_post( wp_trim_words( $content, 20, '...' ) ); ?></p>
            </header>
            <div class="toyota-buttons">
              <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="toyota-btn">M&aacute;s informaci&oacute;n <?php echo toyota_monagas_icon('arrow-right'); ?></a>
              <span class="toyota-contact">
                <a href="<?php echo esc_url($wa_fallback); ?>" target="_blank" rel="noopener noreferrer">Cont&aacute;ctanos &gt;</a>
              </span>
            </div>
          </div>
        </article>
      </template>
      <?php
        endwhile;
        wp_reset_postdata();
      else:
        // No vehicles yet — keep nothing (templates can be static fallback if desired)
      endif;
      ?>
    </div>
  </section>

  <!-- Accesorios -->
  <section id="accesorios-mm" class="accesorios-mm">
    <div class="accesorios-overlay">
      <div class="accesorios-content">
        <h2>Accesorios Originales Toyota</h2>
        <p>Equipa tu Toyota con accesorios dise&ntilde;ados para potenciar estilo, comodidad y seguridad, siempre con la calidad original.</p>
        <a href="https://www.toyota.com.ve/mi-toyota/accesorios" class="accesorio-btn" target="_blank" rel="noopener noreferrer">Explorar Accesorios <?php echo toyota_monagas_icon('arrow-right'); ?></a>
      </div>
    </div>
  </section>

  <!-- Servicios -->
  <section id="info-mm" class="info-mm">
    <header class="section-header">
      <span class="kicker">Servicios</span>
      <h2>Cuidado total para tu Toyota</h2>
      <p>Mantenimiento, repuestos y atención especializada</p>
    </header>
    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" width="48" height="48">
            <path d="M135.2 117.4L109.1 192H402.9l-26.1-74.6C372.3 104.6 360.2 96 346.6 96H165.4c-13.6 0-25.7 8.6-30.2 21.4zM39.6 196.8L74.8 96.3C88.3 57.8 124.6 32 165.4 32H346.6c40.8 0 77.1 25.8 90.6 64.3l35.2 100.5c23.2 9.6 39.6 32.5 39.6 59.2V400v48c0 17.7-14.3 32-32 32H448c-17.7 0-32-14.3-32-32V400H96v48c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32V400 256c0-26.7 16.4-49.6 39.6-59.2zM128 288a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/>
          </svg>
        </div>
        <h3>Veh&iacute;culos</h3>
        <p>Descubre toda la gama de veh&iacute;culos disponibles, pensados para tu estilo de vida.</p>
        <a href="<?php echo esc_url(home_url('/vehiculos/')); ?>" class="service-btn">Explorar <?php echo toyota_monagas_icon('arrow-right'); ?></a>
      </div>
      <div class="service-card">
        <div class="service-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" width="48" height="48">
            <path d="M308.5 135.3c7.1-6.3 9.9-16.2 6.2-25c-2.3-5.3-4.8-10.5-7.6-15.5L304 89.4c-3-5-6.3-9.9-9.8-14.6c-5.7-7.6-15.7-10.1-24.7-7.1l-28.2 9.3c-10.7-8.8-23-16-36.2-20.9L199 27.1c-1.9-9.3-9.1-16.7-18.5-17.8C173.9 8.4 167.2 8 160.4 8h-.7c-6.8 0-13.5 .4-20.1 1.2c-9.4 1.1-16.6 8.6-18.5 17.8L115 56.1c-13.3 5-25.5 12.1-36.2 20.9L50.5 67.8c-9-3-19-.5-24.7 7.1c-3.5 4.7-6.8 9.6-9.9 14.6l-3 5.3c-2.8 5-5.3 10.2-7.6 15.6c-3.7 8.7-.9 18.6 6.2 25l22.2 19.8C32.6 161.9 32 168.9 32 176s.6 14.1 1.7 20.9L11.5 216.7c-7.1 6.3-9.9 16.2-6.2 25c2.3 5.3 4.8 10.5 7.6 15.6l3 5.2c3 5.1 6.3 9.9 9.9 14.6c5.7 7.6 15.7 10.1 24.7 7.1l28.2-9.3c10.7 8.8 23 16 36.2 20.9l6.1 29.1c1.9 9.3 9.1 16.7 18.5 17.8c6.7 .8 13.5 1.2 20.4 1.2s13.7-.4 20.4-1.2c9.4-1.1 16.6-8.6 18.5-17.8l6.1-29.1c13.3-5 25.5-12.1 36.2-20.9l28.2 9.3c9 3 19 .5 24.7-7.1c3.5-4.7 6.8-9.5 9.8-14.6l3.1-5.4c2.8-5 5.3-10.2 7.6-15.5c3.7-8.7 .9-18.6-6.2-25l-22.2-19.8c1.1-6.8 1.7-13.8 1.7-20.9s-.6-14.1-1.7-20.9l22.2-19.8zM112 176a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM504.7 500.5c6.3 7.1 16.2 9.9 25 6.2c5.3-2.3 10.5-4.8 15.5-7.6l5.4-3.1c5-3 9.9-6.3 14.6-9.8c7.6-5.7 10.1-15.7 7.1-24.7l-9.3-28.2c8.8-10.7 16-23 20.9-36.2l29.1-6.1c9.3-1.9 16.7-9.1 17.8-18.5c.8-6.7 1.2-13.5 1.2-20.4s-.4-13.7-1.2-20.4c-1.1-9.4-8.6-16.6-17.8-18.5L583.9 307c-5-13.3-12.1-25.5-20.9-36.2l9.3-28.2c3-9 .5-19-7.1-24.7c-4.7-3.5-9.6-6.8-14.6-9.9l-5.3-3c-5-2.8-10.2-5.3-15.6-7.6c-8.7-3.7-18.6-.9-25 6.2l-19.8 22.2c-6.8-1.1-13.8-1.7-20.9-1.7s-14.1 .6-20.9 1.7l-19.8-22.2c-6.3-7.1-16.2-9.9-25-6.2c-5.3 2.3-10.5 4.8-15.6 7.6l-5.2 3c-5.1 3-9.9 6.3-14.6 9.9c-7.6 5.7-10.1 15.7-7.1 24.7l9.3 28.2c-8.8 10.7-16 23-20.9 36.2L315.1 313c-9.3 1.9-16.7 9.1-17.8 18.5c-.8 6.7-1.2 13.5-1.2 20.4s.4 13.7 1.2 20.4c1.1 9.4 8.6 16.6 17.8 18.5l29.1 6.1c5 13.3 12.1 25.5 20.9 36.2l-9.3 28.2c-3 9-.5 19 7.1 24.7c4.7 3.5 9.5 6.8 14.6 9.8l5.4 3.1c5 2.8 10.2 5.3 15.5 7.6c8.7 3.7 18.6 .9 25-6.2l19.8-22.2c6.8 1.1 13.8 1.7 20.9 1.7s14.1-.6 20.9-1.7l19.8 22.2zM464 400a48 48 0 1 1 0-96 48 48 0 1 1 0 96z"/>
          </svg>
        </div>
        <h3>Repuestos</h3>
        <p>Solicita repuestos originales Toyota con garant&iacute;a y confianza asegurada.</p>
        <a href="<?php echo esc_url($tm_repuestos_url); ?>" target="_blank" rel="noopener noreferrer" class="service-btn">Pedir repuestos <?php echo toyota_monagas_icon('arrow-right'); ?></a>
      </div>
      <div class="service-card">
        <div class="service-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" width="48" height="48">
            <path d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-112-112c-24.2-24.2-60.6-29-89.6-14.3l-109-109V104c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L233.7 374.3c-7.8-20.9-9-43.6-3.6-65.1l-61.7-61.7L19.9 396.1zM512 144c0-10.5-1.1-20.7-3.2-30.5c-2.4-11.2-16.1-14.1-24.2-6l-63.9 63.9c-3 3-7.1 4.7-11.3 4.7H352c-8.8 0-16-7.2-16-16V102.6c0-4.2 1.7-8.3 4.7-11.3l63.9-63.9c8.1-8.1 5.2-21.8-6-24.2C388.7 1.1 378.5 0 368 0C288.5 0 224 64.5 224 144l0 .8 85.3 85.3c36-9.1 75.8 .5 104 28.7L429 274.5c49-23 83-72.8 83-130.5zM56 432a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"/>
          </svg>
        </div>
        <h3>Servicio</h3>
        <p>Mantenimiento, revisi&oacute;n y asistencia t&eacute;cnica especializada para tu Toyota.</p>
        <a href="<?php echo esc_url($tm_servicio_url); ?>" target="_blank" rel="noopener noreferrer" class="service-btn">Agendar cita <?php echo toyota_monagas_icon('arrow-right'); ?></a>
      </div>

    </div>
  </section>

  <!-- Sobre nosotros -->
  <section id="sobre-nosotros" class="sobre-nosotros">
    <header class="section-header">
      <span class="kicker">Nuestra historia</span>
      <h2>Excelencia y confianza Toyota</h2>
      <p>Comprometidos con tu movilidad y seguridad</p>
    </header>
    <div class="sobre-nosotros-container">
      <div class="sobre-nosotros-text">
        <h2>Sobre Nosotros</h2>
        <p>Con el paso del tiempo y el incremento de su actividad comercial, así como de la demanda de sus productos y servicios, la empresa tomó la decisión de trasladarse nuevamente a una sede más moderna y funcional. Actualmente, sus instalaciones se encuentran ubicadas en la Avenida Alirio Ugarte Pelayo, en el sector Tipuro, en un edificio propio identificado como "Motores Morichal". Esta sede cuenta con una amplia exhibición de vehículos de la reconocida marca Toyota, además de ofrecer al público servicio autorizado de taller, venta de repuestos y accesorios originales, consolidándose como un centro integral de atención para los usuarios de esta marca en la región.</p>
        <a href="<?php echo esc_url(home_url('/sobre-nosotros/')); ?>" class="sobre-nosotros-btn">M&aacute;s informaci&oacute;n <?php echo toyota_monagas_icon('arrow-right'); ?></a>
      </div>
      <?php
      $tm_home_gallery = array(
        array('src' => get_theme_file_uri('/assets/img/home/gallery-1.jpg'), 'alt' => 'Motores Morichal - Instalaciones'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-2.jpg'), 'alt' => 'Motores Morichal - Showroom'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-3.jpg'), 'alt' => 'Motores Morichal - Atención'),
        array('src' => get_theme_file_uri('/assets/img/home/yaris-cross-thumb.jpg'), 'alt' => 'Toyota Yaris Cross'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-4.jpg'), 'alt' => 'Motores Morichal - Servicio'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-5.jpg'), 'alt' => 'Motores Morichal - Concesionario'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-6.jpg'), 'alt' => 'Motores Morichal - Vehículos'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-7.jpg'), 'alt' => 'Motores Morichal - Experiencia'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-8.jpg'), 'alt' => 'Motores Morichal - Interior'),
        array('src' => get_theme_file_uri('/assets/img/home/gallery-9.jpg'), 'alt' => 'Motores Morichal - Venta'),
      );
      ?>
      <div class="sobre-nosotros-images gallery-grid">
        <?php foreach (array_slice($tm_home_gallery, 0, 3) as $index => $image) : ?>
          <div class="img img<?php echo esc_attr($index + 1); ?> gallery-item" data-index="<?php echo esc_attr($index); ?>" role="button" tabindex="0"
            aria-haspopup="dialog" aria-controls="gallery-lightbox" aria-label="<?php echo esc_attr('Ampliar ' . $image['alt']); ?>">
            <img src="<?php echo esc_url($image['src']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="lazy" decoding="async">
            <div class="gallery-overlay"><?php echo toyota_monagas_icon('zoom'); ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div id="gallery-lightbox" class="gallery-lightbox" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Galería de Motores Morichal" tabindex="-1">
        <button type="button" class="lightbox-close" aria-label="Cerrar galería">
          <?php echo toyota_monagas_icon('close'); ?>
        </button>

        <div class="lightbox-content">
          <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
              <?php foreach ($tm_home_gallery as $image) : ?>
                <div class="swiper-slide">
                  <img src="<?php echo esc_url($image['src']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" loading="lazy" decoding="async">
                </div>
              <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev gallery-arrow-prev" role="button" tabindex="0" aria-label="Imagen anterior"><?php echo toyota_monagas_icon('chevron-left'); ?></div>
            <div class="swiper-button-next gallery-arrow-next" role="button" tabindex="0" aria-label="Imagen siguiente"><?php echo toyota_monagas_icon('chevron-right'); ?></div>
          </div>

          <div class="gallery-thumbs swiper" aria-label="Miniaturas de la galería">
            <div class="swiper-wrapper">
              <?php foreach ($tm_home_gallery as $index => $image) : ?>
                <div class="swiper-slide">
                  <button type="button" class="gallery-thumb-button" data-gallery-index="<?php echo esc_attr($index); ?>" aria-label="<?php echo esc_attr('Mostrar ' . $image['alt']); ?>">
                    <img src="<?php echo esc_url($image['src']); ?>" alt="" loading="lazy" decoding="async">
                  </button>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Opiniones Google -->
  <section class="home-reviews">
    <header class="section-header">
      <span class="kicker">Opiniones</span>
      <h2>Lo que dicen en Google</h2>
      <p>Reseñas reales de nuestros clientes</p>
    </header>
    <?php echo do_shortcode('[trustindex no-registration=google]'); ?>
  </section>

  <!-- Blog -->
  <section id="whatsapp-mm" class="whatsapp-mm">
    <header class="section-header">
      <span class="kicker">Contacto</span>
      <h2>Solicita información por WhatsApp</h2>
      <p>Resolvemos tus dudas rápidamente</p>
    </header>
    <div class="whatsapp-wrap">
      <?php echo do_shortcode('[formulario_mmorichal]'); ?>
    </div>
  </section>

  <section id="blog-mm" class="blog-mm">
    <header class="section-header">
      <span class="kicker">Noticias</span>
      <h2>Historias y novedades Toyota</h2>
      <p>Tendencias, consejos y lanzamientos destacados</p>
    </header>
    <div class="blog-grid">
      <?php
      $blog_query = new WP_Query(array(
        'post_type'           => 'post',
        'posts_per_page'      => 3,
        'ignore_sticky_posts' => true,
      ));
      if ($blog_query->have_posts()) :
        while ($blog_query->have_posts()) : $blog_query->the_post();
      ?>
        <article class="blog-card">
          <?php
            $has_thumb = has_post_thumbnail();
            $img_alt   = get_the_title();
          ?>
          <div class="blog-img">
            <a href="<?php the_permalink(); ?>">
              <?php if ($has_thumb) {
                the_post_thumbnail('large', array('alt' => $img_alt));
              } else {
                $ph = toyota_monagas_placeholder_image_url();
              ?>
                <img src="<?php echo esc_url($ph); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async" />
              <?php } ?>
            </a>
          </div>
          <div class="blog-content">
            <span class="blog-meta"><?php echo esc_html( get_the_date() ); ?></span>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 40, '…' ) ); ?></p>
          </div>
        </article>
      <?php
        endwhile;
        wp_reset_postdata();
      else :
      ?>
        <p class="no-posts">No hay art&iacute;culos publicados a&uacute;n.</p>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php get_footer(); ?>
