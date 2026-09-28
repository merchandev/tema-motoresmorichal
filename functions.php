<?php
/**
 * Theme functions
 *
 * This file is now modularized. Please check the 'inc' directory for specific functionality.
 */

define('TOYOTA_MONAGAS_VERSION', '1.3.0');

// Setup and Enqueues
require_once get_template_directory() . '/inc/setup.php';

// Custom Post Types and Taxonomies
require_once get_template_directory() . '/inc/cpt.php';

// Metaboxes
require_once get_template_directory() . '/inc/metaboxes.php';

// AJAX Handlers
require_once get_template_directory() . '/inc/ajax.php';

// Shortcodes
require_once get_template_directory() . '/inc/shortcodes.php';

// Admin Tools
require_once get_template_directory() . '/inc/cleanup.php';
require_once get_template_directory() . '/inc/admin-tools.php';

// Contact System (Leads & SMTP)
require_once get_template_directory() . '/inc/contact-system.php';

// Customizer Options
require_once get_template_directory() . '/inc/customizer.php';
