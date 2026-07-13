<?php
/**
 * Contact System & SMTP Module
 * Handles Lead tracking (CPT), SMTP configuration, and Email Notifications.
 */

if (!defined('ABSPATH')) exit;

/**
 * 1. Register Hidden CPT for Leads
 */
function mm_register_lead_cpt() {
    $labels = array(
        'name'               => 'Contactos',
        'singular_name'      => 'Contacto',
        'menu_name'          => 'Contactos',
        'add_new'            => 'Añadir Manual',
        'add_new_item'       => 'Añadir Nuevo Lead',
        'edit_item'          => 'Ver Lead',
        'new_item'           => 'Nuevo Lead',
        'view_item'          => 'Ver Lead',
        'search_items'       => 'Buscar Contactos',
        'not_found'          => 'No se encontraron contactos',
        'not_found_in_trash' => 'No hay contactos en la papelera'
    );
    $args = array(
        'labels'              => $labels,
        'public'              => false,  // Not visible on frontend
        'show_ui'             => true,   // Visible in admin
        'show_in_menu'        => true,
        'menu_position'       => 26,
        'menu_icon'           => 'dashicons-email',
        'capability_type'     => 'mm_lead',
        'map_meta_cap'        => true,
        'capabilities' => array(
            'edit_post' => 'edit_mm_lead',
            'read_post' => 'read_mm_lead',
            'delete_post' => 'delete_mm_lead',
            'edit_posts' => 'edit_mm_leads',
            'edit_others_posts' => 'edit_others_mm_leads',
            'publish_posts' => 'publish_mm_leads',
            'read_private_posts' => 'read_private_mm_leads',
        ),
        'has_archive'         => false,
        'rewrite'             => false,
        'exclude_from_search' => true,
    );
    register_post_type('mm_lead', $args);
}
add_action('init', 'mm_register_lead_cpt');

function mm_grant_lead_caps() {
    $role = get_role('administrator');
    if ($role && !$role->has_cap('edit_mm_leads')) {
        $role->add_cap('edit_mm_lead');
        $role->add_cap('read_mm_lead');
        $role->add_cap('delete_mm_lead');
        $role->add_cap('edit_mm_leads');
        $role->add_cap('edit_others_mm_leads');
        $role->add_cap('publish_mm_leads');
        $role->add_cap('read_private_mm_leads');
    }
}
add_action('admin_init', 'mm_grant_lead_caps');

/**
 * 2. Custom Columns for Leads
 */
function mm_set_lead_columns($columns) {
    // Rearrange columns
    $new = array();
    $new['cb'] = $columns['cb'];
    $new['title'] = 'Nombre / Referencia';
    $new['con_type'] = 'Tipo';
    $new['con_email'] = 'Email / Info';
    $new['con_phone'] = 'Teléfono';
    $new['date'] = 'Fecha';
    return $new;
}
add_filter('manage_mm_lead_posts_columns', 'mm_set_lead_columns');

function mm_lead_custom_column($column, $post_id) {
    switch ($column) {
        case 'con_type':
            $type = get_post_meta($post_id, 'lead_type', true); // 'whatsapp' or 'email'
            if ($type === 'whatsapp') {
                echo '<span style="color:#25D366; font-weight:bold;"><span class="dashicons dashicons-whatsapp"></span> WhatsApp</span>';
            } elseif ($type === 'email') {
                echo '<span style="color:#0073aa; font-weight:bold;"><span class="dashicons dashicons-email"></span> Email</span>';
            } else {
                echo ucfirst($type);
            }
            break;
        case 'con_email':
            echo get_post_meta($post_id, 'lead_email', true);
            break;
        case 'con_phone':
            echo get_post_meta($post_id, 'lead_phone', true);
            break;
    }
}
add_action('manage_mm_lead_posts_custom_column', 'mm_lead_custom_column', 10, 2);

/**
 * 3. Settings Page (SMTP & Config)
 */
function mm_contact_add_submenu() {
    add_submenu_page(
        'edit.php?post_type=mm_lead',
        'Configuración Contacto',
        'Configuración',
        'manage_options',
        'mm-contact-settings',
        'mm_contact_settings_page'
    );
}
add_action('admin_menu', 'mm_contact_add_submenu');

function mm_register_contact_settings() {
    register_setting('mm_contact_opts', 'mm_smtp_host');
    register_setting('mm_contact_opts', 'mm_smtp_port'); // 465 or 587
    register_setting('mm_contact_opts', 'mm_smtp_user');
    register_setting('mm_contact_opts', 'mm_smtp_pass');
    register_setting('mm_contact_opts', 'mm_smtp_secure'); // 'ssl' or 'tls'
    register_setting('mm_contact_opts', 'mm_notify_email', array('default' => 'arturomerchan18@gmail.com'));
}
add_action('admin_init', 'mm_register_contact_settings');

function mm_contact_settings_page() {
    ?>
    <div class="wrap">
        <h1>Configuración de Contacto y SMTP</h1>
        <form method="post" action="options.php">
            <?php settings_fields('mm_contact_opts'); ?>
            <?php do_settings_sections('mm_contact_opts'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Email de Notificaciones</th>
                    <td>
                        <input type="email" name="mm_notify_email" value="<?php echo esc_attr(get_option('mm_notify_email', 'arturomerchan18@gmail.com')); ?>" class="regular-text" />
                        <p class="description">A este correo llegarán los formularios recibidos.</p>
                    </td>
                </tr>
                <tr><td colspan="2"><hr><h3>Configuración SMTP (Salida)</h3></td></tr>
                <tr valign="top">
                    <th scope="row">SMTP Host</th>
                    <td><input type="text" name="mm_smtp_host" value="<?php echo esc_attr(get_option('mm_smtp_host')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Port</th>
                    <td><input type="number" name="mm_smtp_port" value="<?php echo esc_attr(get_option('mm_smtp_port')); ?>" class="small-text" /> (465 para SSL, 587 para TLS)</td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Encryption</th>
                    <td>
                        <select name="mm_smtp_secure">
                            <option value="ssl" <?php selected(get_option('mm_smtp_secure'), 'ssl'); ?>>SSL</option>
                            <option value="tls" <?php selected(get_option('mm_smtp_secure'), 'tls'); ?>>TLS</option>
                            <option value="" <?php selected(get_option('mm_smtp_secure'), ''); ?>>Ninguna</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Username</th>
                    <td><input type="text" name="mm_smtp_user" value="<?php echo esc_attr(get_option('mm_smtp_user')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Password</th>
                    <td><input type="password" name="mm_smtp_pass" value="" placeholder="Dejar vacío para conservar actual" class="regular-text" /></td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Prevent overwriting SMTP password with empty value if not changed
add_filter('pre_update_option_mm_smtp_pass', function($new_value, $old_value) {
    if (empty($new_value)) {
        return $old_value;
    }
    return $new_value;
}, 10, 2);

/**
 * 4. Apply SMTP Settings
 */
function mm_apply_smtp($phpmailer) {
    $host = get_option('mm_smtp_host');
    $user = get_option('mm_smtp_user');
    $pass = get_option('mm_smtp_pass');

    if ($host && $user && $pass) {
        $phpmailer->isSMTP();
        $phpmailer->Host       = $host;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = get_option('mm_smtp_port', 465);
        $phpmailer->Username   = $user;
        $phpmailer->Password   = $pass;
        $phpmailer->SMTPSecure = get_option('mm_smtp_secure', 'ssl');
        $phpmailer->From       = $user;
        $phpmailer->FromName   = get_bloginfo('name');
    }
}
add_action('phpmailer_init', 'mm_apply_smtp');

/**
 * 5. AJAX Handler: Submit Form
 */
function mm_ajax_submit_contact() {
    // 1. Verify Nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'mm_contact_nonce')) {
        wp_send_json_error(array('message' => 'Error de seguridad. Por favor recarga la página.'));
    }

    // Rate Limiting by IP
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : 'unknown';
    $transient_name = 'mm_limit_' . md5($ip);
    if (get_transient($transient_name)) {
        wp_send_json_error(array('message' => 'Por favor espera unos segundos antes de enviar otro mensaje.'));
    }
    set_transient($transient_name, true, 30); // 30 seconds limit

    $type  = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'email';
    $name  = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : 'Anónimo';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $msg   = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    $model = isset($_POST['model']) ? sanitize_text_field($_POST['model']) : '';

    // Create Title
    $title = ($type === 'whatsapp' ? 'Click WhatsApp' : 'Mensaje Web') . " - $name";
    if ($model) $title .= " ($model)";

    // Insert Post
    $post_id = wp_insert_post(array(
        'post_title'   => $title,
        'post_type'    => 'mm_lead',
        'post_status'  => 'publish',
        'post_content' => $msg
    ));

    if ($post_id) {
        // Save Metadata
        update_post_meta($post_id, 'lead_type', $type);
        update_post_meta($post_id, 'lead_name', $name);
        update_post_meta($post_id, 'lead_email', $email);
        update_post_meta($post_id, 'lead_phone', $phone);
        update_post_meta($post_id, 'lead_model', $model);

        // Send Email Notification if it's a Form submission
        if ($type === 'email') {
            $to = get_option('mm_notify_email', get_option('admin_email'));
            $subject = "Nuevo Lead: $name ($model)";
            $body = mm_get_email_template(array(
                'name'    => $name,
                'email'   => $email,
                'phone'   => $phone,
                'model'   => $model,
                'message' => $msg
            ));
            
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $sent = wp_mail($to, $subject, $body, $headers);
        }

        wp_send_json_success(array('message' => 'Enviado correctamente'));
    } else {
        wp_send_json_error(array('message' => 'Error al guardar'));
    }
}
add_action('wp_ajax_mm_submit_contact_form', 'mm_ajax_submit_contact');
add_action('wp_ajax_nopriv_mm_submit_contact_form', 'mm_ajax_submit_contact');

/**
 * 6. Beautiful Email Template
 */
function mm_get_email_template($data) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: 'Helvetica', sans-serif; background-color: #f4f4f4; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
            .header { border-bottom: 2px solid #EB0A1E; padding-bottom: 20px; margin-bottom: 20px; }
            .header h2 { margin: 0; color: #EB0A1E; }
            .row { margin-bottom: 12px; }
            .label { font-weight: bold; color: #333; display: block; margin-bottom: 4px; }
            .value { color: #555; background: #f9f9f9; padding: 8px; border-radius: 4px; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #999; text-align: center; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Nuevo Contacto Web</h2>
                <p>Has recibido un nuevo mensaje desde el sitio web.</p>
            </div>
            
            <div class="row">
                <span class="label">Nombre:</span>
                <div class="value"><?php echo esc_html($data['name']); ?></div>
            </div>
            
            <?php if(!empty($data['email'])): ?>
            <div class="row">
                <span class="label">Email:</span>
                <div class="value"><a href="mailto:<?php echo esc_attr($data['email']); ?>"><?php echo esc_html($data['email']); ?></a></div>
            </div>
            <?php endif; ?>

            <?php if(!empty($data['phone'])): ?>
            <div class="row">
                <span class="label">Teléfono:</span>
                <div class="value"><a href="tel:<?php echo esc_attr($data['phone']); ?>"><?php echo esc_html($data['phone']); ?></a></div>
            </div>
            <?php endif; ?>

            <?php if(!empty($data['model'])): ?>
            <div class="row">
                <span class="label">Vehículo de Interés:</span>
                <div class="value" style="color:#EB0A1E; font-weight:bold;"><?php echo esc_html($data['model']); ?></div>
            </div>
            <?php endif; ?>

            <div class="row">
                <span class="label">Mensaje:</span>
                <div class="value"><?php echo nl2br(esc_html($data['message'])); ?></div>
            </div>

            <div class="footer">
                <p>Este mensaje fue enviado automáticamente por el sistema web de Toyota Monagas.</p>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}
