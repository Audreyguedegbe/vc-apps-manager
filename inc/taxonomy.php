<?php
function vc_apps_permalink_filter($post_link, $post) {
    if ($post->post_type !== 'vc-apps') return $post_link;

    $terms = get_the_terms($post->ID, 'vc_category');

    if (is_array($terms) && !empty($terms)) {
        $term_slug = $terms[0]->slug;
    } else {
        $term_slug = 'uncategorized';
    }

    return str_replace('%vc_category%', $term_slug, $post_link);
}
add_filter('post_type_link', 'vc_apps_permalink_filter', 10, 2);