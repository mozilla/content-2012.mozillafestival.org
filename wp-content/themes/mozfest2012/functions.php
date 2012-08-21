<?php

function mf2012_empty_function () {
	// Do nothing
}

// Add support for custom headers.
$custom_header_support = array(
	// The default header text color.
	'default-text-color' => '000',
	// Turn off header text
	'header-test' => false,
	// The height and width of our custom header.
	'width' => apply_filters('mf2012_header_image_width', 1600),
	'height' => apply_filters('mf2012_header_image_height', 500),
	// Support flexible heights.
	'flex-height' => true,
	// Support flexible widths
	'flex-width' => true,
	// Random image rotation by default.
	'random-default' => true,
	// Callback for styling the header.
	'wp-head-callback' => 'mf2012_header_style',
	'admin-head-callback'    => 'mf2012_empty_function',
	'admin-preview-callback' => 'mf2012_empty_function',
);

function mf2012_header_style () {
	if (is_front_page()) {
		$header_image = get_header_image();
		if ($header_image) {
			if (function_exists('get_custom_header')) {
				$header = get_custom_header();
				$header_image_width  = $header->width;
				$header_image_height = $header->height;
				$header_text = get_post($header->attachment_id)->post_title;
				$header_color = get_header_textcolor();
			} else {
				$header_text = '';
				$header_color = '';
			}

			if ($header_color == 'blank') {
				$header_color = '';
			} else {
				if (strlen($header_color) == 6) {
					$r = hexdec(substr($header_color, 0, 2));
					$g = hexdec(substr($header_color, 2, 2));
					$b = hexdec(substr($header_color, 4, 2));
				} else {
					$r = hexdec(substr($header_color, 0, 1));
					$g = hexdec(substr($header_color, 1, 1));
					$b = hexdec(substr($header_color, 2, 1));
				}
				$header_rgb = array($r, $g, $b);
			}
			?>
			<style>
			@media screen and (min-width: 840px) {
				#carousel {
					background-image: url(<?php echo $header_image; ?>);
					<?php if (!empty($header_color)): ?>background-color: #<?php echo $header_color; ?>;
				<?php endif; ?>}
				#carousel .constrained {
					height: <?php echo $header_image_height; ?>px;
				}
				<?php if (!empty($header_color) && !empty($header_text)): ?>#carousel .constrained::after {
					content: "<?php echo $header_text; ?>";
					color: #<?php echo $header_color; ?>;
					color: rgba(<?php echo implode(', ', $header_rgb); ?>, 0.75);
				}
			<?php endif; ?>}
			</style>
			<?php
		}
	}
}

add_theme_support('custom-header', $custom_header_support);

if (!function_exists('get_custom_header')) {
	// This is all for compatibility with versions of WordPress prior to 3.4.
	define('HEADER_TEXTCOLOR', $custom_header_support['default-text-color']);
	define('HEADER_IMAGE', '');
	define('HEADER_IMAGE_WIDTH', $custom_header_support['width']);
	define('HEADER_IMAGE_HEIGHT', $custom_header_support['height']);
	add_custom_image_header(
		$custom_header_support['wp-head-callback'],
		$custom_header_support['admin-head-callback'],
		$custom_header_support['admin-preview-callback']
	);
	add_custom_background();
}

function mf2012_register_sidebars () {
	register_sidebar(array(
		'id' => 'default',
		'name' => __('Default'),
		'description' => __('The default sidebar used on standard pages'),
		'before_widget' => "\t<section id=\"%1\$s\" class=\"widget %2\$s\">\n",
		'after_widget' => "\n\t</section>\n",
		'before_title' => "\n\t\t<h2>",
		'after_title' => "</h2>\n",
	));
}

add_action('widgets_init', 'mf2012_register_sidebars');

function mf2012_register_menus () {
	register_nav_menus(array(
		'header' => __('Main Navigation'),
		'footer' => __('Learn More'),
		'contact' => __('Stay In Touch'),
		'previously' => __('Previous Years'),
	));
}

add_action('init', 'mf2012_register_menus');

function mf2012_allow_html ($str) {
	return html_entity_decode($str);
}

function mf2012_strip_html ($str) {
	return strip_tags(mf2012_allow_html($str));
}

add_filter('bloginfo', 'mf2012_allow_html');

/**
 * Custom post types
 */

function mf2012_create_post_types() {
	register_post_type('mozfest_session', array(
		'labels' => array(
			'name' => __('Sessions'),
			'singular_name' => __('Session')
		),
		'public' => true,
		'has_archive' => true,
	));
}

add_action('init', 'mf2012_create_post_types');

/**
 * Utility Functions
 */

function mf2012_menu_meta ($location) {
	static $locations;
	if (!$locations) $locations = get_nav_menu_locations();

	if (!isset($locations[$location])) return (object) array('count' => 0);

	if (is_int($locations[$location])) {
		if ($menu = wp_get_nav_menu_object($locations[$location])) {
			$menu->id = $locations[$location];
			$menu->count = count(wp_get_nav_menu_items($menu->term_id));
		} else {
			$menu = (object) array('count' => 0);
		}
		$locations[$location] = $menu;
	}

	return $locations[$location];
}

function mf2012_menu ($location, $args=Null) {
	$meta = mf2012_menu_meta($location);

	if (!$meta->count) return '';

	// print '<pre>'.print_r(wp_get_nav_menu_items($meta->term_id),1).'</pre>';

	// print_r($meta);

	$args = (array) $args;

	if (!isset($args['container_id']))
		$args['container_id'] = sanitize_title($location) . '-menu';

	if (!isset($args['container']))
		$args['container'] = 'nav';

	if (!isset($args['depth']))
		$args['depth'] = 1;

	if (!isset($args['items_wrap']))
		$args['items_wrap'] = '<h4>' . $meta->name . '</h4><ul id="%1$s" class="%2$s">%3$s</ul>';

	$args['theme_location'] = $location;
	$args['echo'] = false;
	$args['menu_class'] = 'menu menu-count-' . $meta->count;

	return wp_nav_menu($args);
}
