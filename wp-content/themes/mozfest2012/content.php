<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if (is_single() || is_page()): ?>
		<h2><?php the_title(); ?></h2>
	<?php else: ?>
		<h3><?php the_title(); ?></h3>
	<?php endif; ?>
	<?php the_content(); ?>
</article>
