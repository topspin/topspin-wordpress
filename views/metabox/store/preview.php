<?php
global $tsQuery;
$args = array(
	'post_ID' => $post->ID,
	'show_all_items' => 1
);
$tsQuery = new TS_Query($args);
?>

<span class="description">If Manual sorting is enabled, you can click-and-drag the offers to manually sort the orders. You can also toggle the visiblity of each offer manually by clicking on the each offer's thumbnail. Semi-transparent offers will be hidden from the public view.</span>

<ul id="topspin-preview-grid">
	<?php include(TOPSPIN_PLUGIN_PATH . '/views/metabox/store/preview/grid.php'); ?>
</ul>
<div class="clear"></div>