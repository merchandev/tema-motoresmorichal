<?php
/**
 * Template Name: Single Post
 * Description: Template dedicated to single blog posts.
 */

get_header();
?>

<main id="site-main" class="site-main single-post-main">
  <div class="container single-post-container">
    <?php if ( have_posts() ) : ?>
      <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          
          <!-- Post Header -->
          <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <div class="entry-meta">
               <span class="posted-date"><?php echo get_the_date(); ?></span>
            </div>
          </header>

          <!-- Post Content -->
          <div class="entry-content">
            <?php the_content(); ?>
          </div>

        </article>
      <?php endwhile; ?>
    <?php else : ?>
      <p><?php esc_html_e( 'No hay contenido para mostrar.', 'toyota-monagas' ); ?></p>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>
