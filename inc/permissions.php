<?php
/**
 * Fix and restore Admin permissions
 */
function toyota_restore_admin_caps() {
    $role = get_role('administrator');
    if (!$role) return;

    $caps = array(
        'switch_themes',
        'edit_themes',
        'activate_plugins',
        'edit_plugins',
        'edit_users',
        'edit_files',
        'manage_options',
        'moderate_comments',
        'manage_categories',
        'manage_links',
        'upload_files',
        'import',
        'unfiltered_html',
        'edit_posts',
        'edit_others_posts',
        'edit_published_posts',
        'publish_posts',
        'edit_pages',
        'read',
        'edit_others_pages',
        'edit_published_pages',
        'publish_pages',
        'delete_pages',
        'delete_others_pages',
        'delete_published_pages',
        'delete_posts',
        'delete_others_posts',
        'delete_published_posts',
        'delete_private_posts',
        'edit_private_posts',
        'read_private_posts',
        'delete_private_pages',
        'edit_private_pages',
        'read_private_pages',
        'delete_users',
        'create_users',
        'unfiltered_upload',
        'edit_dashboard',
        'update_plugins',
        'delete_plugins',
        'install_plugins',
        'update_themes',
        'install_themes',
        'update_core',
        'list_users',
        'remove_users',
        'promote_users',
        'edit_theme_options',
        'delete_themes',
        'export'
    );

    foreach ($caps as $cap) {
        if (!$role->has_cap($cap)) {
            $role->add_cap($cap);
        }
    }
}
add_action('admin_init', 'toyota_restore_admin_caps');
