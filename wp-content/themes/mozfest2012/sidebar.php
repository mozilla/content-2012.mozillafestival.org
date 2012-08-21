<?php

if (is_active_sidebar('default')) {
	echo "<div id=\"sidebar\">\n<aside>\n";
	dynamic_sidebar('default');
	echo "</aside>\n</div>\n";
}

?>