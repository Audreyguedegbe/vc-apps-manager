<?php

if (!function_exists('vc_get_reviews')) {
    add_action('wp_ajax_vc_get_reviews', 'vc_get_reviews');
    add_action('wp_ajax_nopriv_vc_get_reviews', 'vc_get_reviews');

    function vc_get_reviews() {
        $post_id = absint($_POST['post_id']);
        $paged = isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1;
        $per_page = 3;

        $reviews = get_post_meta($post_id, 'vc_reviews', true);
        if (!is_array($reviews)) $reviews = [];

        

        $total = count($reviews);
        $total_pages = ceil($total / $per_page);

        // Slice reviews for pagination
        $offset = ($paged - 1) * $per_page;
        $paged_reviews = array_slice($reviews, $offset, $per_page);

        ob_start();

        if (!empty($paged_reviews)) {
            foreach ($paged_reviews as $review) {
                $author_name = esc_html($review['author']);
                $date = isset($review['date']) ? date_i18n('M d, Y', strtotime($review['date'])) : '';
                $avatar = isset($review['avatar']) && $review['avatar'] !== '' ? esc_attr($review['avatar']) : 'profil.jpg';
                $avatar_url = plugin_dir_url(__DIR__) . 'assets/images/' . $avatar;
                echo '<div class="" style="display: flex; gap: 20px;">';
                echo '<div class="vc-review-avatar" style="flex-shrink:0;">';
                echo '<img src="' . esc_url($avatar_url) . '" alt="Avatar" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">';
                echo '</div>';
                echo '<div class="vc-review" >';

                echo '<div style="display: flex;gap: 20px;justify-content: center;align-items: center;">';
                echo '<div style="width: 50%;display: flex;align-items: center;gap: 10px;""><span class="name-author"><strong>' . $author_name . '</strong></span><span style="font-size:12px; color:#888;">' . esc_html($date) . '</span></div>';
                echo '<div class="vc-stars left-star" style="color: #f39c12; font-size: 22px; display:flex; justify-content:end; width: 50%;">';

                $rating = floatval($review['rating']);
                $full = floor($rating);
                $half = ($rating - $full) >= 0.5 ? 1 : 0;
                $empty = 5 - $full - $half;

                // Étoiles pleines
                echo str_repeat('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 12.705 512 486.59" width="20px" height="20px" fill="#ffd700" style="margin-left: 0px;"><polygon points="256.814,12.705 317.205,198.566 512.631,198.566 354.529,313.435 414.918,499.295 256.814,384.427 98.713,499.295 159.102,313.435 1,198.566 196.426,198.566 "/></svg>', $full);

                // Étoile demi (si besoin, peut être remplacée par un SVG demi-étoile)
                if ($half) {
                    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 12.705 512 486.59" width="20px" height="20px" fill="#ffd700" style="margin-left: 0px;"><defs><linearGradient id="halfGrad"><stop offset="50%" stop-color="#ffd700"/><stop offset="50%" stop-color="#ccc"/></linearGradient></defs><polygon points="256.814,12.705 317.205,198.566 512.631,198.566 354.529,313.435 414.918,499.295 256.814,384.427 98.713,499.295 159.102,313.435 1,198.566 196.426,198.566 " fill="url(#halfGrad)"/></svg>';
                }

                // Étoiles vides
                echo str_repeat('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 12.705 512 486.59" width="20px" height="20px" fill="#ccc" style="margin-left: 0px;"><polygon points="256.814,12.705 317.205,198.566 512.631,198.566 354.529,313.435 414.918,499.295 256.814,384.427 98.713,499.295 159.102,313.435 1,198.566 196.426,198.566 "/></svg>', $empty);

                echo '</div>';

                echo '</div>';
                echo '<div>';
                echo '<p class="comments">' . esc_html($review['comment']) . '</p>';
                echo '</div>';

                echo '</div>';
                echo '</div>';

            }

            // Pagination HTML
            echo '<div class="vc-pagination">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i === $paged) ? 'style="font-weight:bold;"' : '';
                echo '<button class="vc-review-page" data-page="' . $i . '" ' . $active . '>' . $i . '</button> ';
            }
            echo '</div>';
        } else {
            echo '<p>Aucun avis pour le moment.</p>';
        }

        wp_send_json_success(ob_get_clean());
    }
}


add_action('wp_ajax_vc_add_review', 'vc_add_review');
add_action('wp_ajax_nopriv_vc_add_review', 'vc_add_review');

function vc_add_review() {
    // Vérification utilisateur connecté
    if (!is_user_logged_in()) {
        wp_send_json_error('Vous devez être connecté pour laisser un avis.');
    }

    $current_user = wp_get_current_user();
    $author  = $current_user->display_name;
    $post_id = absint($_POST['post_id']);
    $rating  = intval($_POST['rating']);
    $comment = sanitize_textarea_field($_POST['comment']);

    if (!$rating || !$comment) {
        wp_send_json_error('Champs requis manquants.');
    }

    $reviews = get_post_meta($post_id, 'vc_reviews', true);
    if (!is_array($reviews)) $reviews = [];

    $reviews[] = [
        'avatar'  => sanitize_file_name($_POST['avatar'] ?? 'profil.jpg'), // tu peux ici aussi mettre un avatar user si tu veux
        'author'  => $author,
        'rating'  => $rating,
        'comment' => $comment,
        'date'    => current_time('mysql'),
    ];

    update_post_meta($post_id, 'vc_reviews', $reviews);
    wp_send_json_success('Avis ajouté.');
}


function vc_get_reviews_summary($post_id) {
    $reviews = get_post_meta($post_id, 'vc_reviews', true);
    if (!is_array($reviews) || empty($reviews)) return false;

    $summary = [
        'count' => 0,
        'average' => 0,
        'ratings' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
    ];

    $total_score = 0;

    foreach ($reviews as $review) {
        $rating = intval($review['rating']);
        if ($rating >= 1 && $rating <= 5) {
            $summary['ratings'][$rating]++;
            $summary['count']++;
            $total_score += $rating;
        }
    }

    $summary['average'] = $summary['count'] > 0 ? round($total_score / $summary['count'], 1) : 0;

    return $summary;
}
