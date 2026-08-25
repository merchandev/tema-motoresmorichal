<?php
$toyota_privacy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
$toyota_has_elementor_footer = function_exists('elementor_theme_do_location')
    && elementor_theme_do_location('footer');

if (!$toyota_has_elementor_footer) :
?>
  <footer class="site-footer" role="contentinfo">
    <div class="container footer-grid">
      <div class="footer-col footer-brand">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo" rel="home">
          <img
            src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/Logo_Toyota_MOTORES MORICHAL_CONSECIONARIO_TOYOTA_MONAGAS_MATURIN_VENEZUELA.webp'); ?>"
            alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
            width="300"
            height="100"
            loading="lazy"
            decoding="async"
          >
        </a>
        <p class="footer-tagline"><?php esc_html_e('Todo lo que te mueve', 'toyota-monagas'); ?></p>
      </div>

      <div class="footer-col">
        <h2><?php esc_html_e('Motores Morichal', 'toyota-monagas'); ?></h2>
        <ul class="footer-links">
          <li><a href="<?php echo esc_url(home_url('/sobre-nosotros/')); ?>"><?php esc_html_e('Sobre nosotros', 'toyota-monagas'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/vehiculos/')); ?>"><?php esc_html_e('Vehículos nuevos', 'toyota-monagas'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/vehiculos-usados/')); ?>"><?php esc_html_e('Vehículos usados', 'toyota-monagas'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Blog', 'toyota-monagas'); ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h2><?php esc_html_e('Atención', 'toyota-monagas'); ?></h2>
        <ul class="footer-links">
          <li><a href="<?php echo esc_url(home_url('/contactanos/')); ?>"><?php esc_html_e('Contáctanos', 'toyota-monagas'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/atencion-al-cliente/')); ?>"><?php esc_html_e('Atención al cliente', 'toyota-monagas'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/buzon-de-sugerencia/')); ?>"><?php esc_html_e('Buzón de sugerencias', 'toyota-monagas'); ?></a></li>
          <li><a href="https://www.toyota.com.ve/contacto-recall" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Contacto Recall', 'toyota-monagas'); ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h2><?php esc_html_e('Información legal', 'toyota-monagas'); ?></h2>
        <ul class="footer-links">
          <li><a href="https://www.toyota.com.ve/terminos-legales" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Términos legales', 'toyota-monagas'); ?></a></li>
          <li><a href="https://www.toyota.com.ve/compliance" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Compliance', 'toyota-monagas'); ?></a></li>
          <?php if ($toyota_privacy_url) : ?>
            <li><a href="<?php echo esc_url($toyota_privacy_url); ?>"><?php esc_html_e('Política de privacidad', 'toyota-monagas'); ?></a></li>
          <?php else : ?>
            <li><a href="https://www.toyota.com.ve/politica-de-privacidad" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Política de privacidad', 'toyota-monagas'); ?></a></li>
          <?php endif; ?>
          <li><a href="https://www.toyota.com.ve/acerca-de-toyota" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Acerca de Toyota', 'toyota-monagas'); ?></a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <p>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>. <?php esc_html_e('Todos los derechos reservados.', 'toyota-monagas'); ?></p>
      </div>
    </div>
  </footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
