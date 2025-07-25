<?php
add_action('admin_post_vc_apps_bulk_delete', 'vc_apps_handle_bulk_delete');

function vc_apps_handle_bulk_delete() {
    if (!current_user_can('delete_posts')) {
        wp_die('Accès refusé');
    }

    if (!empty($_POST['selected_apps']) && is_array($_POST['selected_apps'])) {
        foreach ($_POST['selected_apps'] as $post_id) {
            wp_delete_post((int)$post_id, true);
        }
    }

    wp_redirect(admin_url('admin.php?page=vc_apps_admin&vc_status=deleted'));
    exit;
}