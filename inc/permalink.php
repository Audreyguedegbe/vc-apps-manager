<?php
function vc_apps_add_rewrite_rules() {
    // Rien pour l'instant
}

// Surcharger les templates single/archive pour 'vc-apps' depuis le plugin
function vc_apps_template_override($template) {
    if (is_singular('vc-apps')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-vc-apps.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    if (is_post_type_archive('vc-apps')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-vc-apps.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    return $template;
}
add_filter('template_include', 'vc_apps_template_override');