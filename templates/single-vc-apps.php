<?php
get_header();

if (have_posts()):
    while (have_posts()):
        the_post(); ?>
        <!--cette div a enlevé après-->
        <div class="vc-max-container">
            <div class="section-pricing">
                <div class="container-banner vc-flex-item">
                    <?php
                    // Récupération de short_desc enregistré en custom meta
                    $short_desc = get_post_meta(get_the_ID(), 'vc_app_short_desc', true);
    
                    // Affichage si short_desc existe
                    if (!empty($short_desc)): ?>
                        <div class="container-vc-app-short-desc">
                            <?php echo wp_kses_post(wpautop($short_desc)); ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    // Récupération de l'URL du banner enregistré en custom meta
                    $banner = get_post_meta(get_the_ID(), 'vc_app_banner', true);
    
                    // Affichage si banner existe
                    if (!empty($banner)): ?>
                        <div class="vc-app-banner">
                            <img src="<?php echo esc_url($banner); ?>" alt="Banner de l'app" style="width: 100%;" />
                        </div>
                    <?php endif; ?>
    
                </div>
                <div class="container-infos vc-flex-item">
                    <div class="vc-flex">
                        <div class="container-logo">
                            <?php
                            // Récupération de l'URL du logo enregistré en custom meta
                            $logo = get_post_meta(get_the_ID(), 'vc_app_logo', true);
    
                            // Affichage si le logo existe
                            if (!empty($logo)): ?>
                                <img src="<?php echo esc_url($logo); ?>" alt="Logo de l'app" class="logo" style="width: 100%;" />
    
                            <?php endif; ?>
                        </div>
                        <div class="container-title-app">
                            <h2 class="title-app"><?php the_title(); ?></h2>
                            <div>
                                <?php
                                $summary = vc_get_reviews_summary(get_the_ID());
                                if ($summary) :
                                    $total = $summary['count'];
                                    $avg   = $summary['average'];
                                    $ratings = $summary['ratings'];
                                    ?>

                                    <div class="vc-review-summary">
                                        <div class="vc-review-header">
                                            <div class="vc-stars left-star" style="color: #f39c12; font-size: 22px;">
                                                <?php
                                                $full = floor($avg);
                                                $half = ($avg - $full) >= 0.5 ? 1 : 0;
                                                $empty = 5 - $full - $half;

                                                echo str_repeat('<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 12.705 512 486.59" x="0px" y="0px" xml:space="preserve" width="20px" height="20px" fill="#ffd700" style="margin-left: 0px;"><polygon points="256.814,12.705 317.205,198.566 512.631,198.566 354.529,313.435 414.918,499.295 256.814,384.427 98.713,499.295 159.102,313.435 1,198.566 196.426,198.566 "></polygon></svg>', $full);
                                                if ($half) echo '<svg style="margin-top: -1px;" xmlns="http://www.w3.org/2000/svg" width="21px" height="21px" viewBox="0 0 24 24">
                                                                    <defs>
                                                                        <linearGradient id="halfGrad">
                                                                            <stop offset="50%" stop-color="#ffd700"/>
                                                                            <stop offset="50%" stop-color="#ccc"/>
                                                                        </linearGradient>
                                                                    </defs>
                                                                    <path fill="url(#halfGrad)" d="M12 2l2.8 8.6H24l-7.2 5.2L18.8 24 12 18.2 5.2 24l1.9-8.2L0 10.6h9.2z"/>
                                                                </svg>'; // Ou ajoute une icône spéciale
                                                echo '<span style="color:#ccc; font-size: 26px;margin-top: -2px;">' . str_repeat('★', $empty) . '</span>';
                                                ?>
                                            </div>
                                            <div class="vc-review-average" style="font-size: 18px;">
                                                <strong><?php echo number_format($avg, 1); ?></strong> – <?php echo $total; ?> review<?php echo $total > 1 ? 's' : ''; ?>
                                            </div>
                                        </div>

                                        <div class="vc-review-bars">
                                            <?php for ($i = 5; $i >= 1; $i--) :
                                                $count = $ratings[$i];
                                                $percent = $total > 0 ? round(($count / $total) * 100) : 0;
                                                ?>
                                                <div class="vc-bar-row">
                                                    <div style="width: 30px;"><?php echo $i; ?>★</div>
                                                    <div style="flex: 1; background: #e0e0e0; height: 8px; margin: 0 8px; position: relative; border-radius: 4px;">
                                                        <div style="background: #00acc1; width: <?php echo $percent; ?>%; height: 100%; border-radius: 4px;"></div>
                                                    </div>
                                                    <div style="width: 20px; text-align: right;"><?php echo $count; ?></div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <div class="container-description">
                        <?php
                        // Récupération de la description enregistré en custom meta
                        $long_desc = get_post_meta(get_the_ID(), 'vc_app_long_desc', true);
    
                        // Affichage si la description existe
                        if (!empty($long_desc)): ?>
                            <?php echo wp_kses_post(wpautop($long_desc)); ?>
    
                        <?php endif; ?>
                    </div>
                    <div>
                        <a href="#scrollable-content" style="text-decoration: none;">
                            <button class="btn-view-feature">
                                View features
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#AF2386">
                                    <path d="M504-480 320-664l56-56 240 240-240 240-56-56 184-184Z"/>
                                </svg>
                            </button>
                        </a>
                    </div>
    
                </div>
                <div class="container-info-pricing vc-flex-item">
                    <div class="block-pricing">
                        <?php
                        list($type, $plans) = vc_apps_get_pricing_structured(get_the_ID());
    
                        if ($type === 'simple_nosub') {
                            $plan = $plans['general'];
                            ?>
                            <div class="pricing-box">
                                <h3>Tarif unique</h3>
                                <p><strong>Description :</strong> <?php echo esc_html($plan['desc']); ?></p>
                                <p><strong>Prix :</strong> <?php echo esc_html($plan['price']); ?></p>
                                <?php if (!empty($plan['url'])): ?>
                                    <p><a href="<?php echo esc_url($plan['url']); ?>" class="btn">Acheter</a></p>
                                <?php endif; ?>
                            </div>
                            <?php
                        } elseif ($type === 'multi_nosub') {
                            ?>
                            <div class="pricing-box">
                                <h3>Plans disponibles</h3>
                                <?php foreach ($plans as $key => $plan): ?>
                                    <?php if (!preg_match('/^plan_\d+$/', $key))
                                        continue; ?>
                                    <div class="plan-card">
                                        <h4><?php echo esc_html($plan['title']); ?></h4>
                                        <p><?php echo esc_html($plan['desc']); ?></p>
                                        <p><strong><?php echo esc_html($plan['price']); ?></strong></p>
                                        <?php if (!empty($plan['url'])): ?>
                                            <p><a href="<?php echo esc_url($plan['url']); ?>" class="btn">Acheter</a></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php
                        } elseif ($type === 'simple_withsub') {
                            ?>
                            <div class="pricing-box">
                                <h3>Abonnement Mensuel</h3>
                                <p><?php echo esc_html($plans['simple_monthly']['desc']); ?></p>
                                <p><strong><?php echo esc_html($plans['simple_monthly']['price']); ?></strong></p>
                                <?php if (!empty($plans['simple_monthly']['url'])): ?>
                                    <p><a href="<?php echo esc_url($plans['simple_monthly']['url']); ?>" class="btn">Souscrire</a></p>
                                <?php endif; ?>
    
                                <h3>Abonnement Annuel</h3>
                                <p><?php echo esc_html($plans['simple_yearly']['desc']); ?></p>
                                <p><strong><?php echo esc_html($plans['simple_yearly']['price']); ?></strong></p>
                                <?php if (!empty($plans['simple_yearly']['url'])): ?>
                                    <p><a href="<?php echo esc_url($plans['simple_yearly']['url']); ?>" class="btn">Souscrire</a></p>
                                <?php endif; ?>
                            </div>
                            <?php
                        } elseif ($type === 'multi_withsub') {
                            ?>
                            <div class="pricing-box">
    
                                <div class="pricing-toggle">
                                    <button id="monthly-btn" class="toggle-btn active">Monthly</button>
                                    <button id="yearly-btn" class="toggle-btn">Yearly</button>
                                </div>
                                <div id="vc-current-plan-price" class="vc-current-plan-price">--</div>
    
    
                                <div id="monthly-content" class="content active">
                                    <?php foreach ($plans['monthly'] ?? [] as $i => $plan): ?>
                                        <div class="radio-group">
                                            <label class="radio-label<?php echo $i === 0 ? ' active' : ''; ?>">
                                                <div class="container-input">
                                                    <input type="radio" name="vc_pricing_monthly" <?php echo $i === 0 ? 'checked' : ''; ?>>
                                                    <span class="title-plan"><?php echo esc_html($plan['title']); ?></span>
                                                    <span class="price"><?php echo esc_html($plan['price']); ?></span>
                                                </div>
                                                <div class="radio-content"
                                                    style="display: <?php echo $i === 0 ? 'block' : 'none'; ?>;">
                                                    <div><?php echo esc_html($plan['desc']); ?></div>
                                                    <?php if (!empty($plan['url'])): ?>
                                                        <a href="<?php echo esc_url($plan['url']); ?>"><button class="btn-starter">Add to
                                                                cart</button></a>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
    
                                <div id="yearly-content" class="content">
                                    <?php foreach ($plans['yearly'] ?? [] as $i => $plan): ?>
                                        <div class="radio-group">
                                            <label class="radio-label<?php echo $i === 0 ? ' active' : ''; ?>">
                                                <div class="container-input">
                                                    <input type="radio" name="vc_pricing_yearly" <?php echo $i === 0 ? 'checked' : ''; ?>>
                                                    <span class="title-plan"><?php echo esc_html($plan['title']); ?></span>
                                                    <span class="price"><?php echo esc_html($plan['price']); ?></span>
                                                </div>
                                                <div class="radio-content"
                                                    style="display: <?php echo $i === 0 ? 'block' : 'none'; ?>;">
                                                    <div><?php echo esc_html($plan['desc']); ?></div>
                                                    <?php if (!empty($plan['url'])): ?>
                                                        <a href="<?php echo esc_url($plan['url']); ?>"><button class="btn-starter">Add to
                                                                cart</button></a>
                                                    <?php endif; ?>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
    
    
                            </div>
                            <?php
                        }
                        ?>
                        <div class="container-doc-free">
                            <div>
                                <?php
                                $vc_url_free = get_post_meta(get_the_ID(), 'vc_url_free', true);
                                if (!empty($vc_url_free)) :
                                ?>
                                    <a href="<?php echo esc_url($vc_url_free); ?>" target="_blank">
                                        <button class="btn-vc-free-link">Download Free</button>
                                    </a>
                                <?php
                                endif;?>
                            </div>
                            <div>
                                <?php
                                $vc_url_doc = get_post_meta(get_the_ID(), 'vc_url_doc', true);
                                if (!empty($vc_url_doc)) :
                                ?>
                                    <a href="<?php echo esc_url($vc_url_doc); ?>" target="_blank">
                                        <button class="btn-vc-url-doc">Documentation</button>
                                    </a>
                                <?php
                                endif;?>
                            </div>
                        </div>
                    </div>
                    <div class="" style="padding-top: 30px;">
                        <div class="">
                            <?php
                            $vc_url_live = get_post_meta(get_the_ID(), 'vc_url_live', true);
                            $vc_desc_live = get_post_meta(get_the_ID(), 'vc_desc_live', true);
    
                            if (!empty($vc_url_live)) :
                            ?>
                                <?php if (!empty($vc_desc_live)) : ?>
                                    <span class="vc-live-desc"><?php echo esc_html($vc_desc_live); ?></span>
                                <?php endif; ?>
                                <a href="<?php echo esc_url($vc_url_live); ?>" target="_blank">
                                    <button class="btn-vc-live-demo">Live Demo</button>
                                </a>
    
                            <?php endif; ?>
                        </div>
    
                        <div style="padding-top: 20px;">
                            <?php
                            $vc_url_admin = get_post_meta(get_the_ID(), 'vc_url_admin', true);
                            if (!empty($vc_url_admin)) :
                            ?>
                                <a href="<?php echo esc_url($vc_url_admin); ?>" target="_blank">
                                    <button class="btn-vc-url-admin">Admin Demo</button>
                                </a>
                            <?php
                            endif;?>
                        </div>
                    </div>
                </div>
            </div>
    
            
            <!--<div class="vc-content">
                <?php the_content(); ?>
            </div>-->

        </div>

        <div id="bar-content" class="vc-flex">
            <!--cette div a enlevé après-->
            <div class="vc-max-container">
                <div class="vc-flex container-bar">
                    <div class="vc-menus vc-flex">
                        <div>
                            <a href="#container-features">
                                <button class="vc-features">Main overview</button>
                            </a>
                        </div>
                        <div>
                            <a href="#vc-reviews">
                                <button class="vc-reviews">Review</button>
                            </a>
                        </div>
                        <div>
                            <a href="#vc-faq">
                                <button class="vc-faq">Faq</button>
                            </a>
                        </div>
                        <?php
                        $vc_url_feature = get_post_meta(get_the_ID(), 'vc_url_feature', true);
                        $vc_url_changelog = get_post_meta(get_the_ID(), 'vc_url_changelog', true);
                        ?>

                        <div>
                            <?php if (!empty($vc_url_feature)): ?>
                                <a href="<?php echo esc_url($vc_url_feature); ?>" class="btn vc-feature-request" target="_blank">Features request</a>
                            <?php endif; ?>
                        </div>

                        <div>
                            <?php if (!empty($vc_url_changelog)): ?>
                                <a href="<?php echo esc_url($vc_url_changelog); ?>" class="btn vc-changelog" target="_blank">Changelog</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div id="vc-current-plan-bar" class="vc-pricing-bar" style="display: none;">
                        <div class="container-vc-pricing-bar">
                            <div>
                                <span id="vc-current-plan-title"></span>
                                <span class="vc-current-plan-price price-bar"></span>/
                                <span id="vc-current-plan-type"></span>
                            </div>
                            <div>
                                <a id="vc-current-plan-url" class="btn">Add to cart</a>
                            </div>
                        </div>
                    </div>

                    <div>
    
                    </div>

                </div>
            </div>
        </div>
        <div id="bar-placeholder" style="height: 0;"></div>
        <div id="scrollable-content" class="vc-max-container" style="padding-top: 150px; padding-bottom:100px;">
            <div class="" id="container-features">
                <?php
                $features = get_post_meta(get_the_ID(), 'vc_features', true);
                if (!empty($features) && is_array($features)) :
                    foreach ($features as $index => $feature) :
                        $img_url = esc_url($feature['image'] ?? '');
                        $desc    = wp_kses_post($feature['desc'] ?? '');
                        $title   = sanitize_text_field($feature['title'] ?? '');
                        $button  = esc_url_raw($feature['button'] ?? '');

                        // Exemple : on alterne automatiquement entre 'left', 'right', 'stacked'
                        $style = $feature['style'] ?? (($index % 3 === 0) ? 'left' : (($index % 3 === 1) ? 'right' : 'stacked'));
                        $block_class = 'vc-feature-block vc-style-' . esc_attr($style);
                ?>
                    <div class="<?php echo $block_class; ?>">
                        <?php if ($img_url): ?>
                            <div class="vc-feature-image">
                                <img src="<?php echo $img_url; ?>" alt="Feature image" class="vc-feature-img">
                            </div>
                        <?php endif; ?>

                        <div class="vc-block-title-desc">
                            <?php if ($title): ?>
                                <div class="vc-feature-title"><?php echo $title; ?></div>
                            <?php endif; ?>

                            <?php if ($desc): ?>
                                <div class="vc-feature-desc"><?php echo $desc; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($button)): ?>
                                <a href="<?php echo esc_url($button); ?>" class="btn vc-btn-feature" target="_blank">See more</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                    endforeach;
                endif;
                ?>
   
            </div>
            <div class="" id="vc-reviews">
                <div class="vc-reviews-section">
                    <h2 class="title-section-reviews">Reviews</h2>
                    <div class="block-review">
                        <!-- Formulaire ici -->
                        <div id="vc_reviews_form">
                            <div class="message-connexion">
                                <?php if (is_user_logged_in()) : 
                                    $current_user = wp_get_current_user();
                                    $logout_url = wp_logout_url(get_permalink());
                                    $profile_url = admin_url('profile.php');
                                ?>
                                    <p class="logged-in-as" style="margin-bottom: 5px;">
                                        Logged in as <?php echo esc_html($current_user->display_name); ?>.
                                        <span class="link-con"><a href="<?php echo esc_url($profile_url); ?>">Edit your profile</a></span>.
                                        <span class="link-con"><a href="<?php echo esc_url($logout_url); ?>">Log out?</a></span>
                                    </p>
                                    <p class="required-field-message">
                                        Required fields are marked <span class="required">*</span>
                                    </p>
                            </div>
                            <form id="vc_add_review_form" method="post" style="margin-top: 20px;">
                                <div class="vc-review-field" style="margin-bottom: 15px; display: flex;gap: 15px;align-items: center;">
                                    <label style="display: block;">Your rating</label>
                                    <div class="vc-stars left-star" style="font-size: 26px; color: #ccc;">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <span class="star" data-value="<?php echo $i; ?>" data-index="<?php echo $i; ?>" style="cursor:pointer;">&#9733;</span>
                                        <?php endfor; ?>
                                        <input type="hidden" name="rating" id="rating" required>
                                    </div>
                                </div>

                                <div class="vc-review-field" style="margin-bottom: 15px;">
                                    <textarea name="comment" placeholder="Your review" rows="3" required class="textarea-review"></textarea>
                                </div>

                                <div class="vc-review-field">
                                    <button type="submit" class="vc-submit-review" >
                                        Submit
                                    </button>
                                </div>
                            </form>

                            <script>
                                
                            </script>
                        </div>
                        <?php else : ?>
                            <p class="logged-in-as">
                                Vous devez <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">vous connecter</a> pour ajouter un commentaire.
                            </p>
                        <?php endif; ?>

    
                        <div id="vc_reviews_display">
                            
                        </div>

                    </div>
                </div>
            </div>
            <div class="" id="vc-faq">
                <div class="vc-faq-section">
                    <h2 class="title-section-faq">Questions & Answers</h2>
                    <div class="container-faq">
                        <?php
                        $faqs = get_post_meta(get_the_ID(), 'vc_faqs', true);
                        if (!empty($faqs) && is_array($faqs)) :
                            foreach ($faqs as $index => $faq) :
                                $title   = sanitize_text_field($faq['title'] ?? '');
                                $desc    = wp_kses_post($faq['desc'] ?? '');
                                $button  = esc_url_raw($faq['button'] ?? '');
                        ?>
                            <div class="vc-faq-item">
                                <?php if ($title): ?>
                                    <div class="vc-faq-title" data-index="<?php echo $index; ?>">
                                        <?php echo $title; ?>
                                        <span class="vc-faq-toggle">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
                                                <path d="M480-344 240-584l56-56 184 184 184-184 56 56-240 240Z"/>
                                            </svg>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div class="vc-faq-content">
                                    <?php if ($desc): ?>
                                        <div class="vc-faq-desc"><?php echo $desc; ?></div>
                                    <?php endif; ?>

                                    <?php if (!empty($button)): ?>
                                        <a href="<?php echo esc_url($button); ?>" class="btn vc-btn-faq" target="_blank">See more</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>

            </div>
        </div>

    <?php endwhile;
endif;

get_footer();