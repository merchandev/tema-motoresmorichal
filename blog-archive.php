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
      <form id="blog-search" class="blog-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"
        data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
        data-nonce="<?php echo esc_attr(wp_create_nonce('toyota_front_nonce')); ?>">
        <label for="blog-search-input" class="sr-only">Buscar artículos</label>
        <input id="blog-search-input" name="s" type="search" maxlength="100" placeholder="Buscar artículos…" autocomplete="off"
          aria-controls="blog-search-suggest" />
        <button type="submit" class="sr-only">Buscar</button>
        <div id="blog-search-suggest" class="blog-search-suggest" hidden aria-live="polite"></div>
      </form>
    </header>

    <?php
    // Logical page 1 contains one featured post plus nine cards. Every
    // subsequent page contains ten cards and is also reachable without JS.
    $ppp = 10;
    $total_posts = (int) wp_count_posts('post')->publish;
    $remaining_after_initial = max(0, $total_posts - 10);
    $max_logical_page = max(1, 1 + (int) ceil($remaining_after_initial / $ppp));
    $blog_page = min(
      max(1, absint(toyota_monagas_request_scalar($_GET, 'blog_page', '1'))),
      $max_logical_page
    );

    // The latest post is featured only on the first logical page.
    if ($blog_page === 1) :
    $featured_q = new WP_Query(array(
      'post_type'      => 'post',
      'post_status'    => 'publish',
      'posts_per_page' => 1,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'ignore_sticky_posts' => true,
    ));
    if ($featured_q->have_posts()) : $featured_q->the_post();
      $img_alt = get_the_title();
      $has_thumb = has_post_thumbnail();
      ?>
      <article class="blog-featured">
        <div class="bf-link">
          <figure class="bf-media">
            <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
              <?php if ($has_thumb) { the_post_thumbnail('large', array('alt'=>$img_alt, 'fetchpriority'=>'high')); } else { ?>
                <img src="<?php echo esc_url(toyota_monagas_placeholder_image_url()); ?>" alt="<?php echo esc_attr($img_alt); ?>" fetchpriority="high" decoding="async" />
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
    <?php wp_reset_postdata(); endif; endif; ?>

    <?php
      $list_limit = $blog_page === 1 ? 9 : $ppp;
      $offset = $blog_page === 1 ? 1 : 10 + (($blog_page - 2) * $ppp);
      $list_q = new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $list_limit,
        'offset'         => $offset,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'ignore_sticky_posts' => true,
      ));
    ?>

    <section class="blog-list" id="blog-list" data-page="<?php echo esc_attr($blog_page + 1); ?>" data-max="<?php echo esc_attr($max_logical_page); ?>"
      data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
      data-nonce="<?php echo esc_attr(wp_create_nonce('toyota_front_nonce')); ?>" aria-live="polite">
      <?php if ($list_q->have_posts()) : while ($list_q->have_posts()) : $list_q->the_post();
        $img_alt = get_the_title(); $has_thumb = has_post_thumbnail(); ?>
        <article class="blog-mini">
          <a class="blog-mini-link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
            <figure class="blog-mini-img">
              <?php if ($has_thumb) { the_post_thumbnail('large', array('alt'=>$img_alt, 'loading'=>'lazy', 'decoding'=>'async')); } else { ?>
                <img src="<?php echo esc_url(toyota_monagas_placeholder_image_url()); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async" />
              <?php } ?>
            </figure>
            <div class="blog-mini-body">
              <h2 class="blog-mini-title"><?php the_title(); ?></h2>
              <p class="blog-mini-excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
            </div>
          </a>
        </article>
      <?php endwhile; wp_reset_postdata(); else: ?>
        <p>No hay artículos para mostrar.</p>
      <?php endif; ?>
    </section>

    <div id="blog-sentinel" aria-hidden="true" style="height:1px"></div>

    <?php
      $blog_base_url = get_permalink(get_queried_object_id());
      if (!$blog_base_url) $blog_base_url = home_url('/blog/');
      $previous_url = $blog_page > 2
        ? add_query_arg('blog_page', $blog_page - 1, $blog_base_url)
        : $blog_base_url;
      $next_url = add_query_arg('blog_page', $blog_page + 1, $blog_base_url);
    ?>
    <?php if ($max_logical_page > 1) : ?>
      <nav class="blog-pagination" aria-label="Paginación de artículos">
        <?php if ($blog_page > 1) : ?>
          <a class="tm-btn tm-btn--secondary" href="<?php echo esc_url($previous_url); ?>" rel="prev">Artículos anteriores</a>
        <?php endif; ?>
        <span aria-live="polite">Página <?php echo esc_html($blog_page); ?> de <?php echo esc_html($max_logical_page); ?></span>
        <?php if ($blog_page < $max_logical_page) : ?>
          <a class="tm-btn tm-btn--primary" href="<?php echo esc_url($next_url); ?>" rel="next">Más artículos</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
