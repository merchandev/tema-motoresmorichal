<?php
/*
<footer class="site-footer">
  <div class="container footer-grid">
    <!-- Col 1: Logo & Tagline -->
    <div class="footer-col footer-brand">
      <a href="<?php echo home_url('/'); ?>" class="footer-logo">
        <img src="https://arturomerchan.com/wp-content/uploads/2025/12/BHUB_Logo_ColorVariations_Full-Color_02.svg" alt="Motores Morichal Logo" loading="lazy">
      </a>
      <p class="footer-tagline">Todo lo que te mueve</p>
      
      <div class="footer-social">
        <a href="#" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
        <a href="#" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" aria-label="Correo"><i class="fas fa-envelope"></i></a>
      </div>
    </div>

    <!-- Col 2: Motores Morichal -->
    <div class="footer-col">
      <h3>Motores Morichal</h3>
      <ul class="footer-links">
        <li><a href="https://arturomerchan.com/sobre-nosotros/">Sobre Nosotros</a></li>
        <li><a href="https://arturomerchan.com/vehiculos/">Veh&iacute;culos</a></li>
        <li><a href="https://arturomerchan.com/vehiculos-usados/">Veh&iacute;culos Usados</a></li>
        <li><a href="https://arturomerchan.com/blog/">Blog</a></li>
      </ul>
    </div>

    <!-- Col 3: Sobre Toyota -->
    <div class="footer-col">
      <h3>Sobre Toyota</h3>
      <ul class="footer-links">
        <li><a href="https://www.toyota.com.ve/terminos-legales" target="_blank" rel="noopener">T&eacute;rminos Legales</a></li>
        <li><a href="https://www.toyota.com.ve/compliance" target="_blank" rel="noopener">Compliance</a></li>
        <li><a href="https://www.toyota.com.ve/politica-de-privacidad" target="_blank" rel="noopener">Pol&iacute;tica de Privacidad</a></li>
        <li><a href="https://www.toyota.com.ve/acerca-de-toyota" target="_blank" rel="noopener">Acerca de Toyota</a></li>
      </ul>
    </div>

    <!-- Col 4: Contacto -->
    <div class="footer-col">
      <h3>Contacto</h3>
      <ul class="footer-links">
        <li><a href="https://arturomerchan.com/contactanos/">Cont&aacute;ctanos</a></li>
        <li><a href="https://arturomerchan.com/buzon-de-sugerencia">Buz&oacute;n de Sugerencia</a></li>
        <li><a href="https://arturomerchan.com/atencion-al-cliente">Atenci&oacute;n al Cliente</a></li>
        <li><a href="https://www.toyota.com.ve/contacto-recall" target="_blank" rel="noopener">Contacto Recall</a></li>
      </ul>
    </div>
  </div>
  
  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> Motores Morichal. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>
*/
?>
<?php wp_footer(); ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // FORCE RESET LAYOUT GAPS (Global & Vehiculos Page)
    function killPadding() {
        var targets = [
            document.documentElement, // HTML tag (Crucial for Admin Bar gap)
            document.body,
            document.getElementById('site-main'),
            document.querySelector('.site-main'),
            document.querySelector('.vehiculo-premium-white'),
            document.querySelector('main'),
            document.querySelector('#page')
        ];

        targets.forEach(function(el) {
            if(el) {
                // Force Reset
                el.style.setProperty('padding-top', '0', 'important');
                el.style.setProperty('margin-top', '0', 'important');
                
                // Specific fix for "Vehiculos" pages
                if (window.location.href.indexOf('vehiculos') > -1 || document.body.classList.contains('page-template-vehiculos')) {
                     el.style.setProperty('top', '0', 'important');
                     el.style.setProperty('padding-top', '0', 'important');
                     el.style.setProperty('margin-top', '-1px', 'important'); // Negative margin to force overlap if 0 fails
                     el.style.setProperty('transform', 'none', 'important');
                }
            }
        });
        
        // Target HTML specifically for the admin bar gap on Vehiculos
        if (window.location.href.indexOf('vehiculos') > -1) {
            document.documentElement.style.setProperty('margin-top', '0', 'important');
        }

        
        // Specific fix for Elementor Header Wrapper
        var header = document.querySelector('.elementor-location-header');
        if(header) {
             header.style.setProperty('margin-bottom', '0', 'important');
             header.style.setProperty('margin-top', '0', 'important');
             header.style.setProperty('top', '0', 'important');
        }
    }

    // Execution Strategy
    killPadding();
    window.addEventListener('resize', killPadding);
    window.addEventListener('load', killPadding);
    setTimeout(killPadding, 100);
    setTimeout(killPadding, 500);
    setTimeout(killPadding, 1000); // Late catch for slow scripts
});
</script>
</body>
</html>
