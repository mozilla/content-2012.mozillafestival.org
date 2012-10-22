<?php

get_header();

?>
<div id="content">
	<div class="constrained">
		<div id="content-inner">
<?php

if (have_posts()) {
	global $wp_query;
	$term = $wp_query->get_queried_object();
	$taxonomy = get_taxonomy($term->taxonomy);
	// echo '<pre>'.print_r($term,1).'</pre>';
	echo '<h2><a href="'.site_url($taxonomy->rewrite['slug']).'/">'.$taxonomy->label.'</a> &raquo; '.$term->name.'</h2>';
	if ($term->description) {
		echo '<div class="intro">';
		echo apply_filters('the_content', $term->description);
		echo '</div>';
		// echo '<p class="intro">'.nl2br($term->description).'</p>';
	}
	while (have_posts()) {
		the_post();
		$type = get_post_format();
		if (empty($type)) $type = get_post_type();
		get_template_part('content', $type);
	}
} else if (defined('TAXONOMY_ARCHIVE')) {
	get_template_part('taxonomy-list', TAXONOMY_ARCHIVE);
}

	get_template_part('taxonomy-more');

?>
		</div>
<?php

get_sidebar();

?>
	</div>
</div>
<?php

get_footer();
