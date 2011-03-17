<?php

###	Short Codes
add_shortcode('topspin_buy_buttons','topspin_shortcode_buy_buttons');
add_shortcode('topspin_featured_item','topspin_shortcode_featured_item');

###	[topspin_buy_buttons]
###		@id		Default: the current store's ID
function topspin_shortcode_buy_buttons($atts) {
	##	Outputs the current store grid
	global $store;
	global $post;
	$defaults = array(
		'id' => (isset($atts['id'])) ? $atts['id'] : $store->getStoreId($post->ID)
	);
	$a = shortcode_atts($defaults,$atts);
	$storeID = $a['id'];
	$storedata = $store->getStore($storeID);
	$storedata['grid_item_width'] = floor(100/$storedata['grid_columns']);
	## Set Page
	$page = (isset($_GET['page'])) ? $_GET['page'] : 1;
	## Get Items
	$allitems = $store->getStoreItems($storeID,0);
	## Get Paged Items
	$storeitems = ($storedata['show_all_items']) ? $allitems : $store->getStoreItemsPage($allitems,$storedata['items_per_page'],$page);
	## Set Additional Store Data
	if($storedata['show_all_items']) {
		$storedata['total_items'] = count($allitems);
		$storedata['total_pages'] = 1;
		$storedata['curr_page'] = $page;
		$storedata['prev_page'] = '';
		$storedata['next_page'] = '';
	}
	else {
		$storedata['total_items'] = count($allitems);
		$storedata['total_pages'] = ceil($storedata['total_items']/$storedata['items_per_page']);
		$storedata['curr_page'] = $page;
		$storedata['prev_page'] = ($page==1) ? '' : get_permalink($post->ID).'?page='.($page-1);
		$storedata['next_page'] = ($storedata['curr_page']<$storedata['total_pages']) ? get_permalink($post->ID).'?page='.($page+1) : '';
	}
	ob_start();
	## Template File
		## TEMPLATE_PATH.'/topspin-templates/item-listings.php
	$templatefile = 'templates/item-listings.php';
	if(file_exists(TEMPLATEPATH.'/topspin-templates/item-listings.php')) { $templatefile = TEMPLATEPATH.'/topspin-templates/item-listings.php'; }
	include($templatefile);
	$html = ob_get_contents();
	ob_end_clean();
	echo $html;
}

### [topspin_featured_item]
###		@id		Default: the current store's ID
function topspin_shortcode_featured_item($atts) {
	##	Outputs the current store's featured item
	global $store;
	global $post;
	$defaults = array(
		'id' => (isset($atts['id'])) ? $atts['id'] : $store->getStoreId($post->ID)
	);
	$a = shortcode_atts($defaults,$atts);
	$storeID = $a['id'];
	$featureditem = $store->getStoreFeaturedItem($storeID);
	if($featureditem) {
		ob_start();
		## Template File
			## TEMPLATE_PATH.'/topspin-templates/featured-item.php
		$templatefile = 'templates/featured-item.php';
		if(file_exists(TEMPLATEPATH.'/topspin-templates/featured-item.php')) { $templatefile = TEMPLATEPATH.'/topspin-templates/featured-item.php'; }
		include($templatefile);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	}
}

?>