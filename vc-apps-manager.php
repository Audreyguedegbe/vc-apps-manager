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
