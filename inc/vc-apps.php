<?php
add_filter('post_type_link', 'vc_apps_category_permalink', 10, 2);
function vc_apps_category_permalink($post_link, $post) {
    if ($post->post_type !== 'vc-apps') {
        return $post_link;
    }

    $terms = get_the_terms($post->ID, 'category');
    if (!empty($terms) && !is_wp_error($terms)) {
        return str_replace('%category%', $terms[0]->slug, $post_link);
    }

    return str_replace('%category%', 'non-classe', $post_link); // fallback
}

add_action('admin_menu', 'vc_apps_register_admin_page');

function vc_apps_register_admin_page() {
    // Menu principal Gérer les Apps
    add_menu_page(
        'VC Apps',
        'VC Apps',
        'manage_options',
        'vc_apps_admin',
        'vc_apps_render_admin_page',  // Affiche la liste des Apps personnalisée
        'dashicons-smartphone',
        6
    );

    // Sous-menu : Ajouter une app, page personnalisée aussi
    add_submenu_page(
        'vc_apps_admin',
        'Ajouter une app',
        'Ajouter une app',
        'manage_options',
        'vc_apps_add_app',             // nouveau slug unique
        'vc_apps_render_add_app_page'  // nouvelle fonction pour afficher le formulaire personnalisé
    );
}
add_action('admin_menu', 'vc_apps_register_admin_page');









