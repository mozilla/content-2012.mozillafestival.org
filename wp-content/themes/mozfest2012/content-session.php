<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
	<?php if (is_single()): ?>
		<h2><?php the_title(); ?></h2>
	<?php else: ?>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php endif; ?>
	<?php
		$header = array();
		$start = get_post_meta(get_the_ID(), 'start', true);
		$end = get_post_meta(get_the_ID(), 'end', true);
		if ($start && $end) {
			$header[] = date('D. H:i - ', strtotime($start)).date('H:i', strtotime($end));
		}
		foreach (array('theme', 'format') as $type) {
			foreach ((array) get_the_terms(get_the_ID(), $type) as $term) {
				if ($term) {
					$link = get_term_link($term, $type);
					$header[] = '<a href="' . $link . '">' . $term->name . '</a>';
				}
			}
		}
		if (!empty($header)) {
			echo '<p class="meta">' . implode(' | ', $header) . '</p>';
		}
	?>
	</header>
	<div class="description">
		<?php the_content(); ?>
	</div>
	<?php
		if (is_single()) {
			$meta = array(
				'up-to-date' => (object) array(
					'label' => __('Stay up to date'),
					'items' => array(),
				),
				'more' => (object) array(
					'label' => __('More about this session'),
					'items' => array(),
				)
			);

			$taxonomy = 'location';
			$location_list = array();
			$locations = get_the_terms(get_the_ID(), $taxonomy);
			foreach ($locations as $location) {
				$location_list[] = $location;
				while ($location && $location->parent) {
					$location = array_pop(get_terms($taxonomy, array('include'=>array($location->parent), 'hide_empty'=>0)));
					$location_list[] = $location;
				}
			}

			$location_output = array();
			while (count($location_list)) {
				$location = array_pop($location_list);
				$location_output[] = '<a href="' . get_term_link($location, $taxonomy) . '">' . $location->name . '</a>';
			}
			if (count($location_output)) {
				$meta['up-to-date']->items[] = '<p>Location: ' . implode(' / ', $location_output) . '</p>';
			}

			$organizers = get_the_terms(get_the_ID(), 'organizer');
			$organizer_output = array();
			if (!empty($organizers)) {
				foreach ($organizers as $organizer) {
					@$index ++;
					$user = get_organizer_user($organizer);
					$link = get_term_link($organizer, 'organizer');
					if ($user) {
						$user_meta = array('<a href="' . $link . '" class="url fn">' . $user->display_name . ' ' . get_avatar($user->ID, 120) . '</a>');
						if ($twitter = get_the_author_meta('twitter', $user->ID)) {
							$user_meta[] = '<a href="https://twitter.com/' . $twitter . '">@' . $twitter . '</a>';
						}
						if ($website = get_the_author_meta('user_url', $user->ID)) {
							$website_display = parse_url($website, PHP_URL_HOST);
							$user_meta[] = '<a href="' . $website . '">' . $website_display . '</a>';
						}
						$organizer_output[] = '<li class="vcard organizer-' . $index . '">' . implode(' | ', $user_meta) . '</li>';
					} else {
						$organizer_output[] = '<li class="vcard organizer-' . $index . '"><a href="' . $link . '" class="url fn">' . $organizer->name . ' '  . get_avatar(0, 120) . '</a></li>';
					}
				}
				$meta['up-to-date']->items[] = '<p>Organized by:</p><ul class="organizers">' . implode('', $organizer_output) . '</ul>';
			}

			$external_options = array(
				'lanyrd.com' => __('Lanyrd page'),
				'flickr.com' => __('Flickr photos'),
			);
			$external_links = array();

			foreach ($external_options as $key => $label) {
				$link = get_post_meta(get_the_ID(), $key, true);
				if ($link) {
					$external_links[] = '<a href="' . $link . '">' . $label . '</a>';
				}
			}

			if (count($external_links)) {
				$meta['more']->items[] = '<p class="external">' . implode(' &nbsp;&middot;&nbsp; ', $external_links) . '</p>';
			}

			foreach ($meta as $section => $info) {
				if (count($info->items)) {
					echo '<section class="' . $section . '">';
					echo '<h3>' . $info->label . '</h3>';
					foreach ($info->items as $item) {
						echo $item;
					}
					echo '</section>';
				}
			}		
		?>
	<aside class="share">
		<h3>Share this session</h3>
	</aside>
	<?php } ?>
</article>
<?php
	if (is_single()) get_template_part('taxonomy-more');