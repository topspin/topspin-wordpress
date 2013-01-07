<?php

$spinTags = wp_get_post_terms(ts_get_the_ID(), 'spin-tags');

if(count($spinTags)) {
	echo '<ul>';
	foreach($spinTags as $tag) {
		echo sprintf('<li>%s</li>', $tag->name);
	}
	echo '</ul>';
}
else {
	echo '<p>There are no Spin Tags for this offer.</p>';
}


?>