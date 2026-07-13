<?php
// Cleanup script to remove specific vehicles requested by user
add_action('init', function(){
    // Run only once
    if (get_option('toyota_cleanup_2025_v1_done')) return;

    $removals = array(
        'toyota-hiace-2025',
        'toyota-corolla-cross-2025',
        'toyota-fortuner-sw4-2025',
        'toyota-hilux-2025',
        'toyota-yaris-2025'
    );

    foreach($removals as $slug){
        $p = get_page_by_path($slug, OBJECT, 'vehiculo');
        if ($p){
            wp_delete_post($p->ID, true); // Force delete, skip trash
        }
    }

    update_option('toyota_cleanup_2025_v1_done', true);
});
