<?php

get_header();

?>
<div id="content">
	<div class="constrained">
		<div id="content-inner">
<?php

if (have_posts()) {
	while (have_posts()) {
		the_post();
		$type = get_post_format();
		if (empty($type)) $type = get_post_type();
		get_template_part('content', $type);
	}
} else {
	get_template_part('content', 'missing');
}

?>
			<aside class="more">
				<h2>View Sessions by Type</h2>
				<p class="intro">Learn more about session formats</p>
				<?php get_template_part('taxonomy-more-inner'); ?>
			</aside>

<?php
query_posts(array(
	'post_type' => 'session',
	'meta_key' => 'start',
	'orderby' => 'meta_value',
	'order' => 'ASC',
	'posts_per_page' => -1,
));

if (have_posts()) {
?>
			<section class="session-list">
				<h2>All Sessions</h2>
<?php
	while (have_posts()) {
		the_post();
		$type = get_post_format();
		if (empty($type)) $type = get_post_type();
		get_template_part('content', $type);
	}
}

wp_reset_query();

?>
			</section>
		</div>
<?php

get_sidebar();

?>
	</div>
</div>
<?php

get_footer();
