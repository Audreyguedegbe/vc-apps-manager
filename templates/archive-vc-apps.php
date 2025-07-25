<?php get_header(); ?>

<h1>Nos Applications</h1>

<?php if (have_posts()) : ?>
    <div class="vc-apps-archive">
        <?php while (have_posts()) : the_post(); ?>
            <div class="vc-app-item">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div><?php the_excerpt(); ?></div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else : ?>
    <p>Aucune app trouvée.</p>
<?php endif; ?>

<?php get_footer(); ?>
