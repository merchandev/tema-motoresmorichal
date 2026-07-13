<?php
/**
 * Template Name: Buzón de Sugerencia
 */

// Logic to handle form submission
$msg_status = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buzon_nonce']) && wp_verify_nonce($_POST['buzon_nonce'], 'enviar_sugerencia')) {
    $nombre = sanitize_text_field($_POST['b_nombre']);
    $email = sanitize_email($_POST['b_email']);
    $area = sanitize_text_field($_POST['b_area']);
    $direccion = isset($_POST['b_direccion']) ? sanitize_text_field($_POST['b_direccion']) : 'No indicada';
    $whatsapp = isset($_POST['b_whatsapp']) ? sanitize_text_field($_POST['b_whatsapp']) : 'No indicado';
    $mensaje = sanitize_textarea_field($_POST['b_mensaje']);

    if ($nombre && $email && $area && $mensaje) {
        $to = get_option('admin_email'); // Send to admin email
        $subject = 'Nueva Sugerencia/Reclamo: ' . $area;
        $body = "Has recibido una nueva sugerencia o reclamo desde el sitio web.\n\n";
        $body .= "Nombre: " . $nombre . "\n";
        $body .= "Correo: " . $email . "\n";
        $body .= "Teléfono/WhatsApp: " . $whatsapp . "\n";
        $body .= "Dirección: " . $direccion . "\n";
        $body .= "Área: " . $area . "\n";
        $body .= "Mensaje:\n" . $mensaje . "\n\n";
        $headers = array('Content-Type: text/plain; charset=UTF-8', 'From: ' . $nombre . ' <' . $email . '>');

        if (wp_mail($to, $subject, $body, $headers)) {
            $msg_status = 'success';
        } else {
            $msg_status = 'error';
        }
    } else {
        $msg_status = 'incomplete';
    }
}

get_header(); ?>

<main id="site-main" class="buzon-page">
    
    <!-- Header Section matching theme style -->
    <section class="contacto-header" style="padding-top: var(--header-h, 80px);">
        <header class="section-header text-center">
            <span class="kicker" style="color: var(--toyota-red); font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">Buzón</span>
            <h1 style="color: var(--toyota-black-2); font-size: 2.5rem; margin: 10px 0;">Buzón de Sugerencias</h1>
            <p style="color: #4b5563; max-width: 700px; margin: 0 auto 30px;">
                Utilizando el siguiente formulario podrás ponerte en contacto con nosotros para realizar cualquier reclamo o dejarnos tus comentarios sobre la atención al cliente que has recibido en cualquiera de nuestros concesionaros autorizados.
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
             <div class="form-wrapper" style="background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">
                
                <?php if ($msg_status == 'success'): ?>
                    <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        ¡Gracias! Tu mensaje ha sido enviado correctamente.
                    </div>
                <?php elseif ($msg_status == 'error'): ?>
                    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        Hubo un error al enviar tu mensaje. Por favor intenta nuevamente.
                    </div>
                <?php elseif ($msg_status == 'incomplete'): ?>
                    <div style="background: #fef9c3; color: #854d0e; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                        Por favor completa todos los campos requeridos.
                    </div>
                <?php endif; ?>

                <form method="post" class="mmorichal-form buzon-form">
                    <?php wp_nonce_field('enviar_sugerencia', 'buzon_nonce'); ?>
                    
                    <div class="mf-row">
                        <label for="b_nombre">Nombre completo</label>
                        <input type="text" id="b_nombre" name="b_nombre" placeholder="Ej: Juan Pérez" required>
                    </div>

                    <div class="mf-row">
                        <label for="b_email">Correo electrónico</label>
                        <input type="email" id="b_email" name="b_email" placeholder="Ej: juan@example.com" required>
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
                        <input type="text" id="b_direccion" name="b_direccion" placeholder="Tu dirección completa">
                    </div>

                    <div class="mf-row">
                        <label for="b_whatsapp">Número de Contacto (WhatsApp)</label>
                        <input type="tel" id="b_whatsapp" name="b_whatsapp" placeholder="Ej: +58 424 0000000" pattern="[\+]?[0-9\s-]*" title="Ingresa un número de teléfono válido">
                    </div>

                    <div class="mf-row">
                        <label for="b_mensaje">Descripción / Mensaje</label>
                        <textarea id="b_mensaje" name="b_mensaje" rows="5" placeholder="Cuéntanos los detalles..." required></textarea>
                    </div>

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
