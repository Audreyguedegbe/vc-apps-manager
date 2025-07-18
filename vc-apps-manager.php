<?php
/**
 * Plugin Name: VC Apps Manager
 * Description: Plugin pour gérer des apps personnalisées avec CPT, taxonomie, permaliens.
 * Version: 1.0
 * Author: Vertim Coders
 */

if (!defined('ABSPATH')) exit;

// Inclure les fichiers essentiels
require_once plugin_dir_path(__FILE__) . 'inc/vc-apps.php';
// On utilise la taxonomie native 'category' de WordPress, pas de taxonomie personnalisée
//require_once plugin_dir_path(__FILE__) . 'inc/taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'inc/permalink.php';
require_once plugin_dir_path(__FILE__) . 'inc/acf-fields.php';
require_once plugin_dir_path(__FILE__) . 'inc/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'inc/metabox-app-details.php';
require_once plugin_dir_path(__FILE__) . 'inc/metabox-app-faq.php';
require_once plugin_dir_path(__FILE__) . 'inc/metabox-app-features.php';
require_once plugin_dir_path(__FILE__) . 'inc/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'inc/save.php';

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

// ✅ Enqueue CSS/JS pour la page admin
add_action('admin_enqueue_scripts', 'vc_apps_admin_enqueue_assets');
function vc_apps_admin_enqueue_assets($hook) {
    if ($hook === 'toplevel_page_vc_apps_admin') {
        wp_enqueue_style('vc-admin-style', plugin_dir_url(__FILE__) . 'inc/assets/css/admin.css');
        wp_enqueue_script('vc-admin-script', plugin_dir_url(__FILE__) . 'inc/assets/js/admin.js', ['jquery'], null, true);
        wp_enqueue_media();
    }
}
