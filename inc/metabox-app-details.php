<?php
// Ajouter la metabox
function vc_apps_add_metabox() {
    add_meta_box(
        'vc_app_details',
        'App Details',
        'vc_apps_render_metabox',
        'vc-apps',
        'normal',
        'high'
    );

    // Enqueue la media library WordPress
    add_action('admin_enqueue_scripts', 'vc_apps_enqueue_media');
}
add_action('add_meta_boxes', 'vc_apps_add_metabox');

function vc_apps_enqueue_media() {
    wp_enqueue_media(); // Active la media library dans l'admin
}

// Affichage de la metabox
function vc_apps_render_metabox($post) {
    // Récupérer les données enregistrées
    $logo = get_post_meta($post->ID, '_vc_app_logo', true);
    $banner = get_post_meta($post->ID, '_vc_app_banner', true);
    $short_desc = get_post_meta($post->ID, '_vc_app_short_desc', true);
    $long_desc = get_post_meta($post->ID, '_vc_app_long_desc', true);
    $benefits = get_post_meta($post->ID, '_vc_app_benefits', true);
    $benefit_url = get_post_meta($post->ID, '_vc_app_benefit_url', true);

    // Sécurité
    wp_nonce_field('vc_apps_save_metabox', 'vc_apps_metabox_nonce');
    ?>

    <p><label for="vc_app_logo">Logo:</label><br>
        <input type="text" name="vc_app_logo" id="vc_app_logo" value="<?php echo esc_attr($logo); ?>" style="width:100%;" />
        <input type="button" class="button vc-upload-button" data-target="#vc_app_logo" value="Uploader un logo" />
    </p>

    <p><label for="vc_app_banner">Bannière:</label><br>
        <input type="text" name="vc_app_banner" id="vc_app_banner" value="<?php echo esc_attr($banner); ?>" style="width:100%;" />
        <input type="button" class="button vc-upload-button" data-target="#vc_app_banner" value="Uploader une bannière" />
    </p>

    <p><label for="vc_app_short_desc">Courte description :</label><br>
        <textarea name="vc_app_short_desc" id="vc_app_short_desc" rows="3" style="width:100%;"><?php echo esc_textarea($short_desc); ?></textarea>
    </p>

    <p><label for="vc_app_long_desc">Description longue :</label><br>
        <textarea name="vc_app_long_desc" id="vc_app_long_desc" rows="5" style="width:100%;"><?php echo esc_textarea($long_desc); ?></textarea>
    </p>

    <p><label for="vc_app_benefits">Bénéfices</label><br>
        <textarea name="vc_app_benefits" id="vc_app_benefits" rows="3" style="width:100%;"><?php echo esc_textarea($benefits); ?></textarea>
    </p>

    <p><label for="vc_app_benefit_url">URL associée aux bénéfices :</label><br>
        <input type="url" name="vc_app_benefit_url" id="vc_app_benefit_url" value="<?php echo esc_attr($benefit_url); ?>" style="width:100%;" />
    </p>

    <script>
    jQuery(document).ready(function($){
        $('.vc-upload-button').on('click', function(e){
            e.preventDefault();
            const button = $(this);
            const target = $(button.data('target'));

            const custom_uploader = wp.media({
                title: 'Choisir une image',
                button: {
                    text: 'Utiliser cette image'
                },
                multiple: false
            });

            custom_uploader.on('select', function(){
                const attachment = custom_uploader.state().get('selection').first().toJSON();
                target.val(attachment.url);
            });

            custom_uploader.open();
        });
    });
    </script>

    <?php
}

// Enregistrement des champs
function vc_apps_save_metabox($post_id) {
    if (!isset($_POST['vc_apps_metabox_nonce']) || !wp_verify_nonce($_POST['vc_apps_metabox_nonce'], 'vc_apps_save_metabox')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['post_type']) && 'vc-apps' === $_POST['post_type']) {
        update_post_meta($post_id, '_vc_app_logo', sanitize_text_field($_POST['vc_app_logo']));
        update_post_meta($post_id, '_vc_app_banner', sanitize_text_field($_POST['vc_app_banner']));
        update_post_meta($post_id, '_vc_app_short_desc', sanitize_textarea_field($_POST['vc_app_short_desc']));
        update_post_meta($post_id, '_vc_app_long_desc', sanitize_textarea_field($_POST['vc_app_long_desc']));
        update_post_meta($post_id, '_vc_app_benefits', sanitize_textarea_field($_POST['vc_app_benefits']));
        update_post_meta($post_id, '_vc_app_benefit_url', esc_url_raw($_POST['vc_app_benefit_url']));
    }
}
add_action('save_post', 'vc_apps_save_metabox');
