<?php
/*function vc-apps_add_rewrite_rules() {
    add_rewrite_tag('%category%', '([^/]+)', 'category_name=');
    add_permastruct('vc-apps', 'vc-apps/%category%/%vc-apps%', false);
}*/

// Surcharger les templates single/archive pour 'vc-apps' depuis le plugin
/*function vc-apps_template_override($template) {
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
add_filter('template_include', 'vc-apps_template_override');*/

/*add_filter('post_type_link', 'vc-apps_category_permalink', 10, 2);
function vc-apps_category_permalink($post_link, $post) {
    if ($post->post_type !== 'vc-apps') {
        return $post_link;
    }

    $terms = get_the_terms($post->ID, 'category');
    if (!empty($terms) && !is_wp_error($terms)) {
        return str_replace('%category%', $terms[0]->slug, $post_link);
    }

    return str_replace('%category%', 'non-classe', $post_link); // fallback
}*/