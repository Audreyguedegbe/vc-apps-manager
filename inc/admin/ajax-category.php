<?php
add_action('wp_ajax_vc_add_custom_category', 'vc_handle_add_custom_category');

function vc_handle_add_custom_category() {
    // Sécurité basique
    if (!current_user_can('manage_categories')) {
        wp_send_json_error(['message' => 'Permission refusée.']);
    }

    $name = sanitize_text_field($_POST['name'] ?? '');

    if (empty($name)) {
        wp_send_json_error(['message' => 'Le nom de la catégorie est vide.']);
    }

    // Vérification insensible à la casse
    $existing_cats = get_terms([
        'taxonomy' => 'category',
        'hide_empty' => false,
    ]);

    foreach ($existing_cats as $cat) {
        if (strcasecmp($cat->name, $name) === 0) {
            wp_send_json_error(['message' => 'Cette catégorie existe déjà.']);
        }
    }

    // Insertion
    $new_cat = wp_insert_term($name, 'category');

    if (is_wp_error($new_cat)) {
        wp_send_json_error(['message' => 'Erreur lors de l\'ajout : ' . $new_cat->get_error_message()]);
    }

    wp_send_json_success([
        'term_id' => $new_cat['term_id'],
        'name'    => $name,
    ]);
}