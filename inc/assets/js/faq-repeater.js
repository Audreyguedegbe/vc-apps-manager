jQuery(document).ready(function($) {
    let index = $('#vc-app-faq-wrapper .faq-item').length;

    $('#add-faq').on('click', function(e) {
        e.preventDefault();
        let template = $('#faq-template').html();
        template = template.replace(/__index__/g, index);
        $('#vc-app-faq-wrapper').append(template);
        index++;
    });

    $('#vc-app-faq-wrapper').on('click', '.remove-faq', function(e) {
        e.preventDefault();
        $(this).closest('.faq-item').remove();
    });
});
