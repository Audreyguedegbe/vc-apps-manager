jQuery(document).ready(function ($) {
    function loadReviews(paged = 1) {
        $.post(vcAjax.ajax_url, {
            action: 'vc_get_reviews',
            post_id: vcAjax.post_id,
            paged: paged
        }, function (response) {
            if (response.success) {
                $('#vc_reviews_display').html(response.data);
            } else {
                $('#vc_reviews_display').html('<p>Erreur de chargement des avis.</p>');
            }
        });
    }

    loadReviews();

    $('#vc_add_review_form').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const formData = $(this).serialize();

        $.post(vcAjax.ajax_url, formData + '&action=vc_add_review&post_id=' + vcAjax.post_id, function (response) {
            if (response.success) {
                $('#vc_add_review_form').trigger('reset');
                loadReviews();
            } else {
                alert('Erreur : ' + response.data);
            }
        });
        console.log("Formulaire soumis");
    });

    $(document).on('click', '.vc-review-page', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadReviews(page);
    });

});