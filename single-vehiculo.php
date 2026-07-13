<?php
/* Single template for CPT Vehículos - Premium White Design */
get_header();

$post_id = get_the_ID();
$sub  = get_post_meta($post_id,'veh_subtitulo',true);
$ver  = get_post_meta($post_id,'veh_version',true);
$pr   = get_post_meta($post_id,'veh_precio',true);
$fich_id = get_post_meta($post_id,'veh_ficha_id',true);
$fich = $fich_id ? wp_get_attachment_url((int)$fich_id) : get_post_meta($post_id,'veh_ficha_url',true);
$legal= get_post_meta($post_id,'veh_legal_url',true);

// Colors Data
$cols = get_post_meta($post_id,'veh_colores',true);
if (!is_array($cols)) { 
    $tmp = json_decode((string)$cols,true); 
    $cols = is_array($tmp)?$tmp:array(); 
}
$cols = array_values($cols);

// Helper to resolve full-resolution image URL
$mm_full_img = function($src){
  if (empty($src)) return '';
  if (is_numeric($src)) {
    return wp_get_attachment_image_url((int)$src, 'full') ?: '';
  }
  $maybe_id = attachment_url_to_postid((string)$src);
  if ($maybe_id) {
    $full = wp_get_attachment_image_url((int)$maybe_id, 'full');
    if ($full) return $full;
  }
  return $src;
};

// Initial State
$first_arr = $cols[0] ?? array();
$first_img = '';
$first_name = '';
$featured_img = '';
if (is_array($first_arr)) {
  if (!empty($first_arr['img_id'])) { $first_img = $mm_full_img($first_arr['img_id']); }
  elseif (!empty($first_arr['img'])) { $first_img = $mm_full_img($first_arr['img']); }
  if (!empty($first_arr['nombre'])) { $first_name = (string)$first_arr['nombre']; }
}
// Fallback image
$featured_img = has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'veh-hero') : '';
if (empty($first_img)) {
    $first_img = $featured_img;
}
$hero_img = $featured_img ?: $first_img;
if (empty($hero_img)) {
    $hero_img = 'https://picsum.photos/seed/vehiculo-' . intval($post_id) . '/1400/800';
}
$viewer_img = $first_img ?: $hero_img;

// Gallery items
$gallery_raw = get_post_meta($post_id, 'veh_galeria', true);
if (!is_array($gallery_raw)) {
  $tmp_gallery = json_decode((string)$gallery_raw, true);
  $gallery_raw = is_array($tmp_gallery) ? $tmp_gallery : array();
}
$gallery_items = array();
if (!empty($gallery_raw) && is_array($gallery_raw)) {
  $gallery_raw = array_values($gallery_raw);
  foreach($gallery_raw as $g){
    $gid  = is_array($g) ? (isset($g['id']) ? absint($g['id']) : 0) : 0;
    $gurl = is_array($g) && isset($g['url']) ? (string)$g['url'] : '';
    $alt  = is_array($g) && !empty($g['alt']) ? (string)$g['alt'] : '';

    $full = '';
    $thumb = '';
    if ($gid) {
      $full  = $mm_full_img($gid);
      $thumb = wp_get_attachment_image_url($gid, 'large') ?: $full;
      if (!$alt) {
        $alt = get_post_meta($gid, '_wp_attachment_image_alt', true) ?: get_the_title($gid);
      }
    }
    if (!$full && $gurl) {
      $full = $mm_full_img($gurl);
      $thumb = $full;
    }
    if ($full) {
      $gallery_items[] = array(
        'full'  => $full,
        'thumb' => $thumb ?: $full,
        'alt'   => $alt ?: get_the_title($post_id),
      );
    }
  }
}

// WhatsApp Link
$wa = apply_filters('mmorichal_whatsapp_number','584249090679');
$wa_base = 'https://wa.me/'.rawurlencode($wa).'?text=';

// Año y categoría para subtitle
$veh_terms = wp_get_post_terms($post_id, 'vehiculo_categoria', array('fields' => 'names'));
$veh_cat   = (!is_wp_error($veh_terms) && !empty($veh_terms)) ? $veh_terms[0] : '';
$veh_year  = '';
if ($sub && preg_match('/(20\\d{2}|19\\d{2})/', $sub, $m)) {
    $veh_year = $m[1];
}
if (empty($veh_year)) {
    $maybe_year = get_post_meta($post_id, 'veh_year', true);
    if (!empty($maybe_year)) $veh_year = $maybe_year;
}
$subtitle_parts = array_filter(array($veh_year, $veh_cat));
$subtitle_final = !empty($subtitle_parts) ? implode(' · ', $subtitle_parts) : $sub;
?>

<main id="site-main" class="vehiculo-premium-white">
  
  <!-- 1. HERO SLIDE -->
  <section class="vp-hero">
    <div class="vp-hero-bg">
        <img src="<?php echo esc_url($hero_img); ?>" alt="Hero <?php the_title(); ?>" class="vp-hero-img" id="hero-main-img">
    </div>
    <div class="vp-hero-overlay">
        <div class="container vp-hero__content">
          <div class="veh-hero__eyebrow">
            <?php if($subtitle_final): ?><p class="veh-hero__meta vp-subtitle"><?php echo esc_html($subtitle_final); ?></p><?php endif; ?>
          </div>
          <h1 class="vp-title"><?php the_title(); ?></h1>
          <h2 class="vp-heading veh-hero__lead">SUV compacto, vers&aacute;til y eficiente para la ciudad.</h2>
        </div>
    </div>
  </section>

  <!-- 3. CONFIGURATOR (Interactive) -->
  <section class="vp-configurator">
    <div class="container">
      <div class="vp-viewer vp-viewer-full">
        <figure class="vp-car-stage">
          <img src="<?php echo esc_url($viewer_img); ?>" alt="Vehicle View" id="config-car-img" class="vp-car-img animate-fade">
        </figure>
        
        <!-- Color Dots (Toyota US Style) -->
        <?php if(!empty($cols)): ?>
        <div class="vp-color-selector">
          <div class="vp-color-label">Elegir color</div>
          <div class="vp-color-buttons">
                <?php foreach($cols as $i=>$c):
                    $hex = isset($c['hex']) && $c['hex'] ? $c['hex'] : '#888';
                    $c_img = '';
                    if (!empty($c['img_id'])) {
                      $c_img = $mm_full_img($c['img_id']);
                    } elseif (!empty($c['img'])) {
                      $c_img = $mm_full_img($c['img']);
                    }
                    $c_name = !empty($c['nombre']) ? $c['nombre'] : '';
                    $is_active = ($i === 0) ? 'active is-active' : '';
                    $aria_pressed = ($i === 0) ? 'true' : 'false';
                ?>
            <button 
              class="vp-color-btn <?php echo $is_active; ?>" 
              data-img="<?php echo esc_attr($c_img); ?>" 
              data-name="<?php echo esc_attr($c_name); ?>"
              aria-pressed="<?php echo esc_attr($aria_pressed); ?>"
              aria-label="<?php echo esc_attr($c_name); ?>"
              type="button">
              <span class="color-dot" style="background-color: <?php echo esc_attr($hex); ?>;"></span>
              <span class="color-ring"></span>
            </button>
            <?php endforeach; ?>
          </div>
          <div class="vp-color-name" id="vp-color-name"><?php echo esc_html($first_name); ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php if($fich): ?>
  <!-- 4. TECHNICAL SHEET DOWNLOAD -->
  <section class="vp-ficha">
    <div class="container">
      <div class="vp-ficha-card">
        <p class="vp-ficha-kicker">Cont&aacute;ctanos</p>
        <h2 class="vp-ficha-title">Cotiza tu veh&iacute;culo con nosotros</h2>
        <p class="vp-ficha-text">Nuestros operadores est&aacute;n disponibles para atender cualquier duda sobre <?php the_title(); ?>.</p>
        <div class="vp-actions">
          <div class="vp-price-box">
            <?php if($pr): ?>
            <span class="vp-price-label">Precio estimado</span>
            <span class="vp-price-val">$<?php echo esc_html($pr); ?></span>
            <?php endif; ?>
          </div>
          
          <a href="#" class="vp-btn-primary" id="vp-cotizar-btn"
             data-wa="<?php echo esc_attr($wa_base); ?>" 
             data-modelo="<?php echo esc_attr(get_the_title()); ?>" 
             data-version="<?php echo esc_attr($ver); ?>">
             Cotizar Ahora
          </a>
          <a class="vp-btn-secondary" href="<?php echo esc_url($fich); ?>" download target="_blank" rel="noopener">
            Descargar ficha t&eacute;cnica
          </a>
        </div>
        <p class="vp-ficha-note">Consulta las especificaciones completas de <?php the_title(); ?> en la ficha oficial. Desc&aacute;rgala ahora.</p>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if(!empty($gallery_items)): ?>
  <section class="veh-gallery">
    <div class="container">
      <header class="veh-section-head">
        <div>
          <span class="veh-tag">Galer&iacute;a</span>
          <h3>Fotos de <?php the_title(); ?></h3>
          <p>Explora m&aacute;s &aacute;ngulos y detalles del modelo.</p>
        </div>
      </header>
      <div class="sobre-nosotros-images gallery-grid veh-gallery-grid">
        <?php foreach($gallery_items as $i=>$img):
          $alt = !empty($img['alt']) ? $img['alt'] : (get_the_title() . ' foto ' . ($i+1));
        ?>
        <div class="img img<?php echo ($i % 3) + 1; ?> gallery-item" data-index="<?php echo esc_attr($i); ?>">
          <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
          <div class="gallery-overlay"><i class="fas fa-search-plus"></i></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div id="gallery-lightbox" class="gallery-lightbox" role="dialog" aria-modal="true" aria-hidden="true">
      <button class="lightbox-close" aria-label="Cerrar galer&iacute;a">
        <i class="fas fa-times"></i>
      </button>
      <div class="lightbox-content">
        <div class="gallery-swiper swiper">
          <div class="swiper-wrapper">
            <?php foreach($gallery_items as $i=>$img):
              $alt = !empty($img['alt']) ? $img['alt'] : (get_the_title() . ' foto ' . ($i+1));
            ?>
            <div class="swiper-slide">
              <img src="<?php echo esc_url($img['full']); ?>" alt="<?php echo esc_attr($alt); ?>">
            </div>
            <?php endforeach; ?>
          </div>
          <div class="swiper-button-prev gallery-arrow-prev"></div>
          <div class="swiper-button-next gallery-arrow-next"></div>
        </div>

        <div class="gallery-thumbs swiper">
          <div class="swiper-wrapper">
            <?php foreach($gallery_items as $i=>$img):
              $alt = !empty($img['alt']) ? $img['alt'] : ('Miniatura ' . ($i+1));
            ?>
            <div class="swiper-slide">
              <img src="<?php echo esc_url($img['thumb']); ?>" alt="<?php echo esc_attr($alt); ?>">
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

<script>
(function() {
  if (window.vpColorInit) return;
  window.vpColorInit = true;

  const buttons = Array.from(document.querySelectorAll('.vp-color-btn'));
  const nameEl = document.getElementById('vp-color-name');
  const imgEl = document.getElementById('config-car-img') || document.querySelector('.vp-car-img');
  const cta = document.getElementById('vp-cotizar-btn');

  if (!buttons.length || !imgEl) return;

  const setActive = (btn) => {
    buttons.forEach((b) => {
      b.classList.remove('active', 'is-active');
      b.setAttribute('aria-pressed', 'false');
    });

    btn.classList.add('active', 'is-active');
    btn.setAttribute('aria-pressed', 'true');

    if (nameEl && btn.dataset.name) {
      nameEl.textContent = btn.dataset.name;
    }

    if (imgEl && btn.dataset.img) {
      const newSrc = btn.dataset.img;
      imgEl.classList.add('is-fading');

      const finish = () => imgEl.classList.remove('is-fading');
      imgEl.addEventListener('load', finish, { once: true });
      imgEl.addEventListener('error', finish, { once: true });

      if (imgEl.src !== newSrc) {
        imgEl.src = newSrc;
      } else {
        requestAnimationFrame(finish);
      }
    }
  };

  buttons.forEach((btn) => btn.addEventListener('click', () => setActive(btn)));

  if (cta) {
    cta.addEventListener('click', (e) => {
      const active = document.querySelector('.vp-color-btn.active, .vp-color-btn.is-active');
      const color = active?.dataset.name ? ' en color ' + active.dataset.name : '';
      const modelo = cta.dataset.modelo || document.title;
      const version = cta.dataset.version ? ' (' + cta.dataset.version + ')' : '';
      const text = 'Hola, quisiera cotizar el ' + modelo + version + color + '.';

      if (cta.dataset.wa) {
        e.preventDefault();
        window.open(cta.dataset.wa + encodeURIComponent(text), '_blank');
      }
    });
  }

  const initial = document.querySelector('.vp-color-btn.active, .vp-color-btn.is-active') || buttons[0];
  if (initial) setActive(initial);
})();
</script>

</main>


<?php get_footer(); ?>


