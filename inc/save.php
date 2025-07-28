<?php
add_action('admin_post_vc_apps_save_custom_app', 'vc_apps_save_custom_app');

function vc_apps_save_custom_app() {
    if (!current_user_can('edit_posts')) {
        wp_die('Permission refusée.');
    }

    if (!isset($_POST['vc_apps_nonce']) || !wp_verify_nonce($_POST['vc_apps_nonce'], 'vc_apps_nonce_action')) {
        wp_die('Vérification de sécurité échouée.');
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $is_edit = $post_id > 0;

    $post_data = [
        'post_title'   => sanitize_text_field($_POST['vc_app_title']),
        'post_type'    => 'vc-apps',
        'post_status'  => 'publish',
    ];

    // Insertion ou mise à jour
    if ($is_edit) {
        $post_data['ID'] = $post_id;
        $post_id = wp_update_post($post_data);
    } else {
        $post_id = wp_insert_post($post_data);
    }

    if (is_wp_error($post_id)) {
        wp_redirect(admin_url('admin.php?page=vc_apps_admin&vc_status=error'));
        exit;
    }

    // Catégorie
    if (!empty($_POST['vc_app_category'])) {
        wp_set_object_terms($post_id, [(int) $_POST['vc_app_category']], 'vc_category');
    }



    // Logo & bannière
    update_post_meta($post_id, 'vc_app_logo', esc_url_raw($_POST['vc_app_logo']));
    update_post_meta($post_id, 'vc_app_banner', esc_url_raw($_POST['vc_app_banner']));

    // Descriptions
    update_post_meta($post_id, 'vc_app_short_desc', wp_kses_post($_POST['vc_app_short_desc']));
    update_post_meta($post_id, 'vc_app_long_desc', wp_kses_post($_POST['vc_app_long_desc']));


    // Features
    if (!empty($_POST['vc_features']) && is_array($_POST['vc_features'])) {
        $features = array_map(function ($f) {
            return [
                'image'  => esc_url_raw($f['image']),
                'title'  => sanitize_text_field($f['title']),
                'desc'   => wp_kses_post($f['desc']),
                'button' => esc_url_raw($f['button']),
                'style' => sanitize_text_field($f['style'] ?? ''),
            ];
        }, $_POST['vc_features']);
        update_post_meta($post_id, 'vc_features', $features);
    } else {
        delete_post_meta($post_id, 'vc_features');
    }

    // FAQs
    if (!empty($_POST['vc_faqs']) && is_array($_POST['vc_faqs'])) {
        $faqs = array_map(function ($faq) {
            return [
                'title'  => sanitize_text_field($faq['title']),
                'desc'   => wp_kses_post($faq['desc']),
                'button' => esc_url_raw($faq['button']),
            ];
        }, $_POST['vc_faqs']);
        update_post_meta($post_id, 'vc_faqs', $faqs);
    } else {
        delete_post_meta($post_id, 'vc_faqs');
    }

    //Reviews
    if (!empty($_POST['vc_reviews']) && is_array($_POST['vc_reviews'])) {
    $reviews = array_map(function ($r) {
        return [
            'author'  => sanitize_text_field($r['author']),
            'rating'  => intval($r['rating']),
            'comment' => sanitize_textarea_field($r['comment']),
        ];
    }, $_POST['vc_reviews']);
    update_post_meta($post_id, 'vc_reviews', $reviews);
    } else {
        delete_post_meta($post_id, 'vc_reviews');
    }



    // === PRICING ===
    $pricing_data = [];
$has_sub      = sanitize_text_field($_POST['vc_has_subscription'] ?? 'no');
$has_multi    = sanitize_text_field($_POST['vc_has_multiplan'] ?? 'no');
$input        = $_POST['vc_pricing'] ?? [];

if ($has_sub === 'no' && $has_multi === 'no') {
    // ✅ Cas 2 : Prix simple unique
    $pricing_data['type'] = 'simple';
    $pricing_data['plans']['general'] = [
        'desc'  => sanitize_textarea_field($input['general']['desc'] ?? ''),
        'price' => sanitize_text_field($input['general']['price'] ?? ''),
        'url'   => esc_url_raw($input['general']['url'] ?? ''),
    ];
}
elseif ($has_sub === 'no' && $has_multi === 'yes') {
    // ✅ Cas 1 : Multi-plan sans souscription
    $pricing_data['type'] = 'multi';
    $pricing_data['plans'] = [];

    foreach ($input as $key => $plan) {
        if (preg_match('/^plan_\d+$/', $key) && is_array($plan)) {
            $pricing_data['plans'][$key] = [
                'title' => sanitize_text_field($plan['title'] ?? ''),
                'desc'  => sanitize_textarea_field($plan['desc'] ?? ''),
                'price' => sanitize_text_field($plan['price'] ?? ''),
                'url'   => esc_url_raw($plan['url'] ?? ''),
            ];
        }
    }
}




elseif ($has_sub === 'yes' && $has_multi === 'no') {
    // ✅ Cas 3 : Simple avec souscription (clé simple_monthly + simple_yearly)
    $pricing_data['type'] = 'simple_subscription';
    $pricing_data['plans']['simple_monthly'] = [
        'desc'  => sanitize_textarea_field($input['simple_monthly']['desc'] ?? ''),
        'price' => sanitize_text_field($input['simple_monthly']['price'] ?? ''),
        'url'   => esc_url_raw($input['simple_monthly']['url'] ?? ''),
    ];
    $pricing_data['plans']['simple_yearly'] = [
        'desc'  => sanitize_textarea_field($input['simple_yearly']['desc'] ?? ''),
        'price' => sanitize_text_field($input['simple_yearly']['price'] ?? ''),
        'url'   => esc_url_raw($input['simple_yearly']['url'] ?? ''),
    ];
}
elseif ($has_sub === 'yes' && $has_multi === 'yes') {
    // ✅ Cas 4 : Multi-plan avec souscription (monthly et yearly avec index multiple)
    $pricing_data['type'] = 'multi_subscription';

    foreach (['monthly', 'yearly'] as $group) {
        if (!empty($input[$group]) && is_array($input[$group])) {
            foreach ($input[$group] as $i => $plan) {
                $pricing_data['plans'][$group][$i] = [
                    'title' => sanitize_text_field($plan['title'] ?? ''),
                    'desc'  => sanitize_textarea_field($plan['desc'] ?? ''),
                    'price' => sanitize_text_field($plan['price'] ?? ''),
                    'url'   => esc_url_raw($plan['url'] ?? ''),
                ];
            }
        }
    }
}


update_post_meta($post_id, 'vc_pricing', $pricing_data);
update_post_meta($post_id, 'vc_has_subscription', $has_sub);
update_post_meta($post_id, 'vc_has_multiplan', $has_multi);

    // MULTI-PLATFORM
    update_post_meta($post_id, 'vc_multi_platform', sanitize_text_field($_POST['vc_multi_platform'] ?? 'no'));
    update_post_meta($post_id, 'vc_platform_url', esc_url_raw($_POST['vc_platform_url'] ?? ''));

    // AUTRES URLS
    update_post_meta($post_id, 'vc_url_free', esc_url_raw($_POST['vc_url_free'] ?? ''));
    update_post_meta($post_id, 'vc_url_doc', esc_url_raw($_POST['vc_url_doc'] ?? ''));
    update_post_meta($post_id, 'vc_url_live', esc_url_raw($_POST['vc_url_live'] ?? ''));
    update_post_meta($post_id, 'vc_desc_live', sanitize_textarea_field($_POST['vc_desc_live'] ?? ''));
    update_post_meta($post_id, 'vc_url_admin', esc_url_raw($_POST['vc_url_admin'] ?? ''));
    update_post_meta($post_id, 'vc_url_feature', esc_url_raw($_POST['vc_url_feature'] ?? ''));
    update_post_meta($post_id, 'vc_url_changelog', esc_url_raw($_POST['vc_url_changelog'] ?? ''));

    // Redirection
    $status = $is_edit ? 'updated' : 'success';
    wp_redirect(admin_url("admin.php?page=vc_apps_admin&vc_status={$status}"));
    exit;
}

add_action('admin_post_vc_apps_delete_app', 'vc_apps_delete_app');

function vc_apps_delete_app() {
    if (!current_user_can('delete_posts')) {
        wp_die('Permission refusée');
    }

    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

    if (!$post_id || !wp_verify_nonce($_GET['_wpnonce'], 'vc_apps_delete_' . $post_id)) {
        wp_die('Nonce invalide');
    }

    wp_delete_post($post_id, true); // true pour suppression définitive

    wp_redirect(admin_url('admin.php?page=vc_apps_admin&vc_status=deleted'));
    exit;
}