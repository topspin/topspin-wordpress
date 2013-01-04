<?php
global $tsQuery;
$args = array(
	'post_ID' => $post->ID,
	'show_all_items' => 1
);
$tsQuery = new TS_Query($args);
?>

<ul id="topspin-preview-grid">
	<?php include(TOPSPIN_PLUGIN_PATH . '/views/metabox/store/preview/grid.php'); ?>
</ul>
<div class="clear"></div>