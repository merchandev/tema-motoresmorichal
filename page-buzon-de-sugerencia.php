<?php
/**
 * Template Name: Buzón de Sugerencia
 */

// Post/Redirect/Get prevents accidental duplicate submissions on refresh.
$allowed_statuses = array('success', 'error', 'incomplete', 'security', 'rate');
$msg_status = sanitize_key(toyota_monagas_request_scalar($_GET, 'buzon_status'));
if (!in_array($msg_status, $allowed_statuses, true)) $msg_status = '';

if (strtoupper(toyota_monagas_request_scalar($_SERVER, 'REQUEST_METHOD')) === 'POST') {
    $status = 'security';
    $nonce = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'buzon_nonce'));

    if ($nonce && wp_verify_nonce($nonce, 'enviar_sugerencia')) {
        $honeypot = trim(toyota_monagas_request_scalar($_POST, 'b_website'));
        if ($honeypot !== '') {
            $status = 'success';
        } else {
            $nombre = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'b_nombre'));
            $email = sanitize_email(toyota_monagas_request_scalar($_POST, 'b_email'));
            $area = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'b_area'));
            $direccion = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'b_direccion'));
            $whatsapp = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'b_whatsapp'));
            $mensaje = sanitize_textarea_field(toyota_monagas_request_scalar($_POST, 'b_mensaje'));
            $privacy_consent = toyota_monagas_request_scalar($_POST, 'privacy_consent');
            $areas = array('Stock', 'Venta', 'Atención al cliente', 'Seguridad');
            $length = function($value) {
                return function_exists('mm_contact_text_length') ? mm_contact_text_length($value) : strlen($value);
            };

            $valid = $nombre !== ''
                && $length($nombre) <= 100
                && is_email($email)
                && $length($email) <= 254
                && in_array($area, $areas, true)
                && $length($direccion) <= 200
                && $length($whatsapp) <= 40
                && ($whatsapp === '' || preg_match('/^[0-9+() .-]{7,40}$/', $whatsapp))
                && $mensaje !== ''
                && $length($mensaje) <= 4000
                && $privacy_consent === '1';

            if (!$valid) {
                $status = 'incomplete';
            } elseif (function_exists('mm_contact_rate_limit_allows') && !mm_contact_rate_limit_allows('suggestion', 3, 15 * MINUTE_IN_SECONDS)) {
                $status = 'rate';
            } else {
                $to = sanitize_email(get_option('mm_notify_email', get_option('admin_email')));
                if (!is_email($to)) $to = sanitize_email(get_option('admin_email'));

                $subject = 'Nueva Sugerencia/Reclamo: ' . $area;
                $body = "Has recibido una nueva sugerencia o reclamo desde el sitio web.\n\n";
                $body .= "Nombre: " . $nombre . "\n";
                $body .= "Correo: " . $email . "\n";
                $body .= "Teléfono/WhatsApp: " . ($whatsapp ?: 'No indicado') . "\n";
                $body .= "Dirección: " . ($direccion ?: 'No indicada') . "\n";
                $body .= "Área: " . $area . "\n";
                $body .= "Mensaje:\n" . $mensaje . "\n\n";
                $headers = function_exists('mm_contact_mail_headers')
                    ? mm_contact_mail_headers($nombre, $email, false)
                    : array('Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $nombre . ' <' . $email . '>');

                $status = is_email($to) && wp_mail($to, $subject, $body, $headers) ? 'success' : 'error';
            }
        }
    }

    $redirect_url = get_permalink(get_queried_object_id());
    if (!$redirect_url) $redirect_url = home_url('/buzon-de-sugerencia/');
    wp_safe_redirect(add_query_arg('buzon_status', $status, $redirect_url), 303);
    exit;
}

get_header(); ?>

<main id="site-main" class="buzon-page">
    
    <!-- Header Section matching theme style -->
    <section class="contacto-header">
        <header class="section-header text-center">
            <span class="kicker" style="color: var(--toyota-red); font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">Buzón</span>
            <h1 style="color: var(--toyota-black-2); font-size: 2.5rem; margin: 10px 0;">Buzón de Sugerencias</h1>
            <p style="color: #4b5563; max-width: 700px; margin: 0 auto 30px;">
                Utilizando el siguiente formulario podrás ponerte en contacto con nosotros para realizar cualquier reclamo o dejarnos tus comentarios sobre la atención al cliente que has recibido en cualquiera de nuestros concesionarios autorizados.
            </p>
        </header>

        <div class="container" style="max-width: 800px; margin: 0 auto; padding-bottom: 60px;">
            
            <!-- Instructions List -->
            <div class="buzon-instructions" style="background: #f9fafb; border-left: 4px solid var(--toyota-red); padding: 20px; border-radius: 8px; margin-bottom: 40px;">
                <p style="font-weight: 700; margin-bottom: 10px;">Debes tomar en cuenta:</p>
                <ul style="margin: 0; padding-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;">Llenar todas las celdas donde se solicita información.</li>
                    <li style="margin-bottom: 8px;">Verificar sus datos de contacto antes de enviarlos.</li>
                    <li>En la descripción de la situación ser lo más específico posible.</li>
                </ul>
                <p style="margin-top: 15px; font-style: italic;">Nuestro personal de Atención al Cliente se pondrá en contacto contigo a la brevedad posible.</p>
            </div>

            <!-- Form -->
             <div class="form-wrapper" style="background: #fff; padding: clamp(24px, 5vw, 40px); border-radius: var(--tm-radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border: 1px solid var(--tm-border);">
                
                <?php if ($msg_status == 'success'): ?>
                    <div role="status" style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        ¡Gracias! Tu mensaje ha sido enviado correctamente.
                    </div>
                <?php elseif ($msg_status == 'error'): ?>
                    <div role="alert" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        Hubo un error al enviar tu mensaje. Por favor intenta nuevamente.
                    </div>
                <?php elseif ($msg_status == 'incomplete'): ?>
                    <div role="alert" style="background: #fef9c3; color: #854d0e; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        Revisa los campos requeridos, sus longitudes y la aceptación de privacidad.
                    </div>
                <?php elseif ($msg_status == 'rate'): ?>
                    <div role="alert" style="background: #fef9c3; color: #854d0e; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        Has realizado varios envíos. Intenta de nuevo en unos minutos.
                    </div>
                <?php elseif ($msg_status == 'security'): ?>
                    <div role="alert" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        La sesión del formulario expiró. Recarga la página e intenta nuevamente.
                    </div>
                <?php endif; ?>

                <form method="post" class="mmorichal-form buzon-form">
                    <?php wp_nonce_field('enviar_sugerencia', 'buzon_nonce'); ?>
                    <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden;">
                        <label for="b_website">Sitio web</label>
                        <input type="text" id="b_website" name="b_website" value="" tabindex="-1" autocomplete="off">
                    </div>
                    
                    <div class="mf-row">
                        <label for="b_nombre">Nombre completo</label>
                        <input type="text" id="b_nombre" name="b_nombre" maxlength="100" autocomplete="name" placeholder="Ej: Juan Pérez" required>
                    </div>

                    <div class="mf-row">
                        <label for="b_email">Correo electrónico</label>
                        <input type="email" id="b_email" name="b_email" maxlength="254" autocomplete="email" placeholder="Ej: juan@example.com" required>
                    </div>

                    <div class="mf-row">
                        <label for="b_area">Área</label>
                        <div class="select-wrapper">
                            <select id="b_area" name="b_area" required>
                                <option value="" disabled selected>Selecciona un área</option>
                                <option value="Stock">Stock</option>
                                <option value="Venta">Venta</option>
                                <option value="Atención al cliente">Atención al cliente</option>
                                <option value="Seguridad">Seguridad</option>
                            </select>
                        </div>
                    </div>

                    <div class="mf-row">
                        <label for="b_direccion">Dirección</label>
                        <input type="text" id="b_direccion" name="b_direccion" maxlength="200" autocomplete="street-address" placeholder="Tu dirección completa">
                    </div>

                    <div class="mf-row">
                        <label for="b_whatsapp">Número de Contacto (WhatsApp)</label>
                        <input type="tel" id="b_whatsapp" name="b_whatsapp" maxlength="40" autocomplete="tel" placeholder="Ej: +58 424 0000000" pattern="[\+]?[0-9\s().-]{7,40}" title="Ingresa un número de teléfono válido">
                    </div>

                    <div class="mf-row">
                        <label for="b_mensaje">Descripción / Mensaje</label>
                        <textarea id="b_mensaje" name="b_mensaje" rows="5" maxlength="4000" placeholder="Cuéntanos los detalles..." required></textarea>
                    </div>

                    <?php toyota_monagas_privacy_consent_field('buzon'); ?>

                    <button type="submit" class="mf-submit">
                        Enviar Sugerencia
                    </button>
                </form>
            </div>

        </div>
    </section>

</main>

<style>
/* Page specific minimal adjustments if needed, otherwise relying on theme mmorichal-form */
.buzon-page .select-wrapper select {
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1em;
}
</style>

<?php get_footer(); ?>
