<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if (is_single()): ?>
		<h2><?php the_title(); ?></h2>
	<?php elseif (!is_page()): ?>
		<h3><?php the_title(); ?></h3>
	<?php endif; ?>
	<?php the_content(); ?>
</article>
