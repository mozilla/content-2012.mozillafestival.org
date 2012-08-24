<?php

get_header();

if (function_exists('get_custom_header')) {
	$header = get_custom_header();
	$header_text = get_post($header->attachment_id)->post_title;
	$header_color = get_header_textcolor();
	if (!empty($header_text) && $header_color !== 'blank') {
		echo '<div id="carousel"><div class="constrained"></div></div>'."\n";
	}
}

?>
<div id="content">
	<div class="constrained">
		<div id="content-inner">
<?php

if (have_posts()) {
	while (have_posts()) {
		the_post();
		get_template_part('content', get_post_format());
	}
} else {
	get_template_part('content', 'missing');
}

?>
		</div>
<?php

get_sidebar();

?>
	</div>
</div>
<?php

get_footer();
