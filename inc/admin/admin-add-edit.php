<?php
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
        $reviews = get_post_meta($post_id, 'vc_reviews', true);

    }
}

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo $editing ? 'Modifier l’app' : 'Ajouter une nouvelle App'; ?></h1>
            
        
        <div style="float: right;">
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
                        <div class="menu-info"> <a href="#container-reviews">Reviews</a> </div>
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
                                        <label for="vc_app_short_desc">Description courte :</label>
                                    </div>
                                    <div class="container-input">
                                        <?php
                                            wp_editor($editing ? $short_desc : '', 'vc_app_short_desc', [
                                                'textarea_name' => 'vc_app_short_desc',
                                                'media_buttons' => true,
                                                'textarea_rows' => 4,
                                            ]);
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="container-label">
                                        <label for="vc_app_long_desc">Description longue :</label>
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

                    <!--pour features-->
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
                                    <div class="vc-feature-block" data-index="<?php echo $i; ?>" style="margin-bottom: 30px; padding: 15px; border: 1px solid #ccc; position: relative;">
                                        <a href="#" class="button-link-delete remove-feature" style="color: red; position: absolute; top: 10px; right: 10px;">Supprimer</a>

                                        <input type="button" class="button vc-upload-feature-image" value="Uploader une image">
                                        <input type="hidden" name="vc_features[<?php echo $i; ?>][image]" class="vc-feature-image-url" value="<?php echo esc_attr($feature['image']); ?>">
                                        <div class="vc-feature-image-preview"><?php echo $feature['image'] ? '<img src="' . esc_url($feature['image']) . '" style="max-width:100px;">' : ''; ?></div>

                                        <p><label>Titre<br><input type="text" name="vc_features[<?php echo $i; ?>][title]" class="regular-text" value="<?php echo esc_attr($feature['title']); ?>"></label></p>

                                        <p><label>Description</label><br>
                                            <textarea name="vc_features[<?php echo $i; ?>][desc]" rows="6" class="vc-feature-desc" style="width:100%;"><?php echo esc_textarea($feature['desc']); ?></textarea>
                                        </p>

                                        <p><label>URL du bouton<br><input type="url" name="vc_features[<?php echo $i; ?>][button]" class="regular-text" value="<?php echo esc_attr($feature['button']); ?>"></label></p>
                                        <label>Style d'affichage<br>
                                        <select name="vc_features[<?php echo $i; ?>][style]" id="vc_feature_style_<?php echo $i; ?>">
                                            <option value="left" <?php selected($feature['style'] ?? '', 'left'); ?>>Image Left / Text Right</option>
                                            <option value="right" <?php selected($feature['style'] ?? '', 'right'); ?>>Image Right / Text Left</option>
                                            <option value="stacked" <?php selected($feature['style'] ?? '', 'stacked'); ?>>Stacked (Image Top)</option>
                                        </select>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                            <p><a href="#" class="button" id="add-feature-button">Ajouter une feature</a></p>

                        <template id="vc-feature-template">
                            <div class="vc-feature-block" data-index="<?php echo $i; ?>" style="margin-bottom: 30px; padding: 15px; border: 1px solid #ccc; position: relative;">
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
                                <p>
                                    <label>Style d'affichage<br>
                                        <select name="vc_features[__index__][style]" class="vc-feature-style">
                                            <option value="left">Image Left / Text Right</option>
                                            <option value="right">Image Right / Text Left</option>
                                            <option value="stacked">Stacked (Image Top)</option>
                                        </select>
                                    </label>
                                </p>
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

                    <!--pour les reviews-->
                    <div id="container-reviews">
                        <h2>Test Reviews</h2>
                        <div>
                            <p>Ajoutez manuellement des avis associés à cette app. (utiles pour pré-remplir des données ou importer).</p>
                        </div>

                        <div id="vc_reviews_container">
                            <?php
                            $reviews = get_post_meta($post_id, 'vc_reviews', true);
                            if ($editing && !empty($reviews) && is_array($reviews)) {
                                foreach ($reviews as $i => $review) {
                                    ?>
                                    <div class="vc-review-block" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                                        <p>
                                            <label>Nom de l'auteur</label><br>
                                            <input type="text" name="vc_reviews[<?php echo $i; ?>][author]" value="<?php echo esc_attr($review['author']); ?>" style="width:100%;" />
                                        </p>
                                        <p>
                                            <label>Note (1 à 5)</label><br>
                                            <select name="vc_reviews[<?php echo $i; ?>][rating]" style="width:100%;">
                                                <?php for ($j = 5; $j >= 1; $j--): ?>
                                                    <option value="<?php echo $j; ?>" <?php selected($review['rating'], $j); ?>><?php echo $j; ?> ★</option>
                                                <?php endfor; ?>
                                            </select>
                                        </p>
                                        <p>
                                            <label>Commentaire</label><br>
                                            <textarea name="vc_reviews[<?php echo $i; ?>][comment]" rows="5" style="width:100%;"><?php echo esc_textarea($review['comment']); ?></textarea>
                                        </p>
                                        <p>
                                            <button class="button remove-review">Supprimer cet avis</button>
                                        </p>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>

                        <button class="button button-secondary" id="add-review-button">+ Ajouter un avis</button>

                        <template id="vc-review-template">
                            <div class="vc-review-block" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                                <p>
                                    <label>Nom de l'auteur</label><br>
                                    <input type="text" name="vc_reviews[__index__][author]" style="width:100%;" />
                                </p>
                                <p>
                                    <label>Note (1 à 5)</label><br>
                                    <select name="vc_reviews[__index__][rating]" style="width:100%;">
                                        <option value="">Choisir une note</option>
                                        <option value="5">5 ★</option>
                                        <option value="4">4 ★</option>
                                        <option value="3">3 ★</option>
                                        <option value="2">2 ★</option>
                                        <option value="1">1 ★</option>
                                    </select>
                                </p>
                                <p>
                                    <label>Commentaire</label><br>
                                    <textarea name="vc_reviews[__index__][comment]" rows="5" style="width:100%;"></textarea>
                                </p>
                                <p>
                                    <button class="button remove-review">Supprimer cet avis</button>
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