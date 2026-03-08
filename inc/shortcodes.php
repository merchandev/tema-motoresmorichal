<?php
/**
 * Shortcodes
 */

// ---------------------------------------------
// Shortcode: WhatsApp form
// ---------------------------------------------
add_shortcode('formulario_mmorichal', function () {
    ob_start();
    $wa_number = apply_filters('mmorichal_whatsapp_number', '584249090679');
    ?>
    <form class="mmorichal-form" onsubmit="return mmorichalEnviarWhatsApp(this);">
        <div class="mf-row">
            <label for="m_nombre">Nombre completo</label>
            <input type="text" id="m_nombre" name="m_nombre" placeholder="Ej: Juan Gómez" required>
        </div>
        <div class="mf-row">
            <label for="m_telefono">Tel&eacute;fono</label>
            <input type="tel" id="m_telefono" name="m_telefono" placeholder="Ej: 0424-123-4567" required>
        </div>
        <div class="mf-row">
            <label for="m_email">Correo electr&oacute;nico</label>
            <input type="email" id="m_email" name="m_email" placeholder="Ej: correo@ejemplo.com" required>
        </div>
        <div class="mf-row">
            <label for="m_servicio">Servicio a solicitar</label>
            <select id="m_servicio" name="m_servicio" required>
                <option value="Servicios generales">Servicios generales</option>
                <option value="Servicio escaner">Servicio escáner</option>
                <option value="Mantenimiento periódicos">Mantenimiento periódicos</option>
                <option value="Cambio de aceite y filtro">Cambio de aceite y filtro</option>
                <option value="Entonación de motor">Entonación de motor</option>
                <option value="Servicio de lavado">Servicio de lavado</option>
                <option value="Cuando llega este modelo">Cuando llega este modelo</option>
            </select>
        </div>
        <div class="mf-row">
            <label for="m_modelo">Modelo de Vehículo</label>
            <input type="text" id="m_modelo" name="m_modelo" placeholder="Ej: Toyota Hilux 2025" required>
        </div>
        <div class="mf-row">
            <label for="m_mensaje">Mensaje</label>
            <textarea id="m_mensaje" name="m_mensaje" rows="4" placeholder="Cuéntanos"></textarea>
        </div>
        <button type="submit" class="mf-submit">
            <span class="mf-icon" aria-hidden="true">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-whatsapp.webp" alt="WhatsApp" width="24" height="24" loading="lazy" />
            </span>
            Enviar a WhatsApp
        </button>
    </form>
    <script>
    function mmorichalEnviarWhatsApp(form){
      try {
        var nombre  = form.querySelector('#m_nombre').value.trim();
        var telefono= form.querySelector('#m_telefono').value.trim();
        var email   = form.querySelector('#m_email').value.trim();
        var servicio= form.querySelector('#m_servicio').value;
        var modelo  = form.querySelector('#m_modelo').value.trim();
        var mensajeLibre = (form.querySelector('#m_mensaje').value || '').trim();
        if(!nombre || !servicio || !modelo || !telefono){ return false; }

        var formData = new FormData();
        formData.append('action', 'mm_submit_contact_form');
        formData.append('type', 'whatsapp');
        formData.append('name', nombre);
        formData.append('phone', telefono);
        formData.append('email', email);
        formData.append('model', modelo);
        formData.append('message', "Servicio: " + servicio + "\n\n" + mensajeLibre);

        var ajaxUrl = (typeof mm_ajax !== 'undefined' && mm_ajax.ajaxurl) ? mm_ajax.ajaxurl : '/wp-admin/admin-ajax.php';

        if (navigator.sendBeacon) {
            navigator.sendBeacon(ajaxUrl, formData);
        } else {
            fetch(ajaxUrl, { method: 'POST', body: formData });
        }

        var mensaje = 'Hola Motores Morichal, me gustaría recibir información:%0A' +
                      '• Nombre: ' + encodeURIComponent(nombre) + '%0A' +
                      '• Teléfono: ' + encodeURIComponent(telefono) + '%0A' +
                      '• Correo: ' + encodeURIComponent(email) + '%0A' +
                      '• Servicio: ' + encodeURIComponent(servicio) + '%0A' +
                      '• Modelo: ' + encodeURIComponent(modelo);
        if(mensajeLibre){ mensaje += '%0A• Mensaje: ' + encodeURIComponent(mensajeLibre); }
        var numero = '<?php echo esc_js($wa_number); ?>';
        var url = 'https://wa.me/' + numero + '?text=' + mensaje;
        window.open(url, '_blank');
      } catch(e) {}
      return false;
    }
    </script>
    <?php
    return ob_get_clean();
});

// ---------------------------------------------
// Shortcode: WhatsApp form para consultar disponibilidad (Vehículos usados)
// ---------------------------------------------
add_shortcode('formulario_disponibilidad', function () {
    ob_start();
    $wa_number = apply_filters('mmorichal_whatsapp_number', '584249090679');
    ?>
    <form class="mmorichal-form" onsubmit="return mmorichalEnviarDisponibilidad(this);">
        <div class="mf-row">
            <label for="d_nombre">Nombre completo</label>
            <input type="text" id="d_nombre" name="d_nombre" placeholder="Ej: Juan Gómez" required>
        </div>
        <div class="mf-row">
            <label for="d_telefono">Teléfono</label>
            <input type="tel" id="d_telefono" name="d_telefono" placeholder="Ej: 0424-123-4567" required>
        </div>
        <div class="mf-row">
            <label for="d_email">Correo electr&oacute;nico</label>
            <input type="email" id="d_email" name="d_email" placeholder="Ej: correo@ejemplo.com" required>
        </div>
        <div class="mf-row">
            <label for="d_vehiculo">Vehículo de interés</label>
            <input type="text" id="d_vehiculo" name="d_vehiculo" placeholder="Nombre del Vehículo" required>
        </div>
        <div class="mf-row">
            <label for="d_mensaje">Mensaje adicional</label>
            <textarea id="d_mensaje" name="d_mensaje" rows="3" placeholder="Información adicional que quieras compartir..."></textarea>
        </div>
        <button type="submit" class="mf-submit">
            <span class="mf-icon" aria-hidden="true">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-whatsapp.webp" alt="WhatsApp" width="24" height="24" loading="lazy" />
            </span>
            Consultar disponibilidad
        </button>
    </form>
    <script>
    function mmorichalEnviarDisponibilidad(form){
      try {
        var nombre   = form.querySelector('#d_nombre').value.trim();
        var telefono = form.querySelector('#d_telefono').value.trim();
        var email    = form.querySelector('#d_email').value.trim();
        var vehiculo = form.querySelector('#d_vehiculo').value.trim();
        var mensajeLibre = (form.querySelector('#d_mensaje').value || '').trim();
        if(!nombre || !telefono || !vehiculo || !email){ return false; }

        var formData = new FormData();
        formData.append('action', 'mm_submit_contact_form');
        formData.append('type', 'whatsapp');
        formData.append('name', nombre);
        formData.append('phone', telefono);
        formData.append('email', email);
        formData.append('model', vehiculo);
        formData.append('message', mensajeLibre);

        var ajaxUrl = (typeof mm_ajax !== 'undefined' && mm_ajax.ajaxurl) ? mm_ajax.ajaxurl : '/wp-admin/admin-ajax.php';

        if (navigator.sendBeacon) {
            navigator.sendBeacon(ajaxUrl, formData);
        } else {
            fetch(ajaxUrl, { method: 'POST', body: formData });
        }

        var mensaje = 'Hola Motores Morichal, quiero consultar disponibilidad:%0A' +
                      '• Nombre: ' + encodeURIComponent(nombre) + '%0A' +
                      '• Teléfono: ' + encodeURIComponent(telefono) + '%0A' +
                      '• Correo: ' + encodeURIComponent(email) + '%0A' +
                      '• Vehículo: ' + encodeURIComponent(vehiculo);
        if(mensajeLibre){ mensaje += '%0A• Mensaje: ' + encodeURIComponent(mensajeLibre); }
        var numero = '<?php echo esc_js($wa_number); ?>';
        var url = 'https://wa.me/' + numero + '?text=' + mensaje;
        window.open(url, '_blank');
      } catch(e) {}
      return false;
    }
    </script>
    <?php
    return ob_get_clean();
});
