<?php

require_once(dirname(__FILE__).'/admin/widgets.php');

function mf2012_empty_function () {
	// Do nothing
}

function mf2012_alter_taxonomy_archive_posts ($query) {
	if (!is_admin() && $query->tax_query && count($query->tax_query->queries)) {
		$query->query_vars['meta_key'] = 'start';
		$query->query_vars['orderby'] = 'meta_value';
		$query->query_vars['order'] = 'ASC';

		foreach ($query->tax_query->queries as $i => $q) {
			// echo '<pre>'.print_r($q['terms'],1).'</pre>';
			if ($q['terms'][0] === '__all__') {
				$taxonomy = $q['taxonomy'];
				$query->tax_query->queries[$i] = array(
					'taxonomy' => $taxonomy,
				);
				$query->is_tax = false;
				$query->query_vars['taxonomy'] = $taxonomy;
				// $query->query_vars['term'] = '';
				// $query->query_vars[$taxonomy] = '';
				// $query->query[$taxonomy] = '';
				define('TAXONOMY_ARCHIVE', $taxonomy);
			}
		}
		// echo '<pre>'.print_r($query,1).'</pre>';
		// exit();
	}
}

add_filter('pre_get_posts', 'mf2012_alter_taxonomy_archive_posts');

function mf2012_alter_posts_results ($posts) {
	if (!is_array($posts)) {
		$posts = array();
	}
	return $posts;
}

add_filter('posts_results', 'mf2012_alter_posts_results');

function mf2012_fix_sql ($sql) {
	if (defined('TAXONOMY_ARCHIVE')) {
		$sql = '';
	}
	return $sql;
}

add_filter('posts_request', 'mf2012_fix_sql');

function mf2012_override_404 () {
	global $wp_query;

	if (defined('TAXONOMY_ARCHIVE')) {
		status_header(200);

		$wp_query->queried_object = (object) array(
			'taxonomy' => TAXONOMY_ARCHIVE,
			'slug' => '',
			'term_id' => 0,
			'name' => '',
		);

		$wp_query->post = null;
		$wp_query->found_posts = 0;
		$wp_query->post_count = 0;
		$wp_query->is_404 = false;
		$wp_query->posts = array();
		$wp_query->is_archive = 1;
		$wp_query->is_tax = 1;
		$wp_query->queried_object_id = 0;
		$wp_query->term_id = 0;
	}

	// echo '<pre>'.print_r($wp_query,1).'</pre>';
	// exit();
}

add_filter('template_redirect', 'mf2012_override_404');

function mf2012_body_class ($classes) {
	if (defined('TAXONOMY_ARCHIVE')) {
		$classes[] = 'taxonomy-list';
	}
	return $classes;
}

add_filter('body_class', 'mf2012_body_class');

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

function __mf2012_autolink_callback ($matches) {
	@list($match, $open, $before, $link, $after, $close) = $matches;
	if ($open == '[' && $close == ']') {
		$label = trim($before) . trim($after);
	} else {
		if (preg_match('/\.(jpg|png|gif)$/', $link)) {
			return '<img src="'.$link.'" style="max-width: 100%;">';
		} else {
			$label = preg_replace('|^https?://|', '', $link);
			$label = preg_replace('|/$|', '', $label);
		}
	}

	return '<a href="'.$link.'">'.$label.'</a>';
}

function mf2012_autolink ($str) {
	return preg_replace_callback('|(?:(\[)(.*?))?(https?://[^\]\s]+)(?:(.*?)(\]))?|', '__mf2012_autolink_callback', $str);
}

function mf2012_autop ($str, $br=1) {
	if (get_post_type() === 'session') {
		$str = mf2012_autolink($str);
		$str = preg_replace('|^\s*\*\s*(.*?)\s*$|m', '<li>$1</li>', $str);
		$str = wpautop($str, $br);
		$str = preg_replace('|(</p>\s*)(<li>)|', '$1<ul>$2', $str);
		$str = preg_replace('|(</li>)(\s*<p>)|', '$1</ul>$2', $str);
	}
	return $str;
}

add_filter('the_content', 'mf2012_autop');

remove_filter('the_content', 'wpautop');

function mf2012_nav_classes ($items) {
	$request = site_url($_SERVER["REQUEST_URI"]);
	if (is_archive() || is_single() || is_page()) {
		foreach ($items as $item) {
			if ($item->url !== $request && stripos($request, $item->url) !== false) {
				$item->classes[] = 'current-page-ancestor';
			}
		}
	}
	return $items;
}

add_filter('wp_nav_menu_objects', 'mf2012_nav_classes');

/**
 * Custom post types
 */

function mf2012_generate_taxonomy_labels ($singular, $plural=null) {
	if (is_null($plural)) $plural = $singular . 's';
	$lPlural = strToLower($plural);
	return array(
		'name' => __($plural),
		'singular_name' => __($singular),
		'search_items' => __("Search $plural"),
		'popular_items' => __("Popular $plural"),
		'all_items' => __("All $plural"),
	    'parent_item' => __("Parent $singular"),
	    'parent_item_colon' => __("Parent $singular:"),
		'edit_item' => __("Edit $singular"),
		'update_item' => __("Update $singular"),
		'add_new_item' => __("Add $singular"),
	    'new_item_name' => __("New $singular Name"),
	    'separate_items_with_commas' => __("Separate $lPlural with commas"),
	    'add_or_remove_items' => __("Add or remove $lPlural"),
	    'choose_from_most_used' => __("Choose from the most used $lPlural"),
	    'menu_name' => __($plural),
	);
}

function mf2012_init_sessions () {
	register_post_type('session', array(
		'labels' => array(
			'name' => __('Sessions'),
			'singular_name' => __('Session'),
			'add_new_item' => __('Add New Session'),
			'edit_item' => __('Edit Session'),
			'new_item' => __('New Session'),
			'view_item' => __('View Session'),
			'search_items' => __('Search Sessions'),
			'not_found' => __('No sessions found.'),
			'not_found_in_trash' => __('No sessions found in Trash.'),
			'parent_item_colon' => __('Parent Session'),
		),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 20,
		'supports' => array(
			'title',
			'excerpt',
			'editor',
			'custom-fields',
		),
		'rewrite' => array(
			'slug' => 'schedule/sessions',
			'with_front' => false,
			'pages' => false,
		),
		'taxonomies' => array(
			'location',
			'time',
			'organizer',
			'theme',
			'format',
		),
	));

	register_taxonomy('location', 'session', array(
		'labels' => mf2012_generate_taxonomy_labels('Location'),
		'hierarchical' => true,
	    'rewrite' => array(
			'slug' => 'schedule/locations',
			'with_front' => false,
			'hierarchical' => true,
		),
	));

	register_taxonomy('organizer', 'session', array(
		'labels' => mf2012_generate_taxonomy_labels('Organizer'),
	    'rewrite' => array(
			'slug' => 'schedule/organizers',
			'with_front' => false,
			'hierarchical' => false,
		),
	));

	add_role('organizer', 'Organizer');

	register_taxonomy('theme', 'session', array(
		'labels' => mf2012_generate_taxonomy_labels('Theme'),
	    'rewrite' => array(
			'slug' => 'schedule/themes',
			'with_front' => false,
			'hierarchical' => false,
		),
	));

	register_taxonomy('format', 'session', array(
		'labels' => mf2012_generate_taxonomy_labels('Format'),
	    'rewrite' => array(
			'slug' => 'schedule/formats',
			'with_front' => false,
			'hierarchical' => false,
		),
	));
}

add_action('init', 'mf2012_init_sessions');

function mf2012_fix_rewrite_rules ($rules) {
	foreach ($rules as $re => $location) {
		if (preg_match('#^(schedule/[^/]+/)\((\[\^/\]\+|\.\+\?)\)/\?\$$#', $re, $matches)) {
			$rules[$matches[1] . '?$'] = preg_replace('#^(index\.php\?\w+=).*$#', '$1__all__', $location);
		}
	}
	echo '<pre>'.print_r($rules,1).'</pre>';
	return $rules;
}

add_filter('rewrite_rules_array','mf2012_fix_rewrite_rules');

function mf2012_user_contact_methods ($contact_method) {
	// Add Twitter
	$contact_method['twitter'] = 'Twitter';

	// Remove Yahoo IM
	unset($contact_method['yim']);
	unset($contact_method['aim']);
	unset($contact_method['jabber']);
	return $contact_method;
}

add_filter('user_contactmethods', 'mf2012_user_contact_methods');

function get_organizer_user ($organizer_id) {
	global $wpdb;

	if (is_object($organizer_id)) $organizer_id = $organizer_id->term_id;

	$query = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key='organizer_id' AND meta_value=%d";
	$user_id = $wpdb->get_var($wpdb->prepare($query, $organizer_id));
	if (!$user_id) return null;
	return get_userdata($user_id);
}

function mf2012_map_organizers_to_users ($term_id, $taxonomy_id=null, $taxonomy='organizer') {
	if ($taxonomy == 'organizer') {
		$user = get_organizer_user($term_id);

		$term = get_term($term_id, $taxonomy);
		$username = $term->slug;
		$name = preg_split('/\s+/', $term->name, 2);

		if (is_null($user)) {
			$user_id = username_exists($username);
			if (!$user_id) {
				$user_id = wp_create_user($username, wp_generate_password(20));
				wp_insert_user(array(
					'ID' => $user_id,
					'user_login' => $username,
					'role' => 'organizer',
				));
			}
		} else {
			$user_id = $user->ID;
		}

		wp_insert_user(array(
			'ID' => $user_id,
			'user_login' => $username,
			'first_name' => @$name[0],
			'last_name' => @$name[1],
			'nickname' => @$name[0],
			'display_name' => $term->name,
			'description' => $term->description,
		));

		update_user_meta($user_id, 'organizer_id', $term_id);
	}
}

add_action('created_organizer', 'mf2012_map_organizers_to_users');
add_action('edited_organizer', 'mf2012_map_organizers_to_users');

function mf2012_custom_columns ($column, $post_id) {
	switch ($column) {
		case 'name':
			edit_post_link(get_the_title($post_id), '', '', $post_id);
			break;
		case 'theme':
		case 'format':
		case 'organizer':
		case 'location':
			$terms = wp_get_post_terms($post_id, $column);
			$items = array();
			foreach ($terms as $term)
				$items[] = edit_term_link($term->name, '', '', $term, false);
			if (empty($items))
				$items[] = '<em>Undefined</em>';
			echo implode(', ', $items);
			break;
		case 'time':
			$start = get_post_meta($post_id, 'start', true);
			$end = get_post_meta($post_id, 'end', true);
			if (!empty($start) && !empty($end)) {
				$start = strtotime($start);
				$end = strtotime($end);
				echo date('H:i - ', $start);
				echo date('H:i (D. jS)', $end);
			}
			break;
	}
}

add_action('manage_posts_custom_column', 'mf2012_custom_columns', 10, 2);

function mf2012_session_columns ($cols) {
	$cols = array(
		'cb' => '<input type="checkbox">',
		'title' => __('Title'),
		'organizer' => __('Organizers'),
		'location' => __('Location'),
		'theme' => __('Themes'),
		'format' => __('Format'),
		'time' => __('Time'),
	);
	return $cols;
}

add_filter('manage_session_posts_columns', 'mf2012_session_columns');

// Make these columns sortable
function mf2012_sortable_columns () {
	return array(
		'title' => 'title',
		'time' => 'time',
	);
}

add_filter('manage_edit-session_sortable_columns', 'mf2012_sortable_columns');

function mf2012_column_orderby ($vars) {
	if (isset($vars['orderby'])) {
		switch ($vars['orderby']) {
			case 'time':
				$vars = array_merge($vars, array(
					'meta_key' => 'start',
					'orderby' => 'meta_value',
				));
				break;
		}
	}
 	return $vars;
}

add_filter('request', 'mf2012_column_orderby');

function mf2012_import_session_menu () {
	add_submenu_page('edit.php?post_type=session', 'Import', 'Import', 'manage_options', 'import', 'mf2012_import_session_page');
}

add_action('admin_menu', 'mf2012_import_session_menu');

function mf2012_import_session_page () {
	include(dirname(__FILE__).'/admin/import_sessions.php');
}

function mf2012_include_styles () {
	global $post;

	if (!defined('WP_DEBUG') || !WP_DEBUG) {
		$min = '.min';
	} else {
		$min = '';
	}

	if (is_404()) {
		echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/media/css/404'.$min.'.css">'."\n";
	} else if ($post) {
		foreach (array($post->post_type, $post->post_name) as $name) {
			if ($stylesheet = locate_template('media/css/'.$name.$min.'.css')) {
				$relative_path = substr($stylesheet, strlen(get_template_directory()));
				echo '<link rel="stylesheet" href="' . get_template_directory_uri() . $relative_path . '">'."\n";
			}
		}
	}
}

add_action('wp_head', 'mf2012_include_styles', 100);

function mf2012_include_scripts () {
	global $post;

	if ($post) {
		foreach (array($post->post_type, $post->post_name) as $name) {
			if ($script = locate_template('media/js/'.$name.'.js')) {
				$relative_path = substr($script, strlen(get_template_directory()));
				echo '<script src="' . get_template_directory_uri() . $relative_path . '"></script>'."\n";
			}
		}
	}
}

add_action('wp_footer', 'mf2012_include_scripts', 100);

function mf2012_add_analytics () {
	$enabled = !defined('WP_DEBUG') || !WP_DEBUG;

	if ($enabled && !is_admin()) {
		// Do some analytics stuff here
?>
		<!-- Analytics! -->
<?php
	}
}

add_action('wp_footer', 'mf2012_add_analytics', 100);

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

