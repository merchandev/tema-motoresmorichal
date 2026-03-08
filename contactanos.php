<?php
/*
Template Name: Contáctanos
*/
get_header(); ?>

<main id="site-main">
  <!-- Encabezado de contacto (títulos en negro) + Formulario centrado -->
  <section id="contacto-header" class="contacto-header">
    <header class="section-header">
      <span class="kicker">Contacto</span>
      <h2>Solicita información por WhatsApp</h2>
      <p>Resolvemos tus dudas rápidamente</p>
    </header>
    <div class="whatsapp-wrap">
      <?php echo do_shortcode('[formulario_mmorichal]'); ?>
    </div>
  </section>

  <!-- Descripción y recomendaciones (debajo del formulario) -->
  <section id="contacto-info" class="contacto-info">
    <div class="container">
      <h3 class="contacto-info__title">Atención al Cliente</h3>
      <p>Estamos aquí para ayudarte. Escríbenos por WhatsApp y te respondemos a la brevedad.</p>
      <p>Utilizando el siguiente formulario podrás ponerte en contacto con nosotros para realizar cualquier consulta o comentario sobre la atención al cliente y/o nuestros concesionarios autorizados. Nuestro personal se comunicará contigo a la brevedad posible.</p>
      <ul class="contacto-lista">
        <li>Llenar todos los campos donde se solicite información.</li>
        <li>Verificar que los datos de contacto sean correctos.</li>
        <li>En la descripción de tu solicitud, sé lo más específico posible.</li>
      </ul>
    </div>
  </section>

  <!-- Preguntas frecuentes -->
  <section id="faq-contacto" class="faq-contacto">
    <header class="section-header">
      <span class="kicker">Ayuda</span>
      <h2>Preguntas frecuentes</h2>
      <p>Información útil sobre procesos y atención</p>
    </header>

    <div class="container">
      <div class="faq-accordion" role="list">
        <details class="faq-item" role="listitem" open>
          <summary>¿Deseo información de financiamiento para el vehículo que quiero comprar?</summary>
          <div class="faq-body">
            <p>Cada concesionario ofrece planes con distintas entidades bancarias. Los factores a evaluar incluyen tasa, plazo, inicial y tipo de crédito. Te invitamos a visitar tu concesionario de preferencia para conocer los planes disponibles y elegir el que mejor se ajuste a tus necesidades.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary>¿Cómo hago para comprar un vehículo a través de un concesionario?</summary>
          <div class="faq-body">
            <p>Debes contactar al concesionario más cercano a tu domicilio para recibir información actualizada de disponibilidad, precios y requisitos. Ellos te guiarán en todo el proceso de compra y formas de pago.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary>Llevo tiempo en lista de espera y aún no recibo mi vehículo, ¿por qué?</summary>
          <div class="faq-body">
            <p>Los tiempos pueden variar por factores logísticos, disponibilidad o procesos de importación/producción. Te sugerimos mantener comunicación con tu concesionario para actualizaciones.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary>¿Dónde puedo comprar repuestos y accesorios originales?</summary>
          <div class="faq-body">
            <p>Los repuestos y accesorios originales se adquieren exclusivamente en concesionarios autorizados. Allí obtendrás garantía, respaldo y recomendaciones de instalación.</p>
          </div>
        </details>

        <details class="faq-item" open>
          <summary>¿Puedo solicitar servicio de mantenimiento?</summary>
          <div class="faq-body">
            <p>Sí. Agenda tu cita de mantenimiento preventivo o correctivo en el concesionario de tu preferencia. También puedes usar el formulario de WhatsApp para iniciar tu solicitud.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary>¿Qué información debo tener a mano para ser atendido más rápido?</summary>
          <div class="faq-body">
            <p>Nombre y apellido, modelo del vehículo, año, cédula/RIF y una breve descripción del motivo de tu consulta. Si aplica, incluye número de placa o seriales.</p>
          </div>
        </details>
      </div>
    </div>
  </section>

  <!-- Mapa de Google Maps -->
  <section id="mapa-contacto" class="mapa-contacto">
    <header class="section-header">
      <span class="kicker">Ubicación</span>
      <h2>Nuestra ubicación</h2>
      <p>Toyota - Motores Morichal</p>
    </header>
    <div class="container">
      <div class="map-frame" aria-label="Mapa: Toyota - Motores Morichal">
        <iframe
          loading="lazy"
          allowfullscreen
          referrerpolicy="no-referrer-when-downgrade"
          src="https://www.google.com/maps?q=Toyota%20-%20Motores%20Morichal%2C%20Matur%C3%ADn%206201%2C%20Monagas%2C%20Venezuela&t=&z=17&ie=UTF8&iwloc=near&output=embed">
        </iframe>
        
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
