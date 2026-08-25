<?php
/**
 * Shortcodes
 */

if (!defined('ABSPATH')) {
    exit;
}

function toyota_monagas_privacy_policy_url() {
    $policy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
    if ($policy_url) {
        return esc_url_raw($policy_url);
    }

    $default_fallback = 'https://www.toyota.com.ve/politica-de-privacidad';
    $filtered_fallback = apply_filters('toyota_monagas_privacy_fallback_url', $default_fallback);
    $filtered_fallback = is_scalar($filtered_fallback) ? esc_url_raw((string) $filtered_fallback) : '';

    return $filtered_fallback !== '' ? $filtered_fallback : $default_fallback;
}

function toyota_monagas_privacy_consent_field($form_uid) {
    $policy_url = toyota_monagas_privacy_policy_url();
    ?>
    <div class="mf-row mm-privacy-consent">
        <label for="<?php echo esc_attr($form_uid); ?>-privacy">
            <input type="checkbox" id="<?php echo esc_attr($form_uid); ?>-privacy" name="privacy_consent" value="1" required>
            <span>
                Acepto el tratamiento de mis datos para atender esta solicitud
                y he leído la <a href="<?php echo esc_url($policy_url); ?>" target="_blank" rel="noopener noreferrer">política de privacidad</a>.
            </span>
        </label>
    </div>
    <?php
}

// ---------------------------------------------
// Shortcode: WhatsApp form
// ---------------------------------------------
add_shortcode('formulario_mmorichal', function () {
    ob_start();
    $wa_number = toyota_monagas_whatsapp_number();
    $form_uid = function_exists('wp_unique_id') ? wp_unique_id('mmorichal-form-') : uniqid('mmorichal-form-', false);
    ?>
    <form class="mmorichal-form" onsubmit="return mmorichalEnviarWhatsApp(this);"
      data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
      data-nonce="<?php echo esc_attr(wp_create_nonce('mm_contact_nonce')); ?>">
        <div class="mm-honeypot" aria-hidden="true">
            <label for="<?php echo esc_attr($form_uid); ?>-website">Sitio web</label>
            <input type="text" id="<?php echo esc_attr($form_uid); ?>-website" name="company_website" value="" tabindex="-1" autocomplete="off">
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-nombre">Nombre completo</label>
            <input type="text" id="<?php echo esc_attr($form_uid); ?>-nombre" name="m_nombre" maxlength="100" autocomplete="name" placeholder="Ej: Juan Gómez" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-telefono">Tel&eacute;fono</label>
            <input type="tel" id="<?php echo esc_attr($form_uid); ?>-telefono" name="m_telefono" maxlength="40" autocomplete="tel" pattern="[+]?[0-9\s().-]{7,40}" placeholder="Ej: 0424-123-4567" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-email">Correo electr&oacute;nico</label>
            <input type="email" id="<?php echo esc_attr($form_uid); ?>-email" name="m_email" maxlength="254" autocomplete="email" placeholder="Ej: correo@ejemplo.com" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-servicio">Servicio a solicitar</label>
            <select id="<?php echo esc_attr($form_uid); ?>-servicio" name="m_servicio" required>
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
            <label for="<?php echo esc_attr($form_uid); ?>-modelo">Modelo de Vehículo</label>
            <input type="text" id="<?php echo esc_attr($form_uid); ?>-modelo" name="m_modelo" maxlength="120" placeholder="Ej: Toyota Hilux 2025" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-mensaje">Mensaje</label>
            <textarea id="<?php echo esc_attr($form_uid); ?>-mensaje" name="m_mensaje" rows="4" maxlength="4000" placeholder="Cuéntanos"></textarea>
        </div>
        <?php toyota_monagas_privacy_consent_field($form_uid); ?>
        <button type="submit" class="mf-submit">
            <span class="mf-icon" aria-hidden="true">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icon-whatsapp.webp'); ?>" alt="" width="24" height="24" loading="lazy" />
            </span>
            Enviar a WhatsApp
        </button>
    </form>
    <script>
    function mmorichalEnviarWhatsApp(form){
      try {
        var nombre  = form.querySelector('[name="m_nombre"]').value.trim();
        var telefono= form.querySelector('[name="m_telefono"]').value.trim();
        var email   = form.querySelector('[name="m_email"]').value.trim();
        var servicio= form.querySelector('[name="m_servicio"]').value;
        var modelo  = form.querySelector('[name="m_modelo"]').value.trim();
        var mensajeLibre = (form.querySelector('[name="m_mensaje"]').value || '').trim();
        if(!nombre || !servicio || !modelo || !telefono){ return false; }

        var formData = new FormData();
        formData.append('action', 'mm_submit_contact_form');
        formData.append('type', 'whatsapp');
        formData.append('name', nombre);
        formData.append('phone', telefono);
        formData.append('email', email);
        formData.append('model', modelo);
        formData.append('message', "Servicio: " + servicio + "\n\n" + mensajeLibre);
        formData.append('company_website', (form.querySelector('[name="company_website"]') || {}).value || '');
        formData.append('privacy_consent', (form.querySelector('[name="privacy_consent"]') || {}).checked ? '1' : '0');

        var localized = (typeof window.mm_ajax === 'object' && window.mm_ajax) ? window.mm_ajax : {};
        var nonce = localized.nonce || form.getAttribute('data-nonce') || '';
        var ajaxUrl = localized.ajaxurl || form.getAttribute('data-ajax-url') || '';
        if (nonce) formData.append('nonce', nonce);
        if (ajaxUrl && window.fetch) {
            fetch(ajaxUrl, { method: 'POST', body: formData, credentials: 'same-origin', keepalive: true })
              .catch(function() {});
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
        var opened = window.open(url, '_blank', 'noopener,noreferrer');
        if (opened) opened.opener = null;
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
    $wa_number = toyota_monagas_whatsapp_number();
    $form_uid = function_exists('wp_unique_id') ? wp_unique_id('disponibilidad-form-') : uniqid('disponibilidad-form-', false);
    ?>
    <form class="mmorichal-form" onsubmit="return mmorichalEnviarDisponibilidad(this);"
      data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
      data-nonce="<?php echo esc_attr(wp_create_nonce('mm_contact_nonce')); ?>">
        <div class="mm-honeypot" aria-hidden="true">
            <label for="<?php echo esc_attr($form_uid); ?>-website">Sitio web</label>
            <input type="text" id="<?php echo esc_attr($form_uid); ?>-website" name="company_website" value="" tabindex="-1" autocomplete="off">
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-nombre">Nombre completo</label>
            <input type="text" id="<?php echo esc_attr($form_uid); ?>-nombre" name="d_nombre" maxlength="100" autocomplete="name" placeholder="Ej: Juan Gómez" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-telefono">Teléfono</label>
            <input type="tel" id="<?php echo esc_attr($form_uid); ?>-telefono" name="d_telefono" maxlength="40" autocomplete="tel" pattern="[+]?[0-9\s().-]{7,40}" placeholder="Ej: 0424-123-4567" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-email">Correo electr&oacute;nico</label>
            <input type="email" id="<?php echo esc_attr($form_uid); ?>-email" name="d_email" maxlength="254" autocomplete="email" placeholder="Ej: correo@ejemplo.com" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-vehiculo">Vehículo de interés</label>
            <input type="text" id="<?php echo esc_attr($form_uid); ?>-vehiculo" name="d_vehiculo" maxlength="120" placeholder="Nombre del Vehículo" required>
        </div>
        <div class="mf-row">
            <label for="<?php echo esc_attr($form_uid); ?>-mensaje">Mensaje adicional</label>
            <textarea id="<?php echo esc_attr($form_uid); ?>-mensaje" name="d_mensaje" rows="3" maxlength="4000" placeholder="Información adicional que quieras compartir..."></textarea>
        </div>
        <?php toyota_monagas_privacy_consent_field($form_uid); ?>
        <button type="submit" class="mf-submit">
            <span class="mf-icon" aria-hidden="true">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icon-whatsapp.webp'); ?>" alt="" width="24" height="24" loading="lazy" />
            </span>
            Consultar disponibilidad
        </button>
    </form>
    <script>
    function mmorichalEnviarDisponibilidad(form){
      try {
        var nombre   = form.querySelector('[name="d_nombre"]').value.trim();
        var telefono = form.querySelector('[name="d_telefono"]').value.trim();
        var email    = form.querySelector('[name="d_email"]').value.trim();
        var vehiculo = form.querySelector('[name="d_vehiculo"]').value.trim();
        var mensajeLibre = (form.querySelector('[name="d_mensaje"]').value || '').trim();
        if(!nombre || !telefono || !vehiculo || !email){ return false; }

        var formData = new FormData();
        formData.append('action', 'mm_submit_contact_form');
        formData.append('type', 'whatsapp');
        formData.append('name', nombre);
        formData.append('phone', telefono);
        formData.append('email', email);
        formData.append('model', vehiculo);
        formData.append('message', mensajeLibre);
        formData.append('company_website', (form.querySelector('[name="company_website"]') || {}).value || '');
        formData.append('privacy_consent', (form.querySelector('[name="privacy_consent"]') || {}).checked ? '1' : '0');

        var localized = (typeof window.mm_ajax === 'object' && window.mm_ajax) ? window.mm_ajax : {};
        var nonce = localized.nonce || form.getAttribute('data-nonce') || '';
        var ajaxUrl = localized.ajaxurl || form.getAttribute('data-ajax-url') || '';
        if (nonce) formData.append('nonce', nonce);
        if (ajaxUrl && window.fetch) {
            fetch(ajaxUrl, { method: 'POST', body: formData, credentials: 'same-origin', keepalive: true })
              .catch(function() {});
        }

        var mensaje = 'Hola Motores Morichal, quiero consultar disponibilidad:%0A' +
                      '• Nombre: ' + encodeURIComponent(nombre) + '%0A' +
                      '• Teléfono: ' + encodeURIComponent(telefono) + '%0A' +
                      '• Correo: ' + encodeURIComponent(email) + '%0A' +
                      '• Vehículo: ' + encodeURIComponent(vehiculo);
        if(mensajeLibre){ mensaje += '%0A• Mensaje: ' + encodeURIComponent(mensajeLibre); }
        var numero = '<?php echo esc_js($wa_number); ?>';
        var url = 'https://wa.me/' + numero + '?text=' + mensaje;
        var opened = window.open(url, '_blank', 'noopener,noreferrer');
        if (opened) opened.opener = null;
      } catch(e) {}
      return false;
    }
    </script>
    <?php
    return ob_get_clean();
});
