<?php
/*
Template Name: Sobre Nosotros
*/
get_header(); ?>

<main id="site-main" class="about-page">
  <!-- Hero con collage -->
  <section class="about-hero scroll-section is-visible" id="hero-nosotros">
    <div class="container about-hero-grid">
      <div class="about-hero-copy fade-item">
        <p class="about-pill">Concesionario oficial Toyota en Maturín</p>
        <h1>Motores Morichal, C.A.</h1>
        <p class="lead">Motores Morichal, C.A. es una empresa venezolana fundada en la ciudad de Matur&iacute;n en el a&ntilde;o 1979. Su constituci&oacute;n formal se llev&oacute; a cabo el 12 de junio de ese mismo a&ntilde;o, mediante un documento constitutivo estatutario de sociedad mercantil.</p>
        <div class="hero-chips">
          <span class="chip">Fundada por Oscar Ram&iacute;rez P&eacute;rez</span>
          <span class="chip">Presentada ante el Juzgado Segundo de Matur&iacute;n</span>
        </div>
        <div class="hero-stats">
          <div class="stat-card">
            <span class="stat-label">Fundaci&oacute;n</span>
            <span class="stat-value">1979</span>
            <p>Un proyecto nacido en Matur&iacute;n para acompa&ntilde;ar la movilidad en el oriente venezolano.</p>
          </div>
          <div class="stat-card">
            <span class="stat-label">Pasi&oacute;n por Toyota</span>
            <span class="stat-value">+45 a&ntilde;os</span>
            <p>Compromiso con la calidad, el buen servicio y la satisfacci&oacute;n de nuestros clientes.</p>
          </div>
          <div class="stat-card">
            <span class="stat-label">Sede actual</span>
            <span class="stat-value">Tipuro</span>
            <p>Av. Alirio Ugarte Pelayo, con showroom, taller autorizado y repuestos originales.</p>
          </div>
        </div>
      </div>
      <div class="about-hero-visual">
        <figure class="bento main gallery-item" data-index="0">
          <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/home/gallery-1.jpg' ); ?>" alt="Fachada de Motores Morichal" loading="lazy" />
        </figure>
        <div class="bento-stack">
          <figure class="bento card gallery-item" data-index="1">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/home/gallery-2.jpg' ); ?>" alt="Showroom de Motores Morichal en Tipuro" loading="lazy" />
          </figure>
          <figure class="bento card gallery-item" data-index="2">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/home/gallery-3.jpg' ); ?>" alt="Espacios interiores del concesionario" loading="lazy" />
          </figure>
        </div>
      </div>
    </div>
  </section>

  <!-- L&iacute;nea de tiempo -->
  <section class="about-story scroll-section is-visible" id="linea-tiempo">
    <div class="container">
      <div class="section-header align-left">
        <p class="about-pill soft">Nuestra trayectoria</p>
        <h2>Una concesionaria que crece con Matur&iacute;n</h2>
        <p>Gracias a su trayectoria, Motores Morichal, C.A. se ha posicionado como una empresa de referencia en el sector automotriz del oriente venezolano, destac&aacute;ndose por su compromiso con la calidad, el buen servicio y la satisfacci&oacute;n de sus clientes.</p>
      </div>
      <div class="story-grid">
        <article class="story-card fade-item">
          <div class="story-year">1979</div>
          <h3>Fundaci&oacute;n y constituci&oacute;n formal</h3>
          <p>Motores Morichal, C.A. es una empresa venezolana fundada en la ciudad de Matur&iacute;n en el a&ntilde;o 1979. Su constituci&oacute;n formal se llev&oacute; a cabo el 12 de junio de ese mismo a&ntilde;o, mediante un documento constitutivo estatutario de sociedad mercantil.</p>
          <p class="muted">El documento fue presentado por su fundador, el ciudadano Oscar Ram&iacute;rez P&eacute;rez, ante el Juzgado Segundo de la Primera Instancia en lo Civil, Mercantil, de Tr&aacute;nsito y del Trabajo, en la ciudad de Matur&iacute;n, estado Monagas.</p>
        </article>

        <article class="story-card fade-item">
          <div class="story-year">1979 - 1992</div>
          <h3>Primer hogar: Edificio Tronchi</h3>
          <p>Durante sus primeros a&ntilde;os de funcionamiento, desde 1979 hasta 1992, la empresa oper&oacute; en el Edificio Tronchi, ubicado en la Avenida Ra&uacute;l Leoni, una de las principales arterias comerciales de la ciudad.</p>
          <p class="muted">All&iacute; comenz&oacute; a consolidarse como un referente en el sector automotriz local, prestando servicios de venta y atenci&oacute;n al cliente.</p>
        </article>

        <article class="story-card fade-item">
          <div class="story-year">1992</div>
          <h3>Mudanza por crecimiento</h3>
          <p>En junio de 1992, debido al crecimiento sostenido de sus operaciones y a la necesidad de contar con un espacio m&aacute;s amplio y adecuado para sus actividades comerciales, Motores Morichal realiz&oacute; una primera mudanza a un nuevo local.</p>
          <p class="muted">El nuevo espacio estaba situado tambi&eacute;n en la Avenida Ra&uacute;l Leoni Sur, espec&iacute;ficamente en el n&uacute;mero 25.</p>
        </article>

        <article class="story-card highlight fade-item">
          <div class="story-year">Actualidad</div>
          <h3>Sede propia en Tipuro</h3>
          <p>Con el paso del tiempo y el incremento de su actividad comercial, as&iacute; como de la demanda de sus productos y servicios, la empresa tom&oacute; la decisi&oacute;n de trasladarse nuevamente a una sede m&aacute;s moderna y funcional.</p>
          <p class="muted">Actualmente, sus instalaciones se encuentran ubicadas en la Avenida Alirio Ugarte Pelayo, en el sector Tipuro, en un edificio propio identificado como "Motores Morichal". Esta sede cuenta con una amplia exhibici&oacute;n de veh&iacute;culos Toyota, servicio autorizado de taller, y venta de repuestos y accesorios originales.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Experiencia actual -->
  <section class="about-focus scroll-section is-visible">
    <div class="container focus-grid">
      <div class="focus-media gallery-item" data-index="3">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/home/gallery-4.jpg' ); ?>" alt="Atenci&oacute;n y servicio en Motores Morichal" loading="lazy" />
      </div>
      <div class="focus-content fade-item">
        <p class="about-pill soft">Centro integral Toyota</p>
        <h3>Experiencia completa en nuestra sede</h3>
        <p>En la Av. Alirio Ugarte Pelayo, sector Tipuro, consolidamos en un solo lugar la exhibici&oacute;n de veh&iacute;culos, el taller autorizado y la venta de repuestos y accesorios originales para atender a los usuarios de Toyota en la regi&oacute;n.</p>
        <ul class="focus-list">
          <li>Showroom amplio con la reconocida marca Toyota.</li>
          <li>Servicio autorizado de taller respaldado por la marca.</li>
          <li>Repuestos y accesorios originales para mantener la calidad Toyota.</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Rese&ntilde;a completa -->
  <section class="about-fullstory scroll-section is-visible" id="resena">
    <div class="container">
      <div class="fullstory-card fade-item">
        <p class="about-pill soft">Rese&ntilde;a completa</p>
        <h3>Motores Morichal, C.A.</h3>
        <p>Motores Morichal, C.A. es una empresa venezolana fundada en la ciudad de Matur&iacute;n en el a&ntilde;o 1979. Su constituci&oacute;n formal se llev&oacute; a cabo el 12 de junio de ese mismo a&ntilde;o, mediante un documento constitutivo estatutario de sociedad mercantil. Dicho documento fue presentado por su fundador, el ciudadano Oscar Ram&iacute;rez P&eacute;rez, ante el Juzgado Segundo de la Primera Instancia en lo Civil, Mercantil, de Tr&aacute;nsito y del Trabajo, en la ciudad de Matur&iacute;n, estado Monagas.</p>
        <p>Durante sus primeros a&ntilde;os de funcionamiento, desde 1979 hasta 1992, la empresa oper&oacute; en el Edificio Tronchi, ubicado en la Avenida Ra&uacute;l Leoni, una de las principales arterias comerciales de la ciudad. All&iacute; comenz&oacute; a consolidarse como un referente en el sector automotriz local, prestando servicios de venta y atenci&oacute;n al cliente.</p>
        <p>En junio de 1992, debido al crecimiento sostenido de sus operaciones y a la necesidad de contar con un espacio m&aacute;s amplio y adecuado para sus actividades comerciales, Motores Morichal realiz&oacute; una primera mudanza a un nuevo local, situado tambi&eacute;n en la Avenida Ra&uacute;l Leoni Sur, espec&iacute;ficamente en el n&uacute;mero 25.</p>
        <p>Con el paso del tiempo y el incremento de su actividad comercial, as&iacute; como de la demanda de sus productos y servicios, la empresa tom&oacute; la decisi&oacute;n de trasladarse nuevamente a una sede m&aacute;s moderna y funcional. Actualmente, sus instalaciones se encuentran ubicadas en la Avenida Alirio Ugarte Pelayo, en el sector Tipuro, en un edificio propio identificado como "Motores Morichal". Esta sede cuenta con una amplia exhibici&oacute;n de veh&iacute;culos de la reconocida marca Toyota, adem&aacute;s de ofrecer al p&uacute;blico servicio autorizado de taller, venta de repuestos y accesorios originales, consolid&aacute;ndose como un centro integral de atenci&oacute;n para los usuarios de esta marca en la regi&oacute;n.</p>
        <p>Gracias a su trayectoria, Motores Morichal, C.A. se ha posicionado como una empresa de referencia en el sector automotriz del oriente venezolano, destac&aacute;ndose por su compromiso con la calidad, el buen servicio y la satisfacci&oacute;n de sus clientes.</p>
      </div>
    </div>
  </section>

  <!-- Schema JSON-LD -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "AutoDealer",
    "name": "Motores Morichal",
    "brand": {"@type":"Brand","name":"Toyota"},
    "image": "<?php echo esc_url( get_template_directory_uri() . '/screenshot.png' ); ?>",
    "url": "<?php echo esc_url( home_url('/') ); ?>",
    "areaServed": "Monagas, Venezuela",
    "telephone": "<?php echo esc_attr( apply_filters('mmorichal_whatsapp_number','584241234567') ); ?>"
  }
  </script>
</main>

<?php get_footer(); ?>
