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
