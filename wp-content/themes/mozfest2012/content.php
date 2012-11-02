<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if (is_single()): ?>
        <h2><?php the_title(); ?></h2>
    <?php elseif (!is_page()): ?>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    <?php endif; ?>
    <?php the_content(); ?>
     <?php if (is_single()): ?>
        <?php the_tags('<p><strong>Tagged:</strong> ', ', ', '</p>'); ?>
    <?php endif; ?>
</article>
