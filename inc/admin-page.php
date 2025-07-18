<?php

/**
 * Normalise la meta vc_pricing et renvoie [$pricing_type, $pricing_plans].
 *
 * Rétro-compatibilité :
 * - Ancienne structure simple : ['general'=>...], ['monthly'=>...], etc.
 * - Ancienne structure multi avec souscription : ['monthly'=>[...], 'yearly'=>[...]]
 * - Nouvelle structure : ['type'=>'...', 'plans'=>[...]]
 *
 * @param int $post_id
 * @param string $has_subscription_meta meta vc_has_subscription (yes|no) si dispo
 * @param string $has_multiplan_meta    meta vc_has_multiplan   (yes|no) si dispo
 * @return array [$type, $plans]
 */
//cette function c'est pour afficher la liste des apps
function vc_apps_render_admin_page() {
        if (isset($_GET['vc_status'])) {
        if ($_GET['vc_status'] === 'success') {
            echo '<div class="notice notice-success"><p>App enregistrée avec succès.</p></div>';
        } elseif ($_GET['vc_status'] === 'updated') {
            echo '<div class="notice notice-success"><p>App mise à jour avec succès.</p></div>';
        } elseif ($_GET['vc_status'] === 'error') {
            echo '<div class="notice notice-error"><p>Une erreur est survenue lors de l’enregistrement.</p></div>';
        }elseif ($_GET['vc_status'] === 'deleted') {
            echo '<div class="notice notice-success"><p>App supprimée avec succès.</p></div>';
        }
    }



    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Liste des Apps</h1>';

    echo '<div class="container-btn-add" style="margin-top: 20px; margin-bottom: 20px;">';
    // Bouton "Ajouter une App"
    $add_url = admin_url('admin.php?page=vc_apps_add_app');
    echo ' <a href="' . esc_url($add_url) . '" class="page-title-action">+ Ajouter une App</a>';
    echo '</div>';

    $query = new WP_Query([
        'post_type' => 'vc-apps',
        'posts_per_page' => -1,
    ]);

    if ($query->have_posts()) {
        echo '<form method="post" action="' . admin_url('admin-post.php') . '" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer les apps sélectionnées ?\')">';
        echo '<input type="hidden" name="action" value="vc_apps_bulk_delete">';
        echo '<input type="submit" class="button button-danger" value="Supprimer la sélection" style="margin-bottom: 10px;">';
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th style="width:20px;padding: 2px;"><input type="checkbox" id="select-all-apps"></th><th>Logo</th><th>Titre</th><th>Catégorie</th><th>Features</th><th>Faqs</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $categories = get_the_terms($post_id, 'category');
            $logo = get_post_meta($post_id, 'vc_app_logo', true);
            $features = get_post_meta($post_id, 'vc_features', true);
            $faqs = get_post_meta($post_id, 'vc_faqs', true);
            $delete_icon_url = plugin_dir_url(__FILE__) . 'assets/images/delete.png';
            $edit_icon_url = plugin_dir_url(__FILE__) . 'assets/images/edit.png';

            echo '<tr>';
            echo '<td><input type="checkbox" class="app-checkbox" name="selected_apps[]" value="' . esc_attr($post_id) . '"></td>';
            echo '<td>' . ($logo ? '<img src="' . esc_url($logo) . '" style="width:50px;height:auto;">' : '—') . '</td>';
            echo '<td><a href="' . get_edit_post_link($post_id) . '">' . get_the_title() . '</a></td>';
            echo '<td>' . (!empty($categories) ? esc_html($categories[0]->name) : '—') . '</td>';
            echo '<td>' . (is_array($features) ? count($features) : 0) . '</td>';
            echo '<td>' . (is_array($faqs) ? count($faqs) : 0) . '</td>';
            echo '<td>' . get_the_date() . '</td>';
            // Bouton modifier
            $edit_url = admin_url('admin.php?page=vc_apps_add_app&post_id=' . $post_id);
            $delete_url = wp_nonce_url(admin_url('admin-post.php?action=vc_apps_delete_app&post_id=' . $post_id), 'vc_apps_delete_' . $post_id);
            echo '<td>
                <a href="' . esc_url($edit_url) . '" class="button"><img src="' . esc_url($edit_icon_url) . '" alt="Modifier" style="width:16px;height:16px;vertical-align:middle;"></a>
                <a href="' . esc_url($delete_url) . '" class="button button-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette app ?\')">
                    <img src="' . esc_url($delete_icon_url) . '" alt="Supprimer" style="width:16px;height:16px;vertical-align:middle;">
                </a>
            </td>';
            echo '';

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

//cette function c'est pour ajouter une app
if (isset($_GET['error']) && $_GET['error'] == 1) : 
    echo '<div class="notice notice-error is-dismissible">
        <p><strong>Erreur :</strong> L’enregistrement de l’app a échoué. Veuillez réessayer.</p>
    </div>';
 endif; 

if (isset($_GET['success']) && $_GET['success'] == 1) : 
    echo '<div class="notice notice-success is-dismissible">
        <p>App enregistrée avec succès.</p>
    </div>';
endif; 

function vc_apps_get_pricing_structured( $post_id, $has_subscription_meta = '', $has_multiplan_meta = '' ) {
	$raw = get_post_meta( $post_id, 'vc_pricing', true );

	$type  = '';
	$plans = [];

	// ✅ Cas 1 : Nouvelle structure
	if ( is_array( $raw ) && isset( $raw['type'], $raw['plans'] ) ) {
		$type  = $raw['type'];
		$plans = $raw['plans'];
	}
	// ✅ Cas 2 : Ancienne structure simple
	elseif ( is_array( $raw ) && isset( $raw['general'] ) && is_array( $raw['general'] ) ) {
		$type  = 'simple_nosub';
		$plans = [ 'general' => $raw['general'] ];
	}
	// ✅ Cas 3 : Ancienne structure simple avec souscription
	elseif ( is_array( $raw ) && isset( $raw['monthly'], $raw['yearly'] ) && !isset( $raw['monthly'][0] ) && !isset( $raw['yearly'][0] ) ) {
		$type  = 'simple_withsub';
		$plans = [
			'monthly' => $raw['monthly'],
			'yearly'  => $raw['yearly'],
		];
	}
	// ✅ Cas 4 : Ancienne structure multi avec souscription
	elseif ( is_array( $raw ) && isset( $raw['monthly'][0], $raw['yearly'][0] ) ) {
		$type  = 'multi_withsub';
		$plans = [
			'monthly' => $raw['monthly'],
			'yearly'  => $raw['yearly'],
		];
	}
	// ✅ Cas 5 : Ancienne structure multi sans souscription
	elseif ( is_array( $raw ) ) {
		$type  = 'multi_nosub';
		$plans = $raw;
	}

	// 🔄 Harmonisation du type pour affichage (si on utilise encore l’ancienne convention)
	if ( $type === 'simple' ) {
		$type = 'simple_nosub';
	} elseif ( $type === 'multi' ) {
		$type = 'multi_nosub';
	} elseif ( $type === 'simple_subscription' ) {
		$type = 'simple_withsub';
	} elseif ( $type === 'multi_subscription' ) {
		$type = 'multi_withsub';
	}

	return [ $type, $plans ];
}




function vc_apps_render_add_app_page() {
    $post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
$editing = $post_id > 0;
$post = null;

if ($editing) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'vc-apps') {
        $title = $post->post_title;
        $short_desc = get_post_meta($post_id, 'vc_app_short_desc', true);
        $long_desc = get_post_meta($post_id, 'vc_app_long_desc', true);
        $logo = get_post_meta($post_id, 'vc_app_logo', true);
        $banner = get_post_meta($post_id, 'vc_app_banner', true);
        $category = wp_get_post_terms($post_id, 'category', ['fields' => 'ids']);
        $category = !empty($category) ? $category[0] : '';
        $features = get_post_meta($post_id, 'vc_features', true);
        $faqs = get_post_meta($post_id, 'vc_faqs', true);

        $has_sub = sanitize_text_field($_POST['vc_has_subscription'] ?? 'no');
        $has_multi = sanitize_text_field($_POST['vc_has_multiplan'] ?? 'no');
        $multi_platform  = get_post_meta($post_id, 'vc_multi_platform', true) ?: 'no';

        $platform_url = get_post_meta($post_id, 'vc_platform_url', true);
        $url_free     = get_post_meta($post_id, 'vc_url_free', true);
        $url_doc      = get_post_meta($post_id, 'vc_url_doc', true);
        $url_demo     = get_post_meta($post_id, 'vc_url_live', true);
        $demo_desc    = get_post_meta($post_id, 'vc_desc_live', true);
        $url_admin    = get_post_meta($post_id, 'vc_url_admin', true);
        $url_request  = get_post_meta($post_id, 'vc_url_feature', true);
        $url_changelog= get_post_meta($post_id, 'vc_url_changelog', true);
        $pricing = get_post_meta($post_id, 'vc_pricing', true);

    }
}

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo $editing ? 'Modifier l’app' : 'Ajouter une nouvelle App'; ?></h1>
            
        
        <div style="margin-top: -35px; float: right;">
            <a href="<?php echo admin_url('admin.php?page=vc_apps_admin'); ?>" class="button button-secondary">← Retour à la liste</a>
        </div>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
            <p><input type="submit" value="Enregistrer l’app" class="button button-primary"></p>
            <input type="hidden" name="action" value="vc_apps_save_custom_app">
            <?php wp_nonce_field('vc_apps_nonce_action', 'vc_apps_nonce'); ?>

            <div class="container-register-app">
                <div class="container-menus">
                    <div class="container">
                        <div class="menu-info"> <a href="#container-details">Détail</a> </div>
                        <div class="menu-info"> <a href="#container-pricing">Pricings</a> </div>
                        <div class="menu-info"> <a href="#container-features">Features</a> </div>
                        <div class="menu-info"> <a href="#container-faqs">Faq</a> </div>
                    </div>
                </div>
                <div class="container-infos">
                    <div id="container-details">
                        <table class="form-table">
                            <tr class="form-step1">
                                <td>
                                    <div class="container-label">
                                        <label for="vc_app_title">Titre:</label>
                                    </div>
                                    <div class="container-input">
                                        <input type="text" name="vc_app_title" id="vc_app_title" class="regular-text" required value="<?php echo esc_attr($editing ? $title : ''); ?>">
                                    </div>
                                </td>
                                <td>
                                    <div class="container-label">
                                        <label for="vc_app_category">Catégorie:</label>
                                    </div>
                                    <div class="container-input">
                                        <select name="vc_app_category" id="vc_app_category" required>
                                            <option value="">Sélectionnez une catégorie</option>
                                            <?php
                                            $categories = get_categories([
                                                'taxonomy' => 'category',
                                                'hide_empty' => false,
                                            ]);
                                            foreach ($categories as $cat) {
                                                $selected = ($editing && $category == $cat->term_id) ? 'selected' : '';
                                                echo '<option value="' . esc_attr($cat->term_id) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
                                            }

                                            ?>
                                        </select>
                                        <br><br>
                                        <input type="text" id="new_category_name" placeholder="Nouvelle catégorie" style="margin-right: 10px;">
                                        <button type="button" class="button" id="add_new_category">+ Ajouter la catégorie</button>
                                        <p class="description" id="cat-msg" style="margin-top: 5px; color: green;"></p>
                                    </div>
                                </td>
                            </tr>

                            <tr class="form-step2">
                                <td>
                                    <div class="container-label">
                                        <label for="vc_app_logo">Logo:</label>
                                    </div>
                                    <div class="container-input">
                                        <input type="button" class="button vc-upload-logo" value="Uploader un logo">
                                        <input type="hidden" name="vc_app_logo" id="vc_app_logo" value="<?php echo esc_attr($logo); ?>">
                                        <div id="vc_app_logo_preview" style="margin-top: 10px;">
                                            <?php if (!empty($logo)) : ?>
                                                <img src="<?php echo esc_url($logo); ?>" style="max-height: 100px;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="container-label">
                                        <label for="vc_app_banner">Bannière:</label>
                                    </div>
                                    <div class="container-input">
                                        <input type="button" class="button vc-upload-banner" value="Uploader une bannière">
                                        <input type="hidden" name="vc_app_banner" id="vc_app_banner" value="<?php echo esc_attr($banner); ?>">
                                        <div id="vc_app_banner_preview" style="margin-top: 10px;">
                                            <?php if (!empty($banner)) : ?>
                                                <img src="<?php echo esc_url($banner); ?>" style="max-height: 100px;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="form-step3">
                                <td>
                                    <div class="container-label">
                                        <label for="vc_app_short_desc">Description courte:</label>
                                    </div>
                                    <div class="container-input">
                                        <textarea name="vc_app_short_desc" id="vc_app_short_desc" class="large-text" rows="3"><?php echo esc_textarea($editing ? $short_desc : ''); ?></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="container-label">
                                        <label for="vc_app_long_desc">Description longue:</label>
                                    </div>
                                    <div class="container-input">
                                        <?php
                                            wp_editor($editing ? $long_desc : '', 'vc_app_long_desc', [
                                                'textarea_name' => 'vc_app_long_desc',
                                                'media_buttons' => true,
                                                'textarea_rows' => 6,
                                            ]);
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- PRICING SECTION -->
                    <div id="container-pricing" class="container-pricing" style="gap: 20px; display: flex; flex-direction: column;">
                        <div style="background-color: #fff; padding: 20px; border-radius: 10px;">              
                            <h2>Pricing</h2>
                            <?php
                            $pricing = get_post_meta($post_id, 'vc_pricing', true);
                            $editing = is_array($pricing) && isset($pricing['plans']);
                            $plans = $editing ? $pricing['plans'] : [];
                            ?>

                            <?php
                            $has_sub = get_post_meta($post_id, 'vc_has_subscription', true) ?: 'no';
                            $has_multi = get_post_meta($post_id, 'vc_has_multiplan', true) ?: 'no';
                            ?>

                            <p><strong>Souscription ?</strong>
                            <select id="vc_subscription" name="vc_has_subscription">
                                <option value="no" <?php selected($has_sub, 'no'); ?>>Non</option>
                                <option value="yes" <?php selected($has_sub, 'yes'); ?>>Oui</option>
                            </select>
                            </p>

                            <p><strong>Multi plan ?</strong>
                            <select id="vc_multi_plan" name="vc_has_multiplan">
                                <option value="no" <?php selected($has_multi, 'no'); ?>>Non</option>
                                <option value="yes" <?php selected($has_multi, 'yes'); ?>>Oui</option>
                            </select>
                            </p>


                                <!-- Contenus conditionnels -->
                                <?php /* Les blocs suivants doivent être masqués par défaut en JS et affichés selon les conditions */ ?>

                                <?php // Cas 1 ?>
                                <div id="pricing_single_multi_nosub" class="pricing-section" style="display:none;">
                                    <h3>Plans disponibles</h3>
                                    <div class="pricing-plans-container" id="general-plans">
                                        <button type="button" class="button add-plan-button" data-type="general">+ Ajouter un plan</button>

                                        <?php if (!empty($pricing) && is_array($pricing)) : ?>
                                            <?php foreach (($pricing['plans'] ?? []) as $key => $plan) : ?>
                                                <?php if (!preg_match('/^plan_\d+$/', $key)) continue; ?>
                                                <div class="plan-block" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                                                    <a href="#" class="remove-plan" style="color: red; float: right;">Supprimer</a>
                                                    <p><label>Nom du plan<br>
                                                        <input type="text" name="vc_pricing[<?php echo esc_attr($key); ?>][title]" class="regular-text" value="<?php echo esc_attr($plan['title'] ?? ''); ?>">
                                                    </label></p>
                                                    <p><label>Description<br>
                                                        <textarea name="vc_pricing[<?php echo esc_attr($key); ?>][desc]" class="large-text" rows="3"><?php echo esc_textarea($plan['desc'] ?? ''); ?></textarea>
                                                    </label></p>
                                                    <p><label>Prix<br>
                                                        <input type="text" name="vc_pricing[<?php echo esc_attr($key); ?>][price]" class="regular-text" value="<?php echo esc_attr($plan['price'] ?? ''); ?>">
                                                    </label></p>
                                                    <p><label>URL<br>
                                                        <input type="url" name="vc_pricing[<?php echo esc_attr($key); ?>][url]" class="regular-text" value="<?php echo esc_url($plan['url'] ?? ''); ?>">
                                                    </label></p>
                                                </div>
                                            <?php endforeach; ?>

                                        <?php endif; ?>

                                        <template id="vc-plan-template">
                                            <div class="plan-block" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                                                <a href="#" class="remove-plan" style="float: right; color: red;">Supprimer</a>
                                                <p><label>Nom du plan<br><input type="text" name="vc_pricing[__index__][title]" class="regular-text"></label></p>
                                                <p><label>Description<br><textarea name="vc_pricing[__index__][desc]" class="large-text" rows="3"></textarea></label></p>
                                                <p><label>Prix<br><input type="text" name="vc_pricing[__index__][price]" class="regular-text"></label></p>
                                                <p><label>URL<br><input type="url" name="vc_pricing[__index__][url]" class="regular-text"></label></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>


                            <?php // Cas 2 ?>
                            <div id="pricing_simple_nosub" class="pricing-section" style="display:none;">
                                <h3>Prix unique</h3>
                                <p><label>Description<br><textarea name="vc_pricing[general][desc]" class="large-text" rows="3"><?php echo esc_textarea($editing ? ($plans['general']['desc'] ?? '') : ''); ?></textarea></label></p>
                                <p><label>Prix<br><input type="text" name="vc_pricing[general][price]" class="regular-text" value="<?php echo esc_attr($editing ? ($plans['general']['price'] ?? '') : ''); ?>"></label></p>
                                <p><label>URL<br><input type="url" name="vc_pricing[general][url]" class="regular-text" value="<?php echo esc_url($editing ? ($plans['general']['url'] ?? '') : ''); ?>"></label></p>
                            </div>

                            <?php // Cas 3 ?>
                            <div id="pricing_simple_withsub" class="pricing-section" style="display:none;">
                                <h3>Mensuel</h3>
                                <p><label>Description<br><textarea name="vc_pricing[simple_monthly][desc]" class="large-text" rows="3"><?php echo esc_textarea($editing ? ($plans['simple_monthly']['desc'] ?? '') : ''); ?></textarea></label></p>
                                <p><label>Prix<br><input type="text" name="vc_pricing[simple_monthly][price]" class="regular-text" value="<?php echo esc_attr($editing ? ($plans['simple_monthly']['price'] ?? '') : ''); ?>"></label></p>
                                <p><label>URL<br><input type="url" name="vc_pricing[simple_monthly][url]" class="regular-text" value="<?php echo esc_url($editing ? ($plans['simple_monthly']['url'] ?? '') : ''); ?>"></label></p>

                                <h3>Annuel</h3>
                                <p><label>Description<br><textarea name="vc_pricing[simple_yearly][desc]" class="large-text" rows="3"><?php echo esc_textarea($editing ? ($plans['simple_yearly']['desc'] ?? '') : ''); ?></textarea></label></p>
                            <p><label>Prix<br><input type="text" name="vc_pricing[simple_yearly][price]" class="regular-text" value="<?php echo esc_attr($editing ? ($plans['simple_yearly']['price'] ?? '') : ''); ?>"></label></p>
                            <p><label>URL<br><input type="url" name="vc_pricing[simple_yearly][url]" class="regular-text" value="<?php echo esc_url($editing ? ($plans['simple_yearly']['url'] ?? '') : ''); ?>"></label></p>
                            </div>

                            <?php // Cas 4 ?>
                            

                            <div id="pricing_multi_withsub" class="pricing-section" style="display:none;">
                                <h3>Plans mensuels</h3>
                                <div class="pricing-plans-container" id="monthly-plans">
                                    <button type="button" class="button add-plan-button" data-type="monthly">+ Ajouter un plan mensuel</button>

                                    <?php if (!empty($plans['monthly']) && is_array($plans['monthly'])) : ?>
                                        <?php foreach ($plans['monthly'] as $i => $plan) : ?>
                                            <div class="plan-block" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                                                <a href="#" class="remove-plan" style="color: red; float: right;">Supprimer</a>
                                                <p><label>Nom du plan<br>
                                                    <input type="text" name="vc_pricing[monthly][<?php echo $i; ?>][title]" class="regular-text" value="<?php echo esc_attr($plan['title'] ?? ''); ?>"></label></p>
                                                <p><label>Description<br>
                                                    <textarea name="vc_pricing[monthly][<?php echo $i; ?>][desc]" class="large-text" rows="3"><?php echo esc_textarea($plan['desc'] ?? ''); ?></textarea></label></p>
                                                <p><label>Prix<br>
                                                    <input type="text" name="vc_pricing[monthly][<?php echo $i; ?>][price]" class="regular-text" value="<?php echo esc_attr($plan['price'] ?? ''); ?>"></label></p>
                                                <p><label>URL<br>
                                                    <input type="url" name="vc_pricing[monthly][<?php echo $i; ?>][url]" class="regular-text" value="<?php echo esc_url($plan['url'] ?? ''); ?>"></label></p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- Template mensuel -->
                                    <template id="vc-plan-template-monthly">
                                        <div class="plan-block" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                                            <a href="#" class="remove-plan" style="color: red; float: right;">Supprimer</a>
                                            <p><label>Nom du plan<br><input type="text" name="vc_pricing[monthly][__index__][title]" class="regular-text" value=""></label></p>
                                            <p><label>Description<br><textarea name="vc_pricing[monthly][__index__][desc]" class="large-text" rows="3"></textarea></label></p>
                                            <p><label>Prix<br><input type="text" name="vc_pricing[monthly][__index__][price]" class="regular-text" value=""></label></p>
                                            <p><label>URL<br><input type="url" name="vc_pricing[monthly][__index__][url]" class="regular-text" value=""></label></p>
                                        </div>
                                    </template>
                                </div>

                                <h3>Plans annuels</h3>
                                <div class="pricing-plans-container" id="yearly-plans">
                                    <button type="button" class="button add-plan-button" data-type="yearly">+ Ajouter un plan annuel</button>

                                    <?php if (!empty($plans['yearly']) && is_array($plans['yearly'])) : ?>
                                        <?php foreach ($plans['yearly'] as $i => $plan) : ?>
                                            <div class="plan-block" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                                                <a href="#" class="remove-plan" style="color: red; float: right;">Supprimer</a>
                                                <p><label>Nom du plan<br>
                                                    <input type="text" name="vc_pricing[yearly][<?php echo $i; ?>][title]" class="regular-text" value="<?php echo esc_attr($plan['title'] ?? ''); ?>"></label></p>
                                                <p><label>Description<br>
                                                    <textarea name="vc_pricing[yearly][<?php echo $i; ?>][desc]" class="large-text" rows="3"><?php echo esc_textarea($plan['desc'] ?? ''); ?></textarea></label></p>
                                                <p><label>Prix<br>
                                                    <input type="text" name="vc_pricing[yearly][<?php echo $i; ?>][price]" class="regular-text" value="<?php echo esc_attr($plan['price'] ?? ''); ?>"></label></p>
                                                <p><label>URL<br>
                                                    <input type="url" name="vc_pricing[yearly][<?php echo $i; ?>][url]" class="regular-text" value="<?php echo esc_url($plan['url'] ?? ''); ?>"></label></p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <!-- Template annuel -->
                                    <template id="vc-plan-template-yearly">
                                        <div class="plan-block" style="margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;">
                                            <a href="#" class="remove-plan" style="color: red; float: right;">Supprimer</a>
                                            <p><label>Nom du plan<br><input type="text" name="vc_pricing[yearly][__index__][title]" class="regular-text" value=""></label></p>
                                            <p><label>Description<br><textarea name="vc_pricing[yearly][__index__][desc]" class="large-text" rows="3"></textarea></label></p>
                                            <p><label>Prix<br><input type="text" name="vc_pricing[yearly][__index__][price]" class="regular-text" value=""></label></p>
                                            <p><label>URL<br><input type="url" name="vc_pricing[yearly][__index__][url]" class="regular-text" value=""></label></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div style="background-color: #fff; padding: 20px; border-radius: 10px;">
                            <!-- Multi-plateforme -->
                            <p><strong>Multi-plateforme ?</strong>
                                <select id="vc_multi_platform" name="vc_multi_platform">
                                    <option value="no" <?php selected($editing && $multi_platform === 'no'); ?>>Non</option>
                                    <option value="yes" <?php selected($editing && $multi_platform === 'yes'); ?>>Oui</option>
                                </select>
                            </p>
                            <div id="multi-platform-url" style="display:none;">
                                <label for="vc_platform_url">URL plateforme</label><br>
                                <input type="url" name="vc_platform_url" class="regular-text" value="<?php echo esc_url($platform_url ?? ''); ?>">
                            </div>

                            <h3>Autres URLs</h3>
                            <table class="form-table" style="display: flex; flex-direction: column; gap: 20px;">
                                <tr class="container-vs-free-url">
                                    <td>
                                        <div class="container-label">
                                            <label for="vc_url_free">Version Free (URL)</label>
                                        </div>
                                        <div class="container-input">
                                            <input type="url" name="vc_url_free" class="regular-text" value="<?php echo esc_url($url_free ?? ''); ?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="container-vs-free-url">
                                    <td>
                                        <div class="container-label">
                                            <label for="vc_url_doc">Documentation (URL)</label>
                                        </div>
                                        <div class="container-input">
                                            <input type="url" name="vc_url_doc" class="regular-text" value="<?php echo esc_url($url_doc ?? ''); ?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="container-vs-free-url">
                                    <td>
                                        <div class="container-label">
                                            <label for="vc_url_live">Live Demo (URL)</label>
                                        </div>
                                        <div class="container-input">
                                            <input type="url" name="vc_url_live" class="regular-text" value="<?php echo esc_url($url_demo ?? ''); ?>"><br><br>
                                            <label>Description</label><br>
                                            <textarea name="vc_desc_live" class="large-text" rows="2" style="width: 55%;"><?php echo esc_textarea($demo_desc ?? ''); ?></textarea>
                                        </div>
                                    </td>
                                </tr>
                            </div>
                            <div style="background-color: #fff; padding: 20px; border-radius: 10px;">
                                <tr class="container-vs-free-url">
                                    <td>
                                        <div class="container-label">
                                            <label for="vc_url_admin">Admin Demo (URL)</label>
                                        </div>
                                        <div class="container-input">
                                            <input type="url" name="vc_url_admin" class="regular-text" value="<?php echo esc_url($url_admin ?? ''); ?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="container-vs-free-url">
                                    <td>
                                        <div class="container-label">
                                            <label for="vc_url_feature">Request Feature (URL)</label>
                                        </div>
                                        <div class="container-input">
                                            <input type="url" name="vc_url_feature" class="regular-text" value="<?php echo esc_url($url_request ?? ''); ?>">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="container-vs-free-url">
                                    <td>
                                        <div class="container-label">
                                            <label for="vc_url_changelog">Changelog (URL)</label>
                                        </div>
                                        <div class="container-input">
                                            <input type="url" name="vc_url_changelog" class="regular-text" value="<?php echo esc_url($url_changelog ?? ''); ?>">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                    </div>


                    <div id="container-features">
                        <!--pour les features-->
                        <h2>Features</h2>
                        <div>
                            <p>Description....</p>
                        </div>
                        <div id="vc_features_container">
                            <?php
                            if ($editing && !empty($features)) {
                                foreach ($features as $i => $feature) {
                                    ?>
                                    <div class="vc-feature-block" style="margin-bottom: 30px; padding: 15px; border: 1px solid #ccc; position: relative;">
                                        <a href="#" class="button-link-delete remove-feature" style="color: red; position: absolute; top: 10px; right: 10px;">Supprimer</a>

                                        <input type="button" class="button vc-upload-feature-image" value="Uploader une image">
                                        <input type="hidden" name="vc_features[<?php echo $i; ?>][image]" class="vc-feature-image-url" value="<?php echo esc_attr($feature['image']); ?>">
                                        <div class="vc-feature-image-preview"><?php echo $feature['image'] ? '<img src="' . esc_url($feature['image']) . '" style="max-width:100px;">' : ''; ?></div>

                                        <p><label>Titre<br><input type="text" name="vc_features[<?php echo $i; ?>][title]" class="regular-text" value="<?php echo esc_attr($feature['title']); ?>"></label></p>

                                        <p><label>Description</label><br>
                                            <textarea name="vc_features[<?php echo $i; ?>][desc]" rows="6" class="vc-feature-desc" style="width:100%;"><?php echo esc_textarea($feature['desc']); ?></textarea>
                                        </p>

                                        <p><label>URL du bouton<br><input type="url" name="vc_features[<?php echo $i; ?>][button]" class="regular-text" value="<?php echo esc_attr($feature['button']); ?>"></label></p>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                            <p><a href="#" class="button" id="add-feature-button">Ajouter une feature</a></p>

                        <template id="vc-feature-template">
                            <div class="vc-feature-block" style="margin-bottom: 30px; padding: 15px; border: 1px solid #ccc; position: relative;">
                                <a href="#" class="button-link-delete remove-feature" style="color: red; position: absolute; top: 10px; right: 10px;">Supprimer</a>

                                <input type="button" class="button vc-upload-feature-image" value="Uploader une image">
                                <input type="hidden" name="vc_features[__index__][image]" class="vc-feature-image-url">
                                <div class="vc-feature-image-preview" style="margin-top: 10px;"></div>

                                <p><label>Titre<br><input type="text" name="vc_features[__index__][title]" class="regular-text"></label></p>

                                <p>
                                    <label>Description</label><br>
                                    <textarea name="vc_features[__index__][desc]" rows="6" class="vc-feature-desc" style="width:100%;"></textarea>
                                </p>

                                <p><label>URL du bouton<br><input type="url" name="vc_features[__index__][button]" class="regular-text"></label></p>
                            </div>
                        </template>
                    </div>
                
                    <!--pour faq-->
                    <div id="container-faqs">
                        <h2>FAQ</h2>
                        <div>
                            <p>Description....</p>
                        </div>
                        <div id="vc_faqs_container">
                            <?php
                            if ($editing && !empty($faqs)) {
                                foreach ($faqs as $i => $faq) {
                                    ?>
                                    <div class="vc-faq-block" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                                        <p>
                                            <label>Titre de la question</label><br>
                                            <input type="text" name="vc_faqs[<?php echo $i; ?>][title]" value="<?php echo esc_attr($faq['title']); ?>" style="width:100%;" />
                                        </p>
                                        <p>
                                            <label>URL du bouton (facultatif)</label><br>
                                            <input type="url" name="vc_faqs[<?php echo $i; ?>][button]" value="<?php echo esc_attr($faq['button']); ?>" style="width:100%;" />
                                        </p>
                                        <p>
                                            <label>Description</label><br>
                                            <textarea name="vc_faqs[<?php echo $i; ?>][desc]" rows="6" class="vc-faq-desc" style="width:100%;"><?php echo esc_textarea($faq['desc']); ?></textarea>
                                        </p>
                                        <p>
                                            <button class="button remove-faq">Supprimer cette question</button>
                                        </p>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <button class="button button-secondary" id="add-faq-button">+ Ajouter une question</button>

                        <template id="vc-faq-template">
                            <div class="vc-faq-block" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                                <p>
                                    <label>Titre de la question</label><br>
                                    <input type="text" name="vc_faqs[__index__][title]" style="width:100%;" />
                                </p>
                                <p>
                                    <label>URL du bouton (facultatif)</label><br>
                                    <input type="url" name="vc_faqs[__index__][button]" style="width:100%;" />
                                </p>
                                <p>
                                    <label>Description</label><br>
                                    <textarea name="vc_faqs[__index__][desc]" rows="6" class="vc-faq-desc" style="width:100%;"></textarea>
                                </p>
                                <p>
                                    <button class="button remove-faq">Supprimer cette question</button>
                                </p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <p><input type="submit" value="Enregistrer l’app" class="button button-primary"></p>
            <?php if ($editing): ?>
                <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
            <?php endif; ?>
        </form>
    </div>
    <?php
}

add_action('admin_enqueue_scripts', function($hook) {
    if (strpos($hook, 'vc_apps') !== false) {
        // Charger la media library WordPress
        wp_enqueue_media();

        // Charger ton JS admin
        wp_enqueue_script(
            'vc-admin-js',
            plugin_dir_url(__FILE__) . 'assets/js/admin.js',
            ['jquery'],
            null,
            true
        );

        // Charger ton CSS admin
        wp_enqueue_style(
            'vc-admin-css',
            plugin_dir_url(__FILE__) . 'assets/css/admin.css',
            [],
            null
        );
    }
});


add_action('wp_ajax_vc_add_custom_category', 'vc_handle_add_custom_category');

function vc_handle_add_custom_category() {
    // Sécurité basique
    if (!current_user_can('manage_categories')) {
        wp_send_json_error(['message' => 'Permission refusée.']);
    }

    $name = sanitize_text_field($_POST['name'] ?? '');

    if (empty($name)) {
        wp_send_json_error(['message' => 'Le nom de la catégorie est vide.']);
    }

    // Vérification insensible à la casse
    $existing_cats = get_terms([
        'taxonomy' => 'category',
        'hide_empty' => false,
    ]);

    foreach ($existing_cats as $cat) {
        if (strcasecmp($cat->name, $name) === 0) {
            wp_send_json_error(['message' => 'Cette catégorie existe déjà.']);
        }
    }

    // Insertion
    $new_cat = wp_insert_term($name, 'category');

    if (is_wp_error($new_cat)) {
        wp_send_json_error(['message' => 'Erreur lors de l\'ajout : ' . $new_cat->get_error_message()]);
    }

    wp_send_json_success([
        'term_id' => $new_cat['term_id'],
        'name'    => $name,
    ]);
}

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




