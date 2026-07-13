<?php get_header(); ?>

<main id="site-main">
  <!-- Slider de videos -->
  <div id="custom-slider" class="custom-slider swiper">
    <div class="swiper-wrapper">
      <!-- Slide 1 -->
      <div class="swiper-slide cs-slide" data-index="0">
        <video class="cs-video-bg" muted playsinline preload="metadata" data-src="https://arturomerchan.com/wp-content/uploads/2025/09/TOYOTA-MONAGAS-VIDEO-0.mp4" crossorigin="anonymous"></video>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Necesitas una Fortuner SW4</h2>
          <p>Es la fusi&oacute;n perfecta entre un todoterreno deportivo, utilitario y elegante.</p>
          <a href="#" class="cs-btn-slide">Ver m&aacute;s</a>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="swiper-slide cs-slide" data-index="1">
        <video class="cs-video-bg" muted playsinline preload="metadata" data-src="https://arturomerchan.com/wp-content/uploads/2025/09/TOYOTA-MONAGAS-VIDEO-2.mp4" crossorigin="anonymous"></video>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Nuevo Yaris Cross</h2>
          <p>Nuevo dise&ntilde;o moderno y sofisticado</p>
          <a href="#" class="cs-btn-slide">Explorar</a>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="swiper-slide cs-slide" data-index="2">
        <video class="cs-video-bg" muted playsinline preload="metadata" data-src="https://arturomerchan.com/wp-content/uploads/2025/09/TOYOTA-MONAGAS-VIDEO-3.mp4" crossorigin="anonymous"></video>
        <div class="cs-slide-content cs-left animate-in">
          <h2>Corolla 2025</h2>
          <p>Innovaci&oacute;n y estilo, ahora m&aacute;s cerca de ti.</p>
          <a href="#" class="cs-btn-slide">Descubrir</a>
        </div>
      </div>

      <!-- Slide 4: AGYA 2025 -->
      <div class="swiper-slide cs-slide" data-index="3">
        <video class="cs-video-bg" muted playsinline preload="metadata" data-src="https://arturomerchan.com/wp-content/uploads/2025/09/TOYOTA-MONAGAS-VIDEO-1.mp4" crossorigin="anonymous"></video>
        <div class="cs-slide-content cs-left animate-in">
          <h2>AGYA 2025</h2>
          <p>El Toyota Agya est&aacute; dise&ntilde;ado priorizando la ergonom&iacute;a, ofreciendo un amplio espacio interior y una posici&oacute;n de conducci&oacute;n enfocada en comodidad que te permitir&aacute; hacer los viajes que necesites sin sentirte fatigado.</p>
          <a href="#" class="cs-btn-slide">Con&oacute;celo</a>
        </div>
      </div>
    </div>

    <!-- Flechas -->
    <div class="cs-swiper-button-prev swiper-button-prev" aria-label="Anterior"></div>
    <div class="cs-swiper-button-next swiper-button-next" aria-label="Siguiente"></div>

    <!-- Barras de progreso -->
    <div class="cs-progress-bars">
      <div class="cs-progress-bar" data-index="0"><span></span></div>
      <div class="cs-progress-bar" data-index="1"><span></span></div>
      <div class="cs-progress-bar" data-index="2"><span></span></div>
      <div class="cs-progress-bar" data-index="3"><span></span></div>
    </div>
  </div>

  <!-- Vehículos por categorías -->
  <section id="vehiculos" class="toyota-section">
    <header class="section-header">
      <span class="kicker">Modelos</span>
      <h2>Descubre la línea Toyota</h2>
      <p>Explora por categoría el Toyota ideal para ti</p>
    </header>
    <nav class="toyota-nav" aria-label="Categorías de vehículos">
      <div class="toyota-tabs" role="tablist">
        <button class="toyota-tab is-active" data-cat="cars" aria-selected="true">Camioneta</button>
        <button class="toyota-tab" data-cat="trucks" aria-selected="false">Pasajero</button>
        <button class="toyota-tab" data-cat="crossovers" aria-selected="false">Pick Ups</button>
        <button class="toyota-tab" data-cat="electrified" aria-selected="false">Comercial</button>
        <span class="toyota-tab-indicator"></span>
      </div>
    </nav>

    <div class="toyota-slider swiper">
      <div class="swiper-wrapper"></div>
      <div class="toyota-arrow swiper-button-prev"></div>
      <div class="toyota-arrow swiper-button-next"></div>
    </div>

    <div class="toyota-templates" hidden aria-hidden="true">
      <template data-cat="cars">
        <article class="toyota-card">
          <figure class="toyota-imgbox">
            <img src="https://arturomerchan.com/wp-content/uploads/2025/09/748df8d8-a697-4619-a38c-6d4b6a8910c6.jpeg" alt="2025 Toyota Yaris Cross" decoding="async">
          </figure>
          <div class="toyota-info">
            <header class="toyota-info-text">
              <span class="toyota-year">2025</span>
              <h3>Toyota Yaris Cross</h3>
              <p>SUV compacto, vers&aacute;til y eficiente para la ciudad.</p>
            </header>
            <div class="toyota-buttons">
              <a href="#" class="toyota-btn">M&aacute;s informaci&oacute;n <i class="fas fa-arrow-right"></i></a>
              <span class="toyota-contact"><a href="#contacto">Cont&aacute;ctanos &gt;</a></span>
            </div>
          </div>
        </article>
      </template>
    </div>
  </section>

  <!-- Accesorios -->
  <section id="accesorios-mm" class="accesorios-mm">
    <div class="accesorios-overlay">
      <div class="accesorios-content">
        <h2>Accesorios Originales Toyota</h2>
        <p>Equipa tu Toyota con accesorios dise&ntilde;ados para potenciar estilo, comodidad y seguridad, siempre con la calidad original.</p>
        <a href="https://www.toyota.com.ve/mi-toyota/accesorios" class="accesorio-btn" target="_blank" rel="noopener">Explorar Accesorios <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- Servicios -->
  <section id="info-mm" class="info-mm">
    <header class="section-header">
      <span class="kicker">Servicios</span>
      <h2>Cuidado total para tu Toyota</h2>
      <p>Mantenimiento, repuestos y atención especializada</p>
    </header>
    <div class="services-grid">
      <div class="service-card">
        <div class="service-icon"><i class="fas fa-car"></i></div>
        <h3>Veh&iacute;culos</h3>
        <p>Descubre toda la gama de veh&iacute;culos disponibles, pensados para tu estilo de vida.</p>
        <a href="#" class="service-btn">Explorar <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="service-card">
        <div class="service-icon"><i class="fas fa-cogs"></i></div>
        <h3>Repuestos</h3>
        <p>Solicita repuestos originales Toyota con garant&iacute;a y confianza asegurada.</p>
        <a href="#" class="service-btn">Pedir repuestos <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="service-card">
        <div class="service-icon"><i class="fas fa-tools"></i></div>
        <h3>Servicios</h3>
        <p>Mantenimiento, revisi&oacute;n y asistencia t&eacute;cnica especializada para tu Toyota.</p>
        <a href="#" class="service-btn">Agendar cita <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="service-card">
        <div class="service-icon"><i class="fab fa-whatsapp"></i></div>
        <h3>Citas</h3>
        <p>Agenda tu cita de forma r&aacute;pida y sencilla, estamos a un clic de distancia.</p>
        <a href="#" class="service-btn">Cont&aacute;ctanos <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- Sobre nosotros -->
  <section id="sobre-nosotros" class="sobre-nosotros">
    <header class="section-header">
      <span class="kicker">Nuestra historia</span>
      <h2>Excelencia y confianza Toyota</h2>
      <p>Comprometidos con tu movilidad y seguridad</p>
    </header>
    <div class="sobre-nosotros-container">
      <div class="sobre-nosotros-text">
        <h2>Sobre Nosotros</h2>
        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur nec elit in urna bibendum interdum. Nulla facilisi. Morbi tincidunt, urna sit amet tincidunt pharetra, lacus nunc egestas ligula, ac vehicula magna felis ut erat. </p>
        <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer sagittis nulla a turpis ornare, nec fermentum sapien gravida. </p>
        <a href="#" class="sobre-nosotros-btn">M&aacute;s informaci&oacute;n <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="sobre-nosotros-images gallery-grid">
        <div class="img img1 gallery-item" data-index="0">
          <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0481-scaled.jpg" alt="Motores Morichal - Instalaciones" loading="lazy">
          <div class="gallery-overlay">
            <i class="fas fa-search-plus"></i>
          </div>
        </div>
        <div class="img img2 gallery-item" data-index="1">
          <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0454-scaled.jpg" alt="Motores Morichal - Showroom" loading="lazy">
          <div class="gallery-overlay">
            <i class="fas fa-search-plus"></i>
          </div>
        </div>
        <div class="img img3 gallery-item" data-index="2">
          <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0469-scaled.jpg" alt="Motores Morichal - Atención" loading="lazy">
          <div class="gallery-overlay">
            <i class="fas fa-search-plus"></i>
          </div>
        </div>
      </div>

      <!-- Lightbox Modal -->
      <div id="gallery-lightbox" class="gallery-lightbox" role="dialog" aria-modal="true" aria-hidden="true">
        <button class="lightbox-close" aria-label="Cerrar galería">
          <i class="fas fa-times"></i>
        </button>
        
        <div class="lightbox-content">
          <!-- Main Gallery Swiper -->
          <div class="gallery-swiper swiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0481-scaled.jpg" alt="Motores Morichal - Instalaciones">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0454-scaled.jpg" alt="Motores Morichal - Showroom">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0469-scaled.jpg" alt="Motores Morichal - Atención">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0354-scaled.jpg" alt="Motores Morichal - Equipo">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0348-scaled.jpg" alt="Motores Morichal - Servicio">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0479-scaled.jpg" alt="Motores Morichal - Concesionario">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0480-scaled.jpg" alt="Motores Morichal - Vehículos">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0252-scaled.jpg" alt="Motores Morichal - Experiencia">
              </div>
            </div>
            
            <!-- Navigation Arrows -->
            <div class="swiper-button-prev gallery-arrow-prev"></div>
            <div class="swiper-button-next gallery-arrow-next"></div>
          </div>

          <!-- Thumbnail Gallery -->
          <div class="gallery-thumbs swiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0481-scaled.jpg" alt="Miniatura 1">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0454-scaled.jpg" alt="Miniatura 2">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0469-scaled.jpg" alt="Miniatura 3">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0354-scaled.jpg" alt="Miniatura 4">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0348-scaled.jpg" alt="Miniatura 5">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0479-scaled.jpg" alt="Miniatura 6">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0480-scaled.jpg" alt="Miniatura 7">
              </div>
              <div class="swiper-slide">
                <img src="https://arturomerchan.com/wp-content/uploads/2025/11/IMG_0252-scaled.jpg" alt="Miniatura 8">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Productos -->
  <section id="productos-mm" class="productos-mm">
    <header class="section-header">
      <span class="kicker">Repuestos</span>
      <h2>Originales Toyota al mejor precio</h2>
      <p>Calidad garantizada para el rendimiento de tu vehículo</p>
    </header>
    <div class="productos-grid">
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-filter"></i></div>
        <h3>Filtro de Aceite</h3>
        <p>Filtro original Toyota para mantener el motor en &oacute;ptimas condiciones.</p>
        <span class="producto-precio">$25.00</span>
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-car-battery"></i></div>
        <h3>Bater&iacute;a Original</h3>
        <p>Bater&iacute;a Toyota de alto rendimiento y larga duraci&oacute;n.</p>
        <span class="producto-precio">$120.00</span>
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-car-side"></i></div>
        <h3>Pastillas de Freno</h3>
        <p>Juego de pastillas delanteras originales Toyota, m&aacute;xima seguridad.</p>
        <span class="producto-precio">$80.00</span>
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="producto-card">
        <div class="producto-icon"><i class="fas fa-wind"></i></div>
        <h3>Filtro de Aire</h3>
        <p>Filtro de aire Toyota para mayor eficiencia y consumo optimizado.</p>
        <span class="producto-precio">$35.00</span>
        <a href="#" class="producto-btn">A&ntilde;adir al carrito <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- Blog -->
  <section id="blog-mm" class="blog-mm">
    <header class="section-header">
      <span class="kicker">Noticias</span>
      <h2>Historias y novedades Toyota</h2>
      <p>Tendencias, consejos y lanzamientos destacados</p>
    </header>
    <div class="blog-grid">
      <article class="blog-card">
        <div class="blog-img"><img src="https://picsum.photos/600/400?random=1" alt="Post 1"></div>
        <div class="blog-content">
          <span class="blog-meta">12 Ago 2025 &bull; Admin</span>
          <h3>Lorem ipsum dolor sit amet</h3>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque habitant morbi tristique senectus...</p>
          <a href="#" class="blog-btn">Leer m&aacute;s</a>
        </div>
      </article>
      <article class="blog-card">
        <div class="blog-img"><img src="https://picsum.photos/600/400?random=2" alt="Post 2"></div>
        <div class="blog-content">
          <span class="blog-meta">10 Ago 2025 &bull; Redacci&oacute;n</span>
          <h3>Consectetur adipiscing elit</h3>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus luctus urna sed urna ultricies...</p>
          <a href="#" class="blog-btn">Leer m&aacute;s</a>
        </div>
      </article>
      <article class="blog-card">
        <div class="blog-img"><img src="https://picsum.photos/600/400?random=3" alt="Post 3"></div>
        <div class="blog-content">
          <span class="blog-meta">8 Ago 2025 &bull; Invitado</span>
          <h3>Curabitur sit amet mauris morbi</h3>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis...</p>
          <a href="#" class="blog-btn">Leer m&aacute;s</a>
        </div>
      </article>
      
    </div>
  </section>

</main>

<?php get_footer(); ?>






