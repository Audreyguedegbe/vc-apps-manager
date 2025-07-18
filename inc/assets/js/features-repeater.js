jQuery(document).ready(function($){
    let index = $('#vc-app-features-wrapper .feature-item').length;

    $('#add-feature').on('click', function(e){
        e.preventDefault();
        let template = $('#feature-template').html().replace(/__index__/g, index);
        $('#vc-app-features-wrapper').append(template);
        index++;
    });

    $('#vc-app-features-wrapper').on('click', '.remove-feature', function(e){
        e.preventDefault();
        $(this).closest('.feature-item').remove();
    });

    // Uploader d'image
    $(document).on('click', '.vc-upload-banner', function(e){
        e.preventDefault();
        const button = $(this);
        const input = button.prev('.vc-banner-url');

        const custom_uploader = wp.media({
            title: 'Sélectionner une image',
            button: {
                text: 'Utiliser cette image'
            },
            multiple: false
        });

        custom_uploader.on('select', function(){
            const attachment = custom_uploader.state().get('selection').first().toJSON();
            input.val(attachment.url);
        });

        custom_uploader.open();
    });
});
