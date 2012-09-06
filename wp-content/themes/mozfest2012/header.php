<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php bloginfo('name'); echo (is_home() ? '' : wp_title('&raquo;',false)); ?></title>
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/media/css/core.css">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div id="header">
			<div class="constrained">
				<header>
					<h1><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?><span></span></a></h1>
					<p><?php bloginfo('description'); ?></p>
				</header>
				<a id="jump-to-content" href="#content">Jump to content</a>
				<nav>
					<input type="checkbox" role="presentation" id="show-navigation">
					<ul class="menu menu-count-<?php echo mf2012_menu_meta('header')->count + 1; ?>">
						<li class="register"><a href="https://donate.mozilla.org/page/contribute/mozfest2012-registration" class="button">Register Now</a></li>
						<?php
							if (has_nav_menu('header')) {
								wp_nav_menu(array(
									'theme_location' => 'header',
									'container' => false,
									'items_wrap' => '%3$s',
									'depth' => 1,
								));
							}
						?>
					</ul>
					<label for="show-navigation" title="Toggle Menu"></label>
				</nav>
			</div>
		</div>
