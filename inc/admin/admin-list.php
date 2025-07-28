<?php
function vc_apps_render_admin_page() {
    if (isset($_GET['vc_status'])) {
        $messages = [
            'success' => 'App enregistrée avec succès.',
            'updated' => 'App mise à jour avec succès.',
            'error' => 'Une erreur est survenue lors de l’enregistrement.',
            'deleted' => 'App supprimée avec succès.'
        ];
        $key = $_GET['vc_status'];
        if (isset($messages[$key])) {
            $class = ($key === 'error') ? 'notice-error' : 'notice-success';
            echo '<div class="notice ' . esc_attr($class) . '"><p>' . esc_html($messages[$key]) . '</p></div>';
        }
    }

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Liste des Apps</h1>';

    echo '<div class="container-btn-add" style="margin-top: 20px; margin-bottom: 20px;">';
    $add_url = admin_url('admin.php?page=vc_apps_add_app');
    echo '<a href="' . esc_url($add_url) . '" class="page-title-action">+ Ajouter une App</a>';
    echo '</div>';

    $query = new WP_Query([
        'post_type' => 'vc-apps',
        'posts_per_page' => -1,
    ]);

    if ($query->have_posts()) {
        echo '<form method="post" action="' . admin_url('admin-post.php') . '" onsubmit="return confirm(\'\u00cates-vous sûr de vouloir supprimer les apps sélectionnées ?\')">';
        echo '<input type="hidden" name="action" value="vc_apps_bulk_delete">';
        echo '<input type="submit" class="button button-danger" value="Supprimer la sélection" style="margin-bottom: 10px;">';
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th><input type="checkbox" id="select-all-apps"></th><th>Logo</th><th>Titre</th><th>Catégorie</th><th>Features</th><th>Faqs</th><th>Reviews</th><th>Date</th><th>Actions</th></tr></thead><tbody>';

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $categories = get_the_terms($post_id, 'vc_category');
            $logo = get_post_meta($post_id, 'vc_app_logo', true);
            $features = get_post_meta($post_id, 'vc_features', true);
            $faqs = get_post_meta($post_id, 'vc_faqs', true);
            $reviews = get_post_meta($post_id, 'vc_reviews', true);
            $delete_icon_url = plugin_dir_url(__FILE__) . '../assets/images/delete.png';
            $edit_icon_url = plugin_dir_url(__FILE__) . '../assets/images/edit.png';

            echo '<tr>';
            echo '<td><input type="checkbox" class="app-checkbox" name="selected_apps[]" value="' . esc_attr($post_id) . '"></td>';
            echo '<td>' . ($logo ? '<img src="' . esc_url($logo) . '" style="width:50px;height:auto;">' : '—') . '</td>';
            echo '<td><a href="' . get_edit_post_link($post_id) . '">' . get_the_title() . '</a></td>';
            echo '<td>' . (!empty($categories) ? esc_html($categories[0]->name) : '—') . '</td>';
            echo '<td>' . (is_array($features) ? count($features) : 0) . '</td>';
            echo '<td>' . (is_array($faqs) ? count($faqs) : 0) . '</td>';
            echo '<td>' . (is_array($reviews) ? count($reviews) : 0) . '</td>';
            echo '<td>' . get_the_date() . '</td>';
            $edit_url = admin_url('admin.php?page=vc_apps_add_app&post_id=' . $post_id);
            $delete_url = wp_nonce_url(admin_url('admin-post.php?action=vc_apps_delete_app&post_id=' . $post_id), 'vc_apps_delete_' . $post_id);
            echo '<td>
                <a href="' . esc_url($edit_url) . '" class="button"><img src="' . esc_url($edit_icon_url) . '" alt="Modifier" style="width:16px;height:16px;vertical-align:middle;"></a>
                <a href="' . esc_url($delete_url) . '" class="button button-danger" onclick="return confirm(\'\u00cates-vous sûr de vouloir supprimer cette app ?\')">
                    <img src="' . esc_url($delete_icon_url) . '" alt="Supprimer" style="width:16px;height:16px;vertical-align:middle;">
                </a>
            </td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '</form>';
    } else {
        echo '<p>Aucune app trouvée.</p>';
    }

    wp_reset_postdata();
    echo '</div>';
    echo '<script>
    document.getElementById("select-all-apps").addEventListener("click", function () {
        const checkboxes = document.querySelectorAll(".app-checkbox");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
    </script>';
}