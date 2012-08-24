<?php

$taxonomy = get_taxonomy(TAXONOMY_ARCHIVE);
$terms = get_terms(TAXONOMY_ARCHIVE, array('orderby'=>'term_group'));
// echo '<pre>'.print_r(array($taxonomy, $terms),1).'</pre>';

echo '<h2>' . $taxonomy->labels->name . '</h2>';

foreach ($terms as $term) {
	$child = $term->parent ? ' child' : '';
	echo '<article class="vcard' . $child . '">';
	echo '<h3><a href="' . get_term_link($term) . '" class="fn url">' . $term->name . '</a></h3>';
	echo '<p>' . $term->description . '</p>';
	echo '</article>';
}