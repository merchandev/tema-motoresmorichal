<?php
/*
Template Name: Blog
*/
get_header(); ?>
<main id="site-main" class="site-main">
  <div class="container">
    <h1 class="entry-title">Blog</h1>
    <div class="entry-content">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <div><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 40, "�" ) ); ?></div>
        </article>
      <?php endwhile; else : ?>
        <p>No hay artículos publicados aún.</p>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php get_footer(); ?>

