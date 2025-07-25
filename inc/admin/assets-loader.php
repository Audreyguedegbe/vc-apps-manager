<?php
add_action('admin_enqueue_scripts', function($hook) {
    if (strpos($hook, 'vc_apps') !== false) {
        // Charger jQuery si ce n'est pas déjà fait
        wp_enqueue_script('jquery');

        wp_enqueue_media();

        wp_enqueue_script(
            'vc-admin-js',
            plugin_dir_url(__FILE__) . 'assets/js/admin.js',
            ['jquery'],
            null,
            true
        );

        wp_enqueue_style(
            'vc-admin-css',
            plugin_dir_url(__FILE__) . 'assets/css/admin.css',
            [],
            null
        );
    }
});
