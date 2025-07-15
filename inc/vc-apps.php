<?php
function vc_register_cpt_apps() {
    $labels = [
        'name' => 'Apps',
        'singular_name' => 'App',
        'menu_name' => 'VC Apps',
        'add_new_item' => 'Ajouter une nouvelle app',
        'all_items' => 'Toutes les apps',
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'vc-apps', 'with_front' => false],
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-smartphone',
        'taxonomies' => ['category'],
    ];

    register_post_type('vc-apps', $args);
}
add_action('init', 'vc_register_cpt_apps');
