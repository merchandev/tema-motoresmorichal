<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="tm-skip-link" href="#site-main"><?php esc_html_e('Saltar al contenido', 'toyota-monagas'); ?></a>

<?php
$toyota_has_elementor_header = function_exists('elementor_theme_do_location')
    && elementor_theme_do_location('header');

if (!$toyota_has_elementor_header) :
?>
  <header class="site-header luxury-nav" role="banner">
    <div class="nav-wrapper">
      <div class="nav-logo site-logo">
        <?php if (has_custom_logo()) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <img
              src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/Logo_Toyota_MOTORES MORICHAL_CONSECIONARIO_TOYOTA_MONAGAS_MATURIN_VENEZUELA.webp'); ?>"
              alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
              width="300"
              height="100"
              decoding="async"
              fetchpriority="high"
            >
          </a>
        <?php endif; ?>
      </div>

      <button
        class="nav-toggle"
        type="button"
        aria-controls="site-menu"
        aria-expanded="false"
        aria-label="<?php esc_attr_e('Abrir menú principal', 'toyota-monagas'); ?>"
      >
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </button>

      <nav class="nav-menu" aria-label="<?php esc_attr_e('Menú principal', 'toyota-monagas'); ?>">
        <?php if (has_nav_menu('menu-principal')) : ?>
          <?php
          wp_nav_menu(array(
              'theme_location' => 'menu-principal',
              'container'      => false,
              'menu_class'     => 'menu-list',
              'menu_id'        => 'site-menu',
              'fallback_cb'    => false,
              'walker'         => class_exists('Toyota_Walker_Nav_Menu') ? new Toyota_Walker_Nav_Menu() : '',
          ));
          ?>
        <?php else : ?>
          <ul class="menu-list" id="site-menu">
            <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Inicio', 'toyota-monagas'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/vehiculos/')); ?>"><?php esc_html_e('Vehículos', 'toyota-monagas'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/vehiculos-usados/')); ?>"><?php esc_html_e('Usados', 'toyota-monagas'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/sobre-nosotros/')); ?>"><?php esc_html_e('Nosotros', 'toyota-monagas'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Blog', 'toyota-monagas'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/contactanos/')); ?>"><?php esc_html_e('Contáctanos', 'toyota-monagas'); ?></a></li>
          </ul>
        <?php endif; ?>
      </nav>
    </div>
  </header>
<?php endif; ?>
