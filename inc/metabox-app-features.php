<?php
function vc_apps_add_features_metabox() {
    add_meta_box(
        'vc_app_features',
        'App Features',
        'vc_apps_render_features_metabox',
        'vc-apps',
        'normal',
        'default'
    );

    add_action('admin_enqueue_scripts', 'vc_apps_enqueue_features_scripts');
}
add_action('add_meta_boxes', 'vc_apps_add_features_metabox');

function vc_apps_enqueue_features_scripts() {
    if (get_post_type() === 'vc-apps') {
        wp_enqueue_media();
        wp_enqueue_script('vc-apps-features', plugin_dir_url(__FILE__) . 'assets/js/features-repeater.js', ['jquery'], null, true);
    }
}

function vc_apps_render_features_metabox($post) {
    $features = get_post_meta($post->ID, '_vc_app_features', true);
    wp_nonce_field('vc_apps_save_features', 'vc_apps_features_nonce');
    ?>

    <div id="vc-app-features-wrapper">
        <?php if (!empty($features) && is_array($features)) : ?>
            <?php foreach ($features as $index => $f) : ?>
                <div class="feature-item" style="border:1px solid #ccc;padding:10px;margin-bottom:15px;">
                    <p><strong>Bannière (URL)</strong></p>
                    <input type="text" name="vc_app_features[<?php echo $index; ?>][banner]" value="<?php echo esc_attr($f['banner']); ?>" class="vc-banner-url" style="width:100%;" />
                    <button class="button vc-upload-banner" type="button">Uploader une bannière</button>

                    <p><strong>Titre :</strong></p>
                    <input type="text" name="vc_app_features[<?php echo $index; ?>][title]" value="<?php echo esc_attr($f['title']); ?>" style="width:100%;" />

                    <p><strong>Description :</strong></p>
                    <?php
                    $editor_id = 'vc_app_features_' . $index . '_desc';
                    wp_editor($f['desc'], $editor_id, [
                        'textarea_name' => "vc_app_features[$index][desc]",
                        'textarea_rows' => 5,
                    ]);
                    ?>

                    <p><strong>Bouton URL :</strong></p>
                    <input type="text" name="vc_app_features[<?php echo $index; ?>][url]" value="<?php echo esc_attr($f['url']); ?>" style="width:100%;" />

                    <p><button type="button" class="remove-feature button-link-delete">Supprimer</button></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button type="button" id="add-feature" class="button">+ Ajouter une Feature</button>

    <div id="feature-template" style="display:none;">
        <div class="feature-item" style="border:1px solid #ccc;padding:10px;margin-bottom:15px;">
            <p><strong>Bannière (URL)</strong></p>
            <input type="text" name="vc_app_features[__index__][banner]" class="vc-banner-url" style="width:100%;" />
            <button class="button vc-upload-banner" type="button">Uploader une bannière</button>

            <p><strong>Titre :</strong></p>
            <input type="text" name="vc_app_features[__index__][title]" style="width:100%;" />

            <p><strong>Description :</strong></p>
            <textarea name="vc_app_features[__index__][desc]" style="width:100%;height:100px;"></textarea>

            <p><strong>Bouton URL :</strong></p>
            <input type="text" name="vc_app_features[__index__][url]" style="width:100%;" />

            <p><button type="button" class="remove-feature button-link-delete">Supprimer</button></p>
        </div>
    </div>
<?php
}

function vc_apps_save_features_metabox($post_id) {
    if (!isset($_POST['vc_apps_features_nonce']) || !wp_verify_nonce($_POST['vc_apps_features_nonce'], 'vc_apps_save_features')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['vc_app_features']) && is_array($_POST['vc_app_features'])) {
        $clean = [];

        foreach ($_POST['vc_app_features'] as $feature) {
            if (!empty($feature['title']) || !empty($feature['desc']) || !empty($feature['banner']) || !empty($feature['url'])) {
                $clean[] = [
                    'title'  => sanitize_text_field($feature['title']),
                    'desc'   => wp_kses_post($feature['desc']),
                    'banner' => esc_url_raw($feature['banner']),
                    'url'    => esc_url_raw($feature['url']),
                ];
            }
        }

        update_post_meta($post_id, '_vc_app_features', $clean);
    }
}
add_action('save_post', 'vc_apps_save_features_metabox');
