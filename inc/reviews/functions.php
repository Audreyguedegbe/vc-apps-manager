<?php

// Charger les actions AJAX liées aux reviews
require_once plugin_dir_path(__FILE__) . 'actions.php';

// Hook pour charger le JS uniquement sur ta page personnalisée
//add_action('admin_enqueue_scripts', 'vc_enqueue_review_assets');

add_action('wp_enqueue_scripts', 'vc_enqueue_reviews_front');

function vc_enqueue_reviews_front() {
    if (!is_singular('vc-apps')) return; // charger uniquement sur le CPT single

    wp_enqueue_script(
        'vc-reviews-front',
        plugin_dir_url(__FILE__) . 'reviews.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('vc-reviews-front', 'vcAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'post_id'  => get_the_ID(),
    ]);
}

