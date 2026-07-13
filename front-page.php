<?php get_header(); ?>

<?php
// Helper para renderizar video o imagen según la extensión
function tm_render_slide_media($url, $is_mobile = false) {
    if (empty($url)) return '';
    $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
    $class = $is_mobile ? 'cs-video-bg mobile-media' : 'cs-video-bg desktop-media';
    if (in_array(strtolower($ext), ['mp4', 'webm', 'ogg'])) {
        return '<video class="'.$class.'" muted playsinline preload="metadata" data-src="'.esc_url($url).'" crossorigin="anonymous"></video>';
    } else {
        return '<img class="'.$class.'" src="'.esc_url($url).'" alt="Toyota Slide" style="object-fit: cover; width: 100%; height: 100%;">';
    }
}
?>

<main id="site-main" class="vehiculo-premium-white">
  <!-- Slider de videos -->
  <div id="custom-slider" class="custom-slider swiper" role="region" aria-label="Slider principal Toyota">
    <div class="swiper-wrapper">
      <!-- Slide 0: Nuevo video "Trabajamos para ti" -->
      <div class="swiper-slide cs-slide" data-index="0" data-type="video">
        <?php 
          $s1_desk = get_theme_mod('home_video_1_desktop', 'https://mmorichal.com/wp-content/uploads/2026/03/toyota-monagas-maturin-venezuela-actulizacion-de-edificio.mp4');
          $s1_mob  = get_theme_mod('home_video_1_mobile', $s1_desk);
          echo tm_render_slide_media($s1_desk, false);
          echo tm_render_slide_media($s1_mob, true);
        ?>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Visita nuestra sede</h2>
          <p>Av. Alirio Ugarte Pelayo, Matur&iacute;n, Monagas, Venezuela</p>
          <a href="https://mmorichal.com/blog/" class="cs-btn-slide">Con&oacute;cenos</a>
        </div>
      </div>

      <!-- Slide 1: Bienvenidos Motores Morichal (video 1) -->
      <div class="swiper-slide cs-slide" data-index="1" data-type="video">
        <?php 
          $s2_desk = get_theme_mod('home_video_2_desktop', get_template_directory_uri().'/assets/video/home/video-fortuner.mp4');
          $s2_mob  = get_theme_mod('home_video_2_mobile', $s2_desk);
          echo tm_render_slide_media($s2_desk, false);
          echo tm_render_slide_media($s2_mob, true);
        ?>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Bienvenidos a Motores Morichal</h2>
          <p>Tu concesionario Oficial Toyota en Matur&iacute;n</p>
          <a href="#" class="cs-btn-slide">Ver veh&iacute;culos</a>
        </div>
      </div>

      <!-- Slide 2: Toyota APP -->
      <div class="swiper-slide cs-slide" data-index="2" data-type="image">
        <?php 
          $s3_desk = get_theme_mod('home_video_3_desktop', get_template_directory_uri().'/assets/img/home/banner-app-desktop.jpg');
          $s3_mob  = get_theme_mod('home_video_3_mobile', get_template_directory_uri().'/assets/img/home/banner-app-mobile.png');
          echo tm_render_slide_media($s3_desk, false);
          echo tm_render_slide_media($s3_mob, true);
        ?>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Toyota APP</h2>
          <p>Toda la informaci&oacute;n de tu veh&iacute;culo al alcance de tu mano.</p>
          <a href="https://www.toyota.com.ve/mi-toyota/app-toyota" class="cs-btn-slide" target="_blank">M&aacute;s informaci&oacute;n</a>
        </div>
      </div>

      <!-- Slide 3: Yaris Cross (video 2) -->
      <div class="swiper-slide cs-slide" data-index="3" data-type="video">
        <?php 
          $s4_desk = get_theme_mod('home_video_4_desktop', get_template_directory_uri().'/assets/video/home/video-yaris.mp4');
          $s4_mob  = get_theme_mod('home_video_4_mobile', $s4_desk);
          echo tm_render_slide_media($s4_desk, false);
          echo tm_render_slide_media($s4_mob, true);
        ?>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Nuevo Yaris Cross</h2>
          <p>Nuevo dise&ntilde;o moderno y sofisticado</p>
          <a href="https://mmorichal.com/vehiculo/toyota-yaris-cross-2025/" class="cs-btn-slide">Explorar</a>
        </div>
      </div>

      <!-- Slide 4: Corolla 2025 (video 3) -->
      <div class="swiper-slide cs-slide" data-index="4" data-type="video">
        <?php 
          $s5_desk = get_theme_mod('home_video_5_desktop', get_template_directory_uri().'/assets/video/home/video-corolla.mp4');
          $s5_mob  = get_theme_mod('home_video_5_mobile', $s5_desk);
          echo tm_render_slide_media($s5_desk, false);
          echo tm_render_slide_media($s5_mob, true);
        ?>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Un legado de confianza que se mide en d&eacute;cadas</h2>
          <a href="https://mmorichal.com/sobre-nosotros/" class="cs-btn-slide">Descubrir</a>
        </div>
      </div>

      <!-- Slide 5: AGYA 2025 (video 4) -->
      <div class="swiper-slide cs-slide" data-index="5" data-type="video">
        <?php 
          $s6_desk = get_theme_mod('home_video_6_desktop', 'https://mmorichal.com/wp-content/uploads/2025/09/AGYA-TOYOTA-MONAGAS.mp4');
          $s6_mob  = get_theme_mod('home_video_6_mobile', $s6_desk);
          echo tm_render_slide_media($s6_desk, false);
          echo tm_render_slide_media($s6_mob, true);
        ?>
        <div class="cs-slide-content cs-left animate-in">
          <h2>AGYA 2025</h2>
          <p>El Toyota Agya est&aacute; dise&ntilde;ado priorizando la ergonom&iacute;a, ofreciendo un amplio espacio interior y una posici&oacute;n de conducci&oacute;n enfocada en comodidad que te permitir&aacute; hacer los viajes que necesites sin sentirte fatigado.</p>
          <a href="https://mmorichal.com/vehiculo/toyota-agya-2025/" class="cs-btn-slide">Con&oacute;celo</a>
        </div>
      </div>

      <!-- Slide 6: Recall Campaign -->
      <div class="swiper-slide cs-slide" data-index="6" data-type="image">
        <?php 
          $s7_desk = get_theme_mod('home_video_7_desktop', get_template_directory_uri().'/assets/img/home/banner-recall-desktop.png');
          $s7_mob  = get_theme_mod('home_video_7_mobile', get_template_directory_uri().'/assets/img/home/banner-recall-mobile.jpg');
          echo tm_render_slide_media($s7_desk, false);
          echo tm_render_slide_media($s7_mob, true);
        ?>
        <div class="cs-slide-content cs-left animate-in">
          <h2>TU VIDA EST&Aacute; EN RIESGO</h2>
          <p>Verifica si tu Toyota est&aacute; en Campa&ntilde;a.</p>
          <a href="https://www.toyota.com.ve/mi-toyota/servicios/recall" class="cs-btn-slide" target="_blank">Verifica aqu&iacute;</a>
        </div>
      </div>

    </div>

    <!-- Flechas -->
    <div class="cs-swiper-button-prev swiper-button-prev" aria-label="Anterior"></div>
    <div class="cs-swiper-button-next swiper-button-next" aria-label="Siguiente"></div>

    <!-- Barras de progreso -->
    <div class="cs-progress-bars">
      <div class="cs-progress-bar" data-index="0"><span></span></div>
      <div class="cs-progress-bar" data-index="1"><span></span></div>
      <div class="cs-progress-bar" data-index="2"><span></span></div>
      <div class="cs-progress-bar" data-index="3"><span></span></div>
      <div class="cs-progress-bar" data-index="4"><span></span></div>
      <div class="cs-progress-bar" data-index="5"><span></span></div>
      <div class="cs-progress-bar" data-index="6"><span></span></div>
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
      <div class="toyota-tabs" role="tablist">
        <button class="toyota-tab is-active" data-cat="cars" aria-selected="true">Camioneta</button>
        <button class="toyota-tab" data-cat="trucks" aria-selected="false">Pasajero</button>
        <button class="toyota-tab" data-cat="crossovers" aria-selected="false">Pick Ups</button>
        <button class="toyota-tab" data-cat="electrified" aria-selected="false">Comercial</button>
        <span class="toyota-tab-indicator"></span>
      </div>
    </nav>

    <div class="toyota-slider swiper">
      <div class="swiper-wrapper"></div>
      <div class="toyota-arrow swiper-button-prev"></div>
      <div class="toyota-arrow swiper-button-next"></div>
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
      // Query vehicles (published)
      $veh_q = new WP_Query(array(
          'post_type' => 'vehiculo',
          'posts_per_page' => -1,
          'post_status' => 'publish',
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
          if (!$thumb) $thumb = 'https://picsum.photos/seed/'.intval($post_id).'/800/600';
          // Determine category
          $terms = wp_get_post_terms($post_id, 'vehiculo_categoria', array('fields'=>'names'));
          $term_name = (!empty($terms) && is_array($terms)) ? $terms[0] : '';
          $data_cat = isset($cat_map[$term_name]) ? $cat_map[$term_name] : 'cars';
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
              <span class="toyota-year"><?php echo esc_html(get_post_meta($post_id,'veh_subtitulo',true)?:''); ?></span>
              <h3><?php echo esc_html($title); ?></h3>
              <p><?php echo wp_kses_post( wp_trim_words( $content, 20, '...' ) ); ?></p>
            </header>
            <div class="toyota-buttons">
              <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="toyota-btn">M&aacute;s informaci&oacute;n <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
              <span class="toyota-contact">
                <a href="#" class="js-whatsapp-link" 
                   data-wa="https://wa.me/584249090679?text=" 
                   data-modelo="<?php echo esc_attr($title); ?>" 
                   data-version="">Cont&aacute;ctanos &gt;</a>
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
        <a href="https://www.toyota.com.ve/mi-toyota/accesorios" class="accesorio-btn" target="_blank" rel="noopener">Explorar Accesorios <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
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
        <a href="/vehiculos/" class="service-btn">Explorar <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
      </div>
      <div class="service-card">
        <div class="service-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" width="48" height="48">
            <path d="M308.5 135.3c7.1-6.3 9.9-16.2 6.2-25c-2.3-5.3-4.8-10.5-7.6-15.5L304 89.4c-3-5-6.3-9.9-9.8-14.6c-5.7-7.6-15.7-10.1-24.7-7.1l-28.2 9.3c-10.7-8.8-23-16-36.2-20.9L199 27.1c-1.9-9.3-9.1-16.7-18.5-17.8C173.9 8.4 167.2 8 160.4 8h-.7c-6.8 0-13.5 .4-20.1 1.2c-9.4 1.1-16.6 8.6-18.5 17.8L115 56.1c-13.3 5-25.5 12.1-36.2 20.9L50.5 67.8c-9-3-19-.5-24.7 7.1c-3.5 4.7-6.8 9.6-9.9 14.6l-3 5.3c-2.8 5-5.3 10.2-7.6 15.6c-3.7 8.7-.9 18.6 6.2 25l22.2 19.8C32.6 161.9 32 168.9 32 176s.6 14.1 1.7 20.9L11.5 216.7c-7.1 6.3-9.9 16.2-6.2 25c2.3 5.3 4.8 10.5 7.6 15.6l3 5.2c3 5.1 6.3 9.9 9.9 14.6c5.7 7.6 15.7 10.1 24.7 7.1l28.2-9.3c10.7 8.8 23 16 36.2 20.9l6.1 29.1c1.9 9.3 9.1 16.7 18.5 17.8c6.7 .8 13.5 1.2 20.4 1.2s13.7-.4 20.4-1.2c9.4-1.1 16.6-8.6 18.5-17.8l6.1-29.1c13.3-5 25.5-12.1 36.2-20.9l28.2 9.3c9 3 19 .5 24.7-7.1c3.5-4.7 6.8-9.5 9.8-14.6l3.1-5.4c2.8-5 5.3-10.2 7.6-15.5c3.7-8.7 .9-18.6-6.2-25l-22.2-19.8c1.1-6.8 1.7-13.8 1.7-20.9s-.6-14.1-1.7-20.9l22.2-19.8zM112 176a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM504.7 500.5c6.3 7.1 16.2 9.9 25 6.2c5.3-2.3 10.5-4.8 15.5-7.6l5.4-3.1c5-3 9.9-6.3 14.6-9.8c7.6-5.7 10.1-15.7 7.1-24.7l-9.3-28.2c8.8-10.7 16-23 20.9-36.2l29.1-6.1c9.3-1.9 16.7-9.1 17.8-18.5c.8-6.7 1.2-13.5 1.2-20.4s-.4-13.7-1.2-20.4c-1.1-9.4-8.6-16.6-17.8-18.5L583.9 307c-5-13.3-12.1-25.5-20.9-36.2l9.3-28.2c3-9 .5-19-7.1-24.7c-4.7-3.5-9.6-6.8-14.6-9.9l-5.3-3c-5-2.8-10.2-5.3-15.6-7.6c-8.7-3.7-18.6-.9-25 6.2l-19.8 22.2c-6.8-1.1-13.8-1.7-20.9-1.7s-14.1 .6-20.9 1.7l-19.8-22.2c-6.3-7.1-16.2-9.9-25-6.2c-5.3 2.3-10.5 4.8-15.6 7.6l-5.2 3c-5.1 3-9.9 6.3-14.6 9.9c-7.6 5.7-10.1 15.7-7.1 24.7l9.3 28.2c-8.8 10.7-16 23-20.9 36.2L315.1 313c-9.3 1.9-16.7 9.1-17.8 18.5c-.8 6.7-1.2 13.5-1.2 20.4s.4 13.7 1.2 20.4c1.1 9.4 8.6 16.6 17.8 18.5l29.1 6.1c5 13.3 12.1 25.5 20.9 36.2l-9.3 28.2c-3 9-.5 19 7.1 24.7c4.7 3.5 9.5 6.8 14.6 9.8l5.4 3.1c5 2.8 10.2 5.3 15.5 7.6c8.7 3.7 18.6 .9 25-6.2l19.8-22.2c6.8 1.1 13.8 1.7 20.9 1.7s14.1-.6 20.9-1.7l19.8 22.2zM464 400a48 48 0 1 1 0-96 48 48 0 1 1 0 96z"/>
          </svg>
        </div>
        <h3>Repuestos</h3>
        <p>Solicita repuestos originales Toyota con garant&iacute;a y confianza asegurada.</p>
        <a href="https://wa.me/584249090679?text=Hola%2C%20necesito%20solicitar%20repuestos%20originales%20Toyota.%20¿Podrían%20ayudarme%3F" target="_blank" rel="noopener" class="service-btn">Pedir repuestos <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
      </div>
      <div class="service-card">
        <div class="service-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" width="48" height="48">
            <path d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-112-112c-24.2-24.2-60.6-29-89.6-14.3l-109-109V104c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L233.7 374.3c-7.8-20.9-9-43.6-3.6-65.1l-61.7-61.7L19.9 396.1zM512 144c0-10.5-1.1-20.7-3.2-30.5c-2.4-11.2-16.1-14.1-24.2-6l-63.9 63.9c-3 3-7.1 4.7-11.3 4.7H352c-8.8 0-16-7.2-16-16V102.6c0-4.2 1.7-8.3 4.7-11.3l63.9-63.9c8.1-8.1 5.2-21.8-6-24.2C388.7 1.1 378.5 0 368 0C288.5 0 224 64.5 224 144l0 .8 85.3 85.3c36-9.1 75.8 .5 104 28.7L429 274.5c49-23 83-72.8 83-130.5zM56 432a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"/>
          </svg>
        </div>
        <h3>Servicio</h3>
        <p>Mantenimiento, revisi&oacute;n y asistencia t&eacute;cnica especializada para tu Toyota.</p>
        <a href="https://wa.me/584249090679?text=Hola%2C%20me%20gustaría%20agendar%20una%20cita%20para%20el%20servicio%20de%20mi%20Toyota.%20¿Cuál%20es%20la%20disponibilidad%3F" target="_blank" rel="noopener" class="service-btn">Agendar cita <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
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
        <a href="/sobre-nosotros/" class="sobre-nosotros-btn">M&aacute;s informaci&oacute;n <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
      </div>
      <div class="sobre-nosotros-images gallery-grid">
        <div class="img img1 gallery-item" data-index="0">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-1.jpg" alt="Motores Morichal - Instalaciones" loading="lazy">
          <div class="gallery-overlay">
            <i class="fas fa-search-plus"></i>
          </div>
        </div>
        <div class="img img2 gallery-item" data-index="1">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-2.jpg" alt="Motores Morichal - Showroom" loading="lazy">
          <div class="gallery-overlay">
            <i class="fas fa-search-plus"></i>
          </div>
        </div>
        <div class="img img3 gallery-item" data-index="2">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-3.jpg" alt="Motores Morichal - Atención" loading="lazy">
          <div class="gallery-overlay">
            <i class="fas fa-search-plus"></i>
          </div>
        </div>
      </div>

      <!-- Lightbox Modal -->
      <div id="gallery-lightbox" class="gallery-lightbox" role="dialog" aria-modal="true" aria-hidden="true">
        <button class="lightbox-close" aria-label="Cerrar galería">
          <i class="fas fa-times"></i>
        </button>
        
        <div class="lightbox-content">
          <!-- Main Gallery Swiper -->
          <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-1.jpg" alt="Motores Morichal - Instalaciones">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-2.jpg" alt="Motores Morichal - Showroom">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-3.jpg" alt="Motores Morichal - Atención">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/yaris-cross-thumb.png" alt="Yaris Cross" decoding="async" loading="lazy" referrerpolicy="no-referrer">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-4.jpg" alt="Motores Morichal - Servicio">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-5.jpg" alt="Motores Morichal - Concesionario">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-6.jpg" alt="Motores Morichal - Vehículos">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-7.jpg" alt="Motores Morichal - Experiencia">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-8.jpg" alt="Motores Morichal - Interior">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-9.jpg" alt="Motores Morichal - Venta">
              </div>
            </div>
            
            <!-- Navigation Arrows -->
            <div class="swiper-button-prev gallery-arrow-prev"></div>
            <div class="swiper-button-next gallery-arrow-next"></div>
          </div>

          <!-- Thumbnail Gallery -->
          <div class="gallery-thumbs swiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-1.jpg" alt="Miniatura 1">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-2.jpg" alt="Miniatura 2">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-3.jpg" alt="Miniatura 3">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-4.jpg" alt="Miniatura 4">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0348-scaled.jpg" alt="Miniatura 5">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-6.jpg" alt="Miniatura 6">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-6.jpg" alt="Miniatura 7">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-7.jpg" alt="Miniatura 8">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-8.jpg" alt="Miniatura 9">
              </div>
              <div class="swiper-slide">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/home/gallery-9.jpg" alt="Miniatura 10">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php /* Productos - Sección ocultada
  <section id="productos-mm" class="productos-mm">
    <header class="section-header">
      <span class="kicker">Repuestos</span>
      <h2>Originales Toyota al mejor precio</h2>
      <p>Calidad garantizada para el rendimiento de tu vehículo</p>
    </header>
    <div class="productos-grid">
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-filter"></i></div>
        <h3>Filtro de Aceite</h3>
        <p>Filtro original Toyota para mantener el motor en &oacute;ptimas condiciones.</p>
        
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
      </div>
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-car-battery"></i></div>
        <h3>Bater&iacute;a Original</h3>
        <p>Bater&iacute;a Toyota de alto rendimiento y larga duraci&oacute;n.</p>
        
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
      </div>
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-car-side"></i></div>
        <h3>Pastillas de Freno</h3>
        <p>Juego de pastillas delanteras originales Toyota, m&aacute;xima seguridad.</p>
        
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
      </div>
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-wind"></i></div>
        <h3>Filtro de Aire</h3>
        <p>Filtro de aire Toyota para mayor eficiencia y consumo optimizado.</p>
        
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
      </div>
    </div>
  </section>
  */ ?>

  <!-- Opiniones Google -->
  <section class="home-reviews">
    <header class="section-header">
      <span class="kicker">Opiniones</span>
      <h2 style="color:#111827;">Lo que dicen en Google</h2>
      <p style="color:#111827;">Reseñas reales de nuestros clientes</p>
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
            $img_alt   = esc_attr(get_the_title());
          ?>
          <div class="blog-img">
            <a href="<?php the_permalink(); ?>">
              <?php if ($has_thumb) {
                the_post_thumbnail('large', array('alt' => $img_alt));
              } else {
                $seed = get_the_ID();
                $ph   = sprintf('https://picsum.photos/seed/%d/600/400', (int)$seed);
              ?>
                <img src="<?php echo esc_url($ph); ?>" alt="<?php echo $img_alt; ?>" loading="lazy" decoding="async" />
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.body.addEventListener('click', function(e) {
    if (e.target && e.target.closest('.js-whatsapp-link')) {
      e.preventDefault();
      var btn = e.target.closest('.js-whatsapp-link');
      var modelo = btn.getAttribute('data-modelo') || '';
      var version = btn.getAttribute('data-version') ? ' (' + btn.getAttribute('data-version') + ')' : '';
      var text = 'Hola, quisiera cotizar el ' + modelo + version + '.';
      var waBase = btn.getAttribute('data-wa');
      
      if (waBase) {
         window.open(waBase + encodeURIComponent(text), '_blank');
      }
    }
  });
});
</script>

<?php get_footer(); ?>





