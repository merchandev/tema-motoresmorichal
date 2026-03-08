<?php
/*
Template Name: Blog (Archivo Mejorado)
*/
get_header(); ?>

<main id="site-main" class="site-main blog-archive">
  <div class="container">
    <nav class="breadcrumbs" aria-label="Ruta de navegación">
      <ol>
        <li><a href="<?php echo esc_url( home_url('/') ); ?>">Inicio</a></li>
        <li aria-current="page">Blog</li>
      </ol>
    </nav>

    <header class="section-header">
      <span class="kicker">Noticias</span>
      <h1 class="entry-title">Historias y novedades Toyota</h1>
      <p>Lo más reciente de Motores Morichal</p>
      <div id="blog-search" class="blog-search" role="search">
        <label for="blog-search-input" class="sr-only">Buscar artículos</label>
        <input id="blog-search-input" type="search" placeholder="Buscar artículos…" autocomplete="off" />
        <div id="blog-search-suggest" class="blog-search-suggest" hidden aria-live="polite"></div>
      </div>
    </header>

    <?php
    // Featured: último post publicado
    $featured_q = new WP_Query(array(
      'post_type'      => 'post',
      'post_status'    => 'publish',
      'posts_per_page' => 1,
      'orderby'        => 'date',
      'order'          => 'DESC',
    ));
    if ($featured_q->have_posts()) : $featured_q->the_post();
      $img_alt = esc_attr(get_the_title());
      $has_thumb = has_post_thumbnail();
      ?>
      <article class="blog-featured">
        <div class="bf-link">
          <figure class="bf-media">
            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
              <?php if ($has_thumb) { the_post_thumbnail('large', array('alt'=>$img_alt)); } else { ?>
                <img src="https://picsum.photos/seed/<?php echo (int)get_the_ID(); ?>/1200/650" alt="<?php echo $img_alt; ?>" />
              <?php } ?>
            </a>
          </figure>
          <div class="bf-body">
            <h2 class="bf-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p class="bf-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 40, '…' ) ); ?></p>
            <a class="bf-cta toyota-btn" href="<?php the_permalink(); ?>">Leer más <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" width="16" height="16" style="display:inline-block;vertical-align:middle;"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg></a>
          </div>
        </div>
      </article>
    <?php wp_reset_postdata(); endif; ?>

    <?php
      // Inicial: 9 posts siguientes (para completar 10 junto al destacado)
      $ppp = 10; $offset = 1;
      $list_q = new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $ppp - 1,
        'offset'         => $offset,
        'orderby'        => 'date',
        'order'          => 'DESC',
      ));
    ?>

    <section class="blog-list" id="blog-list" data-page="2" data-max="<?php echo esc_attr( (int) ceil( max(0, (int)wp_count_posts('post')->publish - 1) / $ppp ) ); ?>">
      <?php if ($list_q->have_posts()) : while ($list_q->have_posts()) : $list_q->the_post();
        $img_alt = esc_attr(get_the_title()); $has_thumb = has_post_thumbnail(); ?>
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
      <?php endwhile; wp_reset_postdata(); else: ?>
        <p>No hay artículos para mostrar.</p>
      <?php endif; ?>
    </section>

    <div id="blog-sentinel" aria-hidden="true" style="height:1px"></div>
  </div>
</main>

<?php get_footer(); ?>
