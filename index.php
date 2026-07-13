<?php
/**
 * Fallback index template required by WordPress.
 */

get_header();
?>

<main id="site-main" class="site-main">
  <div class="container">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <h1 class="entry-title"><?php the_title(); ?></h1>
          <div class="entry-content">
            <?php the_content(); ?>
          </div>
        </article>
      <?php endwhile; ?>
    <?php else : ?>
      <p><?php esc_html_e( 'No hay contenido para mostrar aún.', 'toyota-monagas' ); ?></p>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>

