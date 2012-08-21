<?php

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
