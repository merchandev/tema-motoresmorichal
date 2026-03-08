<?php
/**
 * Template Name: Atención al Cliente
 */
get_header(); 
$wa_number = apply_filters('mmorichal_whatsapp_number', '584249090679'); // Default number if not set
?>

<main id="site-main" class="atencion-page">
    
    <!-- Header Section -->
    <section class="contacto-header" style="padding-top: var(--header-h, 80px);">
        <header class="section-header text-center">
            <span class="kicker" style="color: var(--toyota-red); font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">Contacto</span>
            <h1 style="color: var(--toyota-black-2); font-size: 2.5rem; margin: 10px 0;">Atención al Cliente</h1>
            <p style="color: #4b5563; max-width: 700px; margin: 0 auto 30px;">
                Escríbenos directamente a nuestro WhatsApp para resolver tus dudas o inquietudes sobre nuestros servicios.
            </p>
        </header>

        <div class="container" style="max-width: 600px; margin: 0 auto; padding-bottom: 60px;">
            
            <div class="form-wrapper" style="background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">
                <form class="mmorichal-form atencion-form" onsubmit="return enviarAtencionWhatsApp(this);">
                    
                    <div class="mf-row">
                        <label for="ac_nombre">Nombre completo</label>
                        <input type="text" id="ac_nombre" name="ac_nombre" placeholder="Ej: Maria Gonzalez" required>
                    </div>

                     <div class="mf-row">
                        <label for="ac_asunto">Asunto</label>
                        <select id="ac_asunto" name="ac_asunto" required>
                            <option value="Atención al Cliente" selected>Atención al Cliente</option>
                            <option value="Consulta General">Consulta General</option>
                            <option value="Estatus de Servicio">Estatus de Servicio</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="mf-row">
                        <label for="ac_mensaje">Mensaje</label>
                        <textarea id="ac_mensaje" name="ac_mensaje" rows="4" placeholder="¿En qué podemos ayudarte?" required></textarea>
                    </div>

                    <button type="submit" class="mf-submit">
                        <span class="mf-icon" aria-hidden="true">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-whatsapp.webp" alt="WhatsApp" width="24" height="24" loading="lazy" />
                        </span>
                        Contactar Atención al Cliente
                    </button>
                    <!-- Removing the paragraph note as it was not in the user snippet request explicitly, though it was in my prior version. User snippet didn't have it but I'll leave it as a nice touch? No, strict adherence to "dale a los formularios el estilo de este formulario" which ends at </div>. But wait, the user provided snippet HAS a form closing tag but NO paragraph at the end. I will stick to what the user provided mostly, but I will keep the JS script. -->
                </form>
            </div>

        </div>
    </section>

</main>

<script>
    function enviarAtencionWhatsApp(form) {
        try {
            var nombre = form.querySelector('#ac_nombre').value.trim();
            var asunto = form.querySelector('#ac_asunto').value;
            var mensajeLibre = form.querySelector('#ac_mensaje').value.trim();

            if (!nombre || !mensajeLibre) { return false; }

            var mensaje = 'Hola Motores Morichal, tengo una consulta de Atención al Cliente:%0A' +
                          '• Nombre: ' + encodeURIComponent(nombre) + '%0A' +
                          '• Asunto: ' + encodeURIComponent(asunto) + '%0A' +
                          '• Mensaje: ' + encodeURIComponent(mensajeLibre);

            var numero = '<?php echo esc_js($wa_number); ?>';
            var url = 'https://wa.me/' + numero + '?text=' + mensaje;
            window.open(url, '_blank');
        } catch (e) { console.error(e); }
        return false;
    }
</script>

<?php get_footer(); ?>
