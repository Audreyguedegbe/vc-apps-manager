<?php
/**
 * Plugin Name: VC Apps Manager
 * Description: Plugin pour gérer des apps personnalisées avec CPT, taxonomie, permaliens.
 * Version: 1.0
 * Author: Vertim Coders
 */

if (!defined('ABSPATH')) exit;

// Inclure les fichiers essentiels
// On utilise la taxonomie native 'category' de WordPress, pas de taxonomie personnalisée
require_once plugin_dir_path(__FILE__) . 'inc/taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'inc/vc-apps.php';
//require_once plugin_dir_path(__FILE__) . 'inc/permalink.php';
require_once plugin_dir_path(__FILE__) . 'inc/save.php';

require_once plugin_dir_path(__FILE__) . 'inc/admin/admin-list.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin/admin-add-edit.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin/pricing-helpers.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin/ajax-category.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin/bulk-delete.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin/assets-loader.php';
require_once plugin_dir_path(__FILE__) . 'inc/reviews/functions.php';

//require_once plugin_dir_path(__FILE__) . 'inc/reviews/functions.php';
//require_once plugin_dir_path(__FILE__) . 'inc/reviews/actions.php';

// Déclaration du shortcode
//add_shortcode('vc_app_reviews', 'vc_render_app_reviews');

function vc_apps_plugin_activate() {
    vc_register_cpt_apps();
    //vc_register_taxonomy_app_categories();
    vc_apps_add_rewrite_rules();
    //vc_apps_register_default_categories();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'vc_apps_plugin_activate');

function vc_apps_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'vc_apps_plugin_deactivate');


//pour mon template
add_filter('template_include', 'vc_apps_custom_archive_template');
function vc_apps_custom_archive_template($template) {
    if (is_post_type_archive('vc-apps')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/archive-vc-apps.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}

add_filter('template_include', 'vc_apps_custom_single_template');
function vc_apps_custom_single_template($template) {
    if (is_singular('vc-apps')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-vc-apps.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}

add_action('wp_enqueue_scripts', function () {
    // Charger jQuery sur le front-end si nécessaire
    wp_enqueue_script('jquery');
});

// ✅ Enqueue CSS/JS pour la page archive et single
add_action('wp_enqueue_scripts', 'vc_apps_enqueue_assets');
function vc_apps_enqueue_assets() {
    if (is_singular('vc-apps') || is_post_type_archive('vc-apps')) {
        // Google Fonts : Montserrat + Poppins
        wp_enqueue_style(
            'vc-google-fonts',
            'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap',
            [],
            null
        );

        // CSS principal (dépend de la police)
        wp_enqueue_style(
            'vc-apps-style',
            plugin_dir_url(__FILE__) . 'inc/assets/css/vc-apps-style.css',
            '1.0'
        );

        // JS principal
        wp_enqueue_script(
            'vc-apps-script',
            plugin_dir_url(__FILE__) . 'inc/assets/js/vc-apps-script.js',
            ['jquery'],
            '1.0',
            true
        );
    }
}

