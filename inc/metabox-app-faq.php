<?php
function vc_apps_add_faq_metabox() {
    add_meta_box(
        'vc_app_faq',
        'App FAQ',
        'vc_apps_render_faq_metabox',
        'vc-apps',
        'normal',
        'default'
    );

    // Charge le script
    add_action('admin_enqueue_scripts', 'vc_apps_faq_enqueue_script');
}
add_action('add_meta_boxes', 'vc_apps_add_faq_metabox');

function vc_apps_faq_enqueue_script($hook) {
    if (get_post_type() === 'vc-apps') {
        wp_enqueue_script('vc-apps-faq', plugin_dir_url(__FILE__) . 'assets/js/faq-repeater.js', ['jquery'], null, true);
    }
}

function vc_apps_render_faq_metabox($post) {
    $faqs = get_post_meta($post->ID, '_vc_app_faq', true);
    wp_nonce_field('vc_apps_save_faq', 'vc_apps_faq_nonce');
    ?>

    <div id="vc-app-faq-wrapper">
        <?php if (!empty($faqs) && is_array($faqs)) : ?>
            <?php foreach ($faqs as $index => $faq) : ?>
                <div class="faq-item" style="margin-bottom:20px;border:1px solid #ccc;padding:10px;">
                    <input type="text" name="vc_app_faq[<?php echo $index; ?>][title]" placeholder="Titre de la question" value="<?php echo esc_attr($faq['title']); ?>" style="width:100%;margin-bottom:5px;" />
                    <textarea name="vc_app_faq[<?php echo $index; ?>][desc]" placeholder="Description" style="width:100%;margin-bottom:5px;"><?php echo esc_textarea($faq['desc']); ?></textarea>
                    <input type="text" name="vc_app_faq[<?php echo $index; ?>][url]" placeholder="URL du bouton (optionnel)" value="<?php echo esc_attr($faq['url']); ?>" style="width:100%;" />
                    <button class="remove-faq button-link-delete" style="margin-top:10px;">Supprimer</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button type="button" id="add-faq" class="button">+ Ajouter une FAQ</button>

    <!-- Template caché -->
    <div id="faq-template" style="display:none;">
        <div class="faq-item" style="margin-bottom:20px;border:1px solid #ccc;padding:10px;">
            <input type="text" name="vc_app_faq[__index__][title]" placeholder="Titre de la question" style="width:100%;margin-bottom:5px;" />
            <textarea name="vc_app_faq[__index__][desc]" placeholder="Description" style="width:100%;margin-bottom:5px;"></textarea>
            <input type="text" name="vc_app_faq[__index__][url]" placeholder="URL du bouton (optionnel)" style="width:100%;" />
            <button class="remove-faq button-link-delete" style="margin-top:10px;">Supprimer</button>
        </div>
    </div>
<?php
}

function vc_apps_save_faq_metabox($post_id) {
    if (!isset($_POST['vc_apps_faq_nonce']) || !wp_verify_nonce($_POST['vc_apps_faq_nonce'], 'vc_apps_save_faq')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['post_type']) && 'vc-apps' === $_POST['post_type']) {
        $faqs = $_POST['vc_app_faq'] ?? [];

        // Nettoyage
        $sanitized = [];
        foreach ($faqs as $faq) {
            if (!empty($faq['title']) || !empty($faq['desc']) || !empty($faq['url'])) {
                $sanitized[] = [
                    'title' => sanitize_text_field($faq['title']),
                    'desc'  => sanitize_textarea_field($faq['desc']),
                    'url'   => esc_url_raw($faq['url']),
                ];
            }
        }

        update_post_meta($post_id, '_vc_app_faq', $sanitized);
    }
}
add_action('save_post', 'vc_apps_save_faq_metabox');
