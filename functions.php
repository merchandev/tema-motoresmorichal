<?php
/**
 * Theme functions
 *
 * This file is now modularized. Please check the 'inc' directory for specific functionality.
 */

// Suppress server connection warnings on frontend
@ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

define('TOYOTA_MONAGAS_VERSION', '1.0.0');

// Fix Permissions for Administrator (Force)
// This ensures that the administrator always has full capabilities
require_once get_template_directory() . '/inc/permissions.php';

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

// Font Awesome Auto-Download System (CDN to Local Cache)
// DESACTIVADO TEMPORALMENTE - Causaba errores PHP visibles en frontend
// require_once get_template_directory() . '/inc/fontawesome-auto-download.php';

// Customizer Options
require_once get_template_directory() . '/inc/customizer.php';

// Remove BOM and invisible characters from DOM
add_action('wp_footer', function() {
    ?>
    <script>
    (function() {
        // Remove BOM (Zero Width No-Break Space) and empty text nodes
        document.addEventListener('DOMContentLoaded', function() {
            var body = document.body;
            if (!body) return;
            
            // Check first child nodes for BOM or empty text
            var nodesToRemove = [];
            for (var i = 0; i < body.childNodes.length; i++) {
                var node = body.childNodes[i];
                // Check if it's a text node
                if (node.nodeType === 3) {
                    var text = node.textContent || '';
                    // Check for BOM (U+FEFF) or just whitespace
                    if (text.charCodeAt(0) === 0xFEFF || text.trim() === '') {
                        nodesToRemove.push(node);
                    }
                }
            }
            // Remove identified nodes
            nodesToRemove.forEach(function(node) {
                try {
                    body.removeChild(node);
                } catch(e) {}
            });
        });
    })();
    </script>
    <?php
}, 999);

