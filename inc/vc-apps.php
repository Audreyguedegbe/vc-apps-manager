<?php
function vc_register_vc_apps_cpt() {
    $labels = array(
        'name'                  => 'Apps',
        'singular_name'         => 'App',
        'add_new'               => 'Ajouter une App',
        'add_new_item'          => 'Ajouter une nouvelle App',
        'edit_item'             => 'Modifier l\'App',
        'view_item'             => 'Voir l\'App',
        'all_items'             => 'Toutes les Apps',
        'search_items'          => 'Rechercher une App',
        'not_found'             => 'Aucune App trouvée',
        'not_found_in_trash'    => 'Aucune App trouvée dans la corbeille',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true, // important pour frontend
        'has_archive'        => 'apps',
        'rewrite'            => array(
            'slug'       => 'apps/%vc_category%',
            'with_front' => false
        ),
        'publicly_queryable' => true,
        'show_ui'            => false, // ⚠️ masqué dans l’admin WP
        'show_in_menu'       => false, // ne pas apparaître dans le menu WP
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest'       => false, // sauf si tu veux l’exposer à l’API
    );

    register_post_type('vc-apps', $args);
}
add_action('init', 'vc_register_vc_apps_cpt');


//categorie
function vc_register_app_taxonomy() {
    register_taxonomy(
        'vc_category',
        'vc-apps',
        array(
            'label'        => 'Catégories',
            'hierarchical' => true,
            'public'       => true,
            'rewrite'      => array('slug' => 'apps', 'with_front' => false),
        )
    );
}
add_action('init', 'vc_register_app_taxonomy');

//vc-apps_register_admin_page

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








