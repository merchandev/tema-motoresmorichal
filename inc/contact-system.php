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
        'capability_type'     => array('mm_lead', 'mm_leads'),
        'map_meta_cap'        => true,
        'capabilities' => array(
            'edit_post'              => 'edit_mm_lead',
            'read_post'              => 'read_mm_lead',
            'delete_post'            => 'delete_mm_lead',
            'edit_posts'             => 'edit_mm_leads',
            'edit_others_posts'      => 'edit_others_mm_leads',
            'edit_private_posts'     => 'edit_private_mm_leads',
            'edit_published_posts'   => 'edit_published_mm_leads',
            'publish_posts'          => 'publish_mm_leads',
            'read_private_posts'     => 'read_private_mm_leads',
            'delete_posts'           => 'delete_mm_leads',
            'delete_private_posts'   => 'delete_private_mm_leads',
            'delete_published_posts' => 'delete_published_mm_leads',
            'delete_others_posts'    => 'delete_others_mm_leads',
            'create_posts'           => 'edit_mm_leads',
        ),
        'has_archive'         => false,
        'rewrite'             => false,
        'exclude_from_search' => true,
    );
    register_post_type('mm_lead', $args);
}
add_action('init', 'mm_register_lead_cpt');

function mm_grant_lead_caps() {
    $version = '2026-08-24.1';
    if (get_option('mm_lead_caps_version') === $version) return;

    $role = get_role('administrator');
    if (!$role) return;

    $caps = array(
        'edit_mm_leads',
        'edit_others_mm_leads',
        'edit_private_mm_leads',
        'edit_published_mm_leads',
        'publish_mm_leads',
        'read_private_mm_leads',
        'delete_mm_leads',
        'delete_private_mm_leads',
        'delete_published_mm_leads',
        'delete_others_mm_leads',
    );
    foreach ($caps as $cap) {
        $role->add_cap($cap);
    }

    update_option('mm_lead_caps_version', $version, false);
}
add_action('admin_init', 'mm_grant_lead_caps');
add_action('after_switch_theme', 'mm_grant_lead_caps');

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
                echo esc_html(ucfirst($type));
            }
            break;
        case 'con_email':
            echo esc_html(get_post_meta($post_id, 'lead_email', true));
            break;
        case 'con_phone':
            echo esc_html(get_post_meta($post_id, 'lead_phone', true));
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

function mm_sanitize_smtp_host($value) {
    $value = trim(sanitize_text_field($value));
    $value = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', $value);
    return is_string($value) ? $value : '';
}

function mm_sanitize_smtp_port($value) {
    $port = absint($value);
    if ($port < 1 || $port > 65535) {
        add_settings_error('mm_contact_opts', 'mm_smtp_port_invalid', 'El puerto SMTP debe estar entre 1 y 65535.');
        $previous = absint(get_option('mm_smtp_port', 465));
        return ($previous >= 1 && $previous <= 65535) ? $previous : 465;
    }
    return $port;
}

function mm_sanitize_smtp_secure($value) {
    $value = sanitize_key($value);
    return in_array($value, array('', 'ssl', 'tls'), true) ? $value : 'tls';
}

function mm_sanitize_notify_email($value) {
    $email = sanitize_email($value);
    if (!is_email($email)) {
        add_settings_error('mm_contact_opts', 'mm_notify_email_invalid', 'Introduce un correo de notificaciones válido.');
        $previous = sanitize_email(get_option('mm_notify_email', get_option('admin_email')));
        return is_email($previous) ? $previous : sanitize_email(get_option('admin_email'));
    }
    return $email;
}

function mm_external_smtp_password() {
    if (defined('MM_SMTP_PASSWORD')) {
        $value = constant('MM_SMTP_PASSWORD');
        return is_scalar($value) ? (string) $value : '';
    }

    $value = getenv('MM_SMTP_PASSWORD');
    return is_string($value) ? $value : '';
}

function mm_purge_legacy_smtp_password() {
    if (strtoupper(toyota_monagas_request_scalar($_SERVER, 'REQUEST_METHOD')) !== 'POST') {
        wp_die(esc_html__('Método no permitido.', 'toyota-monagas'), '', array('response' => 405));
    }
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('No tienes permiso para realizar esta acción.', 'toyota-monagas'), '', array('response' => 403));
    }

    check_admin_referer('mm_purge_legacy_smtp_password');

    if (mm_external_smtp_password() === '') {
        $status = 'inactive';
    } else {
        delete_option('mm_smtp_pass');
        $status = (string) get_option('mm_smtp_pass', '') === '' ? 'success' : 'error';
    }

    $redirect_url = add_query_arg(
        'mm_smtp_purge',
        $status,
        admin_url('edit.php?post_type=mm_lead&page=mm-contact-settings')
    );
    wp_safe_redirect($redirect_url, 303);
    exit;
}
add_action('admin_post_mm_purge_legacy_smtp_password', 'mm_purge_legacy_smtp_password');

function mm_sanitize_smtp_password($value) {
    if (mm_external_smtp_password() !== '') {
        // A disabled field is absent from the settings POST. Preserve any
        // legacy value until the administrator uses the explicit purge action.
        return (string) get_option('mm_smtp_pass', '');
    }
    if (isset($_POST['mm_smtp_pass_clear'])) {
        return '';
    }

    // options.php passes an unslashed value to the registered sanitizer.
    $value = is_string($value) ? $value : '';
    if ($value === '') {
        return (string) get_option('mm_smtp_pass', '');
    }
    return $value;
}

function mm_register_contact_settings() {
    register_setting('mm_contact_opts', 'mm_smtp_host', array(
        'type' => 'string',
        'sanitize_callback' => 'mm_sanitize_smtp_host',
        'default' => '',
    ));
    register_setting('mm_contact_opts', 'mm_smtp_port', array(
        'type' => 'integer',
        'sanitize_callback' => 'mm_sanitize_smtp_port',
        'default' => 465,
    ));
    register_setting('mm_contact_opts', 'mm_smtp_user', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ));
    register_setting('mm_contact_opts', 'mm_smtp_pass', array(
        'type' => 'string',
        'sanitize_callback' => 'mm_sanitize_smtp_password',
        'default' => '',
    ));
    register_setting('mm_contact_opts', 'mm_smtp_secure', array(
        'type' => 'string',
        'sanitize_callback' => 'mm_sanitize_smtp_secure',
        'default' => 'ssl',
    ));
    register_setting('mm_contact_opts', 'mm_notify_email', array(
        'type' => 'string',
        'sanitize_callback' => 'mm_sanitize_notify_email',
        'default' => get_option('admin_email'),
    ));
}
add_action('admin_init', 'mm_register_contact_settings');

function mm_contact_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('No tienes permiso para acceder a esta página.', 'toyota-monagas'));
    }
    $external_smtp_password = mm_external_smtp_password() !== '';
    $has_legacy_smtp_password = (string) get_option('mm_smtp_pass', '') !== '';
    $purge_status = sanitize_key(toyota_monagas_request_scalar($_GET, 'mm_smtp_purge'));
    ?>
    <div class="wrap">
        <h1>Configuración de Contacto y SMTP</h1>
        <?php if ($purge_status === 'success') : ?>
            <div class="notice notice-success is-dismissible"><p>La contraseña SMTP heredada fue eliminada de WordPress.</p></div>
        <?php elseif ($purge_status === 'inactive') : ?>
            <div class="notice notice-error"><p>La contraseña guardada no se eliminó porque el override externo no está activo.</p></div>
        <?php elseif ($purge_status === 'error') : ?>
            <div class="notice notice-error"><p>No se pudo eliminar la contraseña SMTP heredada.</p></div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php settings_fields('mm_contact_opts'); ?>
            <?php do_settings_sections('mm_contact_opts'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Email de Notificaciones</th>
                    <td>
                        <input type="email" name="mm_notify_email" value="<?php echo esc_attr(get_option('mm_notify_email', get_option('admin_email'))); ?>" class="regular-text" />
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
                    <td>
                        <input type="password" name="mm_smtp_pass" value="" placeholder="Dejar vacío para conservar actual" class="regular-text" autocomplete="new-password" <?php disabled($external_smtp_password); ?> />
                        <label style="display:block;margin-top:8px;">
                            <input type="checkbox" name="mm_smtp_pass_clear" value="1" <?php disabled($external_smtp_password); ?> /> Borrar la contraseña guardada
                        </label>
                        <?php if ($external_smtp_password) : ?>
                            <p class="description">La contraseña se obtiene de <code>MM_SMTP_PASSWORD</code> y no se guardará en WordPress.</p>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>

        <?php if ($external_smtp_password && $has_legacy_smtp_password) : ?>
            <div class="card" style="max-width:760px;">
                <h2>Contraseña SMTP heredada</h2>
                <p>El override externo está activo, pero WordPress todavía conserva una contraseña anterior. Elimínala solo después de verificar que el correo funciona con <code>MM_SMTP_PASSWORD</code>.</p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="mm_purge_legacy_smtp_password">
                    <?php wp_nonce_field('mm_purge_legacy_smtp_password'); ?>
                    <?php submit_button('Eliminar contraseña heredada', 'delete', 'submit', false); ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 4. Apply SMTP Settings
 */
function mm_apply_smtp($phpmailer) {
    $host = mm_sanitize_smtp_host(get_option('mm_smtp_host'));
    $user = sanitize_text_field(get_option('mm_smtp_user'));
    $external_pass = mm_external_smtp_password();
    $pass = $external_pass !== '' ? $external_pass : (string) get_option('mm_smtp_pass');
    $port = absint(get_option('mm_smtp_port', 465));
    $secure = sanitize_key(get_option('mm_smtp_secure', 'ssl'));
    if ($port < 1 || $port > 65535) $port = 465;
    if (!in_array($secure, array('', 'ssl', 'tls'), true)) $secure = 'ssl';

    if ($host && $user && $pass) {
        $phpmailer->isSMTP();
        $phpmailer->Host       = $host;
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = $port;
        $phpmailer->Username   = $user;
        $phpmailer->Password   = $pass;
        $phpmailer->SMTPSecure = $secure;
        if (is_email($user)) {
            $phpmailer->From = $user;
        }
        $phpmailer->FromName   = get_bloginfo('name');
    }
}
add_action('phpmailer_init', 'mm_apply_smtp');

function mm_contact_text_length($value) {
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

/**
 * Shared rate limiter. Sites behind a trusted proxy may filter the identifier,
 * but must never trust a client-supplied forwarding header directly.
 */
function mm_contact_rate_limit_allows($bucket, $limit = 5, $window = 600) {
    $remote = sanitize_text_field(toyota_monagas_request_scalar($_SERVER, 'REMOTE_ADDR', 'unknown'));
    $identifier = apply_filters('mm_contact_rate_limit_identifier', $remote, $bucket);
    if (!is_scalar($identifier) || (string) $identifier === '') $identifier = $remote;

    $limit = max(1, absint($limit));
    $window = max(60, absint($window));
    $slot = (int) floor(time() / $window);
    $expires_at = ($slot + 1) * $window;
    $expiry_token = str_pad((string) $expires_at, 10, '0', STR_PAD_LEFT);
    $key = 'mmrl_' . $expiry_token . '_' . md5(wp_salt('nonce') . '|' . sanitize_key($bucket) . '|' . (string) $identifier . '|' . $slot);

    // add_option() relies on the unique option_name index, so the first hit is
    // atomic even when several PHP workers receive the same request at once.
    if (add_option($key, 1, '', false)) {
        return true;
    }

    global $wpdb;
    $updated = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->options}
         SET option_value = CAST(option_value AS UNSIGNED) + 1
         WHERE option_name = %s
           AND CAST(option_value AS UNSIGNED) < %d",
        $key,
        $limit
    ));
    wp_cache_delete($key, 'options');

    return $updated === 1;
}

/**
 * Keep exactly one recurring cleanup event instead of one cron entry per
 * public form identity. The minute-aligned timestamp is stable under races.
 */
function mm_contact_ensure_rate_limit_cleanup_event() {
    if (wp_next_scheduled('mm_contact_sweep_rate_limits') !== false) return;

    $next_minute = ((int) floor(time() / MINUTE_IN_SECONDS) + 1) * MINUTE_IN_SECONDS;
    wp_schedule_event($next_minute, 'hourly', 'mm_contact_sweep_rate_limits');
}
add_action('init', 'mm_contact_ensure_rate_limit_cleanup_event', 20);

function mm_contact_unschedule_rate_limit_cleanup_event() {
    wp_clear_scheduled_hook('mm_contact_sweep_rate_limits');
}
add_action('switch_theme', 'mm_contact_unschedule_rate_limit_cleanup_event');

/**
 * Delete only exact rate-limit keys owned by this theme. Batches keep cron
 * bounded while allowing a busy site to drain several thousand stale rows.
 */
function mm_contact_sweep_rate_limits() {
    global $wpdb;

    $like = $wpdb->esc_like('mmrl_') . '%';
    $now = time();
    $batch_size = 500;

    for ($batch = 0; $batch < 10; $batch++) {
        $keys = $wpdb->get_col($wpdb->prepare(
            "SELECT option_name
             FROM {$wpdb->options}
             WHERE option_name LIKE %s
             ORDER BY option_id ASC
             LIMIT %d",
            $like,
            $batch_size
        ));
        if (empty($keys)) break;

        $delete_keys = array();
        foreach ($keys as $key) {
            if (!is_string($key)) continue;

            // Legacy keys had no expiry and are safe to remove during migration.
            if (preg_match('/^mmrl_[a-f0-9]{32}$/D', $key)) {
                $delete_keys[] = $key;
                continue;
            }
            if (preg_match('/^mmrl_([0-9]{10,12})_[a-f0-9]{32}$/D', $key, $matches)
                && (int) $matches[1] <= $now) {
                $delete_keys[] = $key;
            }
        }

        if (empty($delete_keys)) break;

        $placeholders = implode(', ', array_fill(0, count($delete_keys), '%s'));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name IN ({$placeholders})",
            $delete_keys
        ));
        foreach ($delete_keys as $key) {
            wp_cache_delete($key, 'options');
        }
        if (count($keys) < $batch_size) break;
    }
}
add_action('mm_contact_sweep_rate_limits', 'mm_contact_sweep_rate_limits');

// Compatibility cleanup for single events queued by versions prior to 1.2.0.
function mm_contact_clear_rate_limit($key) {
    if (is_string($key) && preg_match('/^mmrl_[a-f0-9]{32}$/D', $key)) {
        delete_option($key);
    }
}
add_action('mm_contact_clear_rate_limit', 'mm_contact_clear_rate_limit');

function mm_contact_mail_headers($name, $email, $html = false) {
    $headers = array($html ? 'Content-Type: text/html; charset=UTF-8' : 'Content-Type: text/plain; charset=UTF-8');
    $host = wp_parse_url(home_url('/'), PHP_URL_HOST);
    $host = is_string($host) ? preg_replace('/^www\./i', '', $host) : '';
    $from = $host ? sanitize_email('wordpress@' . $host) : '';
    $site_name = sanitize_text_field(wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));

    if (is_email($from)) {
        $headers[] = 'From: ' . $site_name . ' <' . $from . '>';
    }
    if (is_email($email)) {
        $headers[] = 'Reply-To: ' . sanitize_text_field($name) . ' <' . sanitize_email($email) . '>';
    }
    return $headers;
}

/**
 * 5. AJAX Handler: Submit Form
 */
function mm_ajax_submit_contact() {
    if (strtoupper(toyota_monagas_request_scalar($_SERVER, 'REQUEST_METHOD')) !== 'POST') {
        wp_send_json_error(array('message' => 'Método no permitido.'), 405);
    }
    $nonce = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'nonce'));
    if (!$nonce || !wp_verify_nonce($nonce, 'mm_contact_nonce')) {
        wp_send_json_error(array('message' => 'Error de seguridad. Por favor recarga la página.'), 403);
    }

    // Optional honeypot for every form that integrates with this endpoint.
    $honeypot = trim(toyota_monagas_request_scalar($_POST, 'company_website'));
    if ($honeypot !== '') {
        wp_send_json_success(array('message' => 'Enviado correctamente'));
    }

    $type  = sanitize_key(toyota_monagas_request_scalar($_POST, 'type', 'email'));
    $name  = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'name'));
    $email = sanitize_email(toyota_monagas_request_scalar($_POST, 'email'));
    $phone = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'phone'));
    $msg   = sanitize_textarea_field(toyota_monagas_request_scalar($_POST, 'message'));
    $model = sanitize_text_field(toyota_monagas_request_scalar($_POST, 'model'));
    $privacy_consent = toyota_monagas_request_scalar($_POST, 'privacy_consent');

    if (!in_array($type, array('email', 'whatsapp'), true)) {
        wp_send_json_error(array('message' => 'Tipo de solicitud no válido.'), 400);
    }
    if ($name === '' || mm_contact_text_length($name) > 100) {
        wp_send_json_error(array('message' => 'Introduce un nombre válido de hasta 100 caracteres.'), 400);
    }
    if (!is_email($email) || mm_contact_text_length($email) > 254) {
        wp_send_json_error(array('message' => 'Introduce un correo válido.'), 400);
    }
    if (mm_contact_text_length($phone) > 40 || ($phone !== '' && !preg_match('/^[0-9+() .-]{7,40}$/', $phone))) {
        wp_send_json_error(array('message' => 'Introduce un teléfono válido.'), 400);
    }
    if (mm_contact_text_length($model) > 120 || mm_contact_text_length($msg) > 4000) {
        wp_send_json_error(array('message' => 'Uno de los campos supera la longitud permitida.'), 400);
    }
    if ($type === 'email' && $msg === '') {
        wp_send_json_error(array('message' => 'Escribe un mensaje.'), 400);
    }
    if ($type === 'whatsapp' && ($phone === '' || $model === '')) {
        wp_send_json_error(array('message' => 'Teléfono y vehículo son obligatorios.'), 400);
    }
    if ($privacy_consent !== '1') {
        wp_send_json_error(array('message' => 'Debes aceptar el tratamiento de datos para enviar la solicitud.'), 400);
    }
    if (!mm_contact_rate_limit_allows('lead', 5, 10 * MINUTE_IN_SECONDS)) {
        wp_send_json_error(array('message' => 'Has realizado varios envíos. Intenta de nuevo más tarde.'), 429);
    }

    // Create Title
    $title = ($type === 'whatsapp' ? 'Click WhatsApp' : 'Mensaje Web') . " - $name";
    if ($model) $title .= " ($model)";

    // Insert Post
    $post_id = wp_insert_post(array(
        'post_title'   => $title,
        'post_type'    => 'mm_lead',
        'post_status'  => 'private',
        'post_content' => $msg
    ), true);

    if (!is_wp_error($post_id) && $post_id) {
        // Save Metadata
        update_post_meta($post_id, 'lead_type', $type);
        update_post_meta($post_id, 'lead_name', $name);
        update_post_meta($post_id, 'lead_email', $email);
        update_post_meta($post_id, 'lead_phone', $phone);
        update_post_meta($post_id, 'lead_model', $model);
        update_post_meta($post_id, 'lead_privacy_consent', '1');
        update_post_meta($post_id, 'lead_privacy_consent_at', current_time('mysql', true));

        // Send Email Notification if it's a Form submission
        if ($type === 'email') {
            $to = sanitize_email(get_option('mm_notify_email', get_option('admin_email')));
            $subject = "Nuevo Lead: $name ($model)";
            $body = mm_get_email_template(array(
                'name'    => $name,
                'email'   => $email,
                'phone'   => $phone,
                'model'   => $model,
                'message' => $msg
            ));
            
            $headers = mm_contact_mail_headers($name, $email, true);
            $sent = is_email($to) && wp_mail($to, $subject, $body, $headers);
            update_post_meta($post_id, 'lead_mail_status', $sent ? 'sent' : 'failed');
            if (!$sent) {
                wp_send_json_error(array(
                    'message' => 'La solicitud quedó guardada, pero no se pudo enviar la notificación por correo.',
                    'saved'   => true,
                ), 500);
            }
        }

        wp_send_json_success(array('message' => 'Enviado correctamente'));
    } else {
        wp_send_json_error(array('message' => 'Error al guardar'), 500);
    }
}
add_action('wp_ajax_mm_submit_contact_form', 'mm_ajax_submit_contact');
add_action('wp_ajax_nopriv_mm_submit_contact_form', 'mm_ajax_submit_contact');

/**
 * WordPress privacy tools: leads can be exported or erased after the normal
 * administrator-confirmed personal data request workflow.
 */
function mm_lead_privacy_query($email_address, $page, $after_id = 0) {
    $after_id = absint($after_id);
    $where_filter = null;

    if ($after_id > 0) {
        $where_filter = function($where, $query) use ($after_id) {
            if (absint($query->get('mm_lead_after_id')) !== $after_id) return $where;

            global $wpdb;
            return $where . $wpdb->prepare(" AND {$wpdb->posts}.ID > %d", $after_id);
        };
        add_filter('posts_where', $where_filter, 10, 2);
    }

    $query = new WP_Query(array(
        'post_type'      => 'mm_lead',
        'post_status'    => array('publish', 'future', 'draft', 'pending', 'private', 'trash'),
        'posts_per_page' => 50,
        'paged'          => $after_id > 0 ? 1 : max(1, absint($page)),
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'mm_lead_after_id' => $after_id,
        'meta_query'     => array(
            array(
                'key'     => 'lead_email',
                'value'   => sanitize_email($email_address),
                'compare' => '=',
            ),
        ),
    ));

    if ($where_filter) remove_filter('posts_where', $where_filter, 10);
    return $query;
}

function mm_export_lead_personal_data($email_address, $page = 1) {
    $query = mm_lead_privacy_query($email_address, $page);
    $data = array();

    foreach ($query->posts as $lead) {
        $data[] = array(
            'group_id'    => 'toyota-monagas-leads',
            'group_label' => 'Solicitudes a Motores Morichal',
            'item_id'     => 'mm-lead-' . $lead->ID,
            'data'        => array(
                array('name' => 'Nombre', 'value' => get_post_meta($lead->ID, 'lead_name', true)),
                array('name' => 'Correo', 'value' => get_post_meta($lead->ID, 'lead_email', true)),
                array('name' => 'Teléfono', 'value' => get_post_meta($lead->ID, 'lead_phone', true)),
                array('name' => 'Vehículo o servicio', 'value' => get_post_meta($lead->ID, 'lead_model', true)),
                array('name' => 'Mensaje', 'value' => $lead->post_content),
                array('name' => 'Canal', 'value' => get_post_meta($lead->ID, 'lead_type', true)),
                array('name' => 'Consentimiento (UTC)', 'value' => get_post_meta($lead->ID, 'lead_privacy_consent_at', true)),
                array('name' => 'Fecha', 'value' => $lead->post_date_gmt),
            ),
        );
    }

    return array(
        'data' => $data,
        'done' => max(1, absint($page)) >= (int) $query->max_num_pages,
    );
}

function mm_erase_lead_personal_data($email_address, $page = 1) {
    $email_address = sanitize_email($email_address);
    $page = max(1, absint($page));
    $cursor_key = 'mm_erase_cursor_' . substr(hash_hmac('sha256', strtolower($email_address), wp_salt('nonce')), 0, 32);
    if ($page === 1) delete_transient($cursor_key);

    $after_id = $page > 1 ? absint(get_transient($cursor_key)) : 0;
    $query = mm_lead_privacy_query($email_address, 1, $after_id);
    $removed = false;
    $retained = false;
    $messages = array();
    $last_id = $after_id;

    foreach ($query->posts as $lead) {
        $last_id = max($last_id, absint($lead->ID));
        if (wp_delete_post($lead->ID, true)) {
            $removed = true;
        } else {
            $retained = true;
            $messages[] = 'No se pudo eliminar la solicitud ' . absint($lead->ID) . '.';
        }
    }

    $done = count($query->posts) < 50;
    if ($done) {
        delete_transient($cursor_key);
    } else {
        set_transient($cursor_key, $last_id, HOUR_IN_SECONDS);
    }

    return array(
        'items_removed'  => $removed,
        'items_retained' => $retained,
        'messages'       => $messages,
        'done'           => $done,
    );
}

add_filter('wp_privacy_personal_data_exporters', function($exporters) {
    $exporters['toyota-monagas-leads'] = array(
        'exporter_friendly_name' => 'Solicitudes de Motores Morichal',
        'callback'               => 'mm_export_lead_personal_data',
    );
    return $exporters;
});

add_filter('wp_privacy_personal_data_erasers', function($erasers) {
    $erasers['toyota-monagas-leads'] = array(
        'eraser_friendly_name' => 'Solicitudes de Motores Morichal',
        'callback'             => 'mm_erase_lead_personal_data',
    );
    return $erasers;
});

function mm_add_privacy_policy_content() {
    if (!function_exists('wp_add_privacy_policy_content')) return;

    $content = '<p>Los formularios de contacto y disponibilidad conectados al sistema de solicitudes recogen nombre, correo, teléfono, vehículo o servicio de interés y mensaje. Cada envío se guarda como una solicitud privada en WordPress para responder al interesado y se conserva hasta que un administrador la elimine o se complete una solicitud verificada de borrado.</p>';
    $content .= '<p>El formulario Buzón de sugerencias recoge nombre, correo, área, mensaje y, de forma opcional, dirección y teléfono. Este formulario no crea una solicitud almacenada en WordPress: envía el contenido por correo a la dirección de notificaciones del sitio. El proveedor SMTP y el buzón receptor pueden conservar copias conforme a su configuración y deben incluirse en cualquier procedimiento de acceso o borrado.</p>';
    $content .= '<p>Cuando un formulario abre WhatsApp, el navegador transfiere a wa.me los datos incluidos por la persona usuaria y ese tratamiento queda sujeto también a las condiciones de WhatsApp. Las notificaciones de las solicitudes almacenadas también pueden procesarse mediante el proveedor SMTP configurado por el sitio.</p>';
    $content .= '<p>Las herramientas de datos personales de WordPress permiten exportar o borrar únicamente las solicitudes almacenadas en WordPress, usando el correo facilitado. Las copias enviadas por correo deben gestionarse además en el proveedor SMTP y en el buzón receptor.</p>';
    wp_add_privacy_policy_content('Toyota Monagas', wp_kses_post(wpautop($content)));
}
add_action('admin_init', 'mm_add_privacy_policy_content');

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
