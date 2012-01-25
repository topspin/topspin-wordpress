<?php
/*
 *	Last Modified:		January 24, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
 *	2012-01-24
 		- Added nav menu shortcode @eThan
 *	2011-09-19
 		- Fixed shortcode content positioning - [@bryanlanders - https://github.com/topspin/topspin-wordpress/issues/19]
 *	2011-08-01
 		- Updated topspin_shortcode_featured_item() to display multiple featured images
 		- Fixed the pagination to work with and without permalinks
 		- Added new short code [topspin_store_item]
 *	2011-04-05
 *		- updated topspin_shortcode_buy_buttons()
 			update template file path orders for new template modes:
 			3.1:		<current-theme>/topspin-mode, <parent-theme>/topspin-mode
 			3.0.0:		<current-theme>/topspin-template, <parent-theme>/topspin-template
 			Default:	<plugin-dir>/topspin-mode
 *		- update topspin_shortcode_featured_item()
 			update template file path orders for new template modes: (see top)
 */

###	Short Codes
add_shortcode('topspin_store_item','topspin_shortcode_store_item');
add_shortcode('topspin_buy_buttons','topspin_shortcode_buy_buttons');
add_shortcode('topspin_featured_item','topspin_shortcode_featured_item');
add_shortcode('topspin_store_nav_menu','topspin_shortcode_store_nav_menu');

### [topspin_store_item]
###		@id		The item ID
function topspin_shortcode_store_item($atts) {
	global $store;
	$defaults = array('id'=>0);
	$a = shortcode_atts($defaults,$atts);
	if($a['id']) {
		$featureditem = topspin_get_item($a['id']);
		if($featureditem) {
			ob_start();
			##	Template File
			$templateMode = $store->getSetting('topspin_template_mode');
			$templatefile = 'templates/topspin-'.$templateMode.'/featured-item.php';
			##	3.1
			if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/featured-item.php'; }
			elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-'.$templateMode.'/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-'.$templateMode.'/featured-item.php'; }
			##	3.0.0
			if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/featured-item.php'; }
			elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/featured-item.php'; }
			include($templatefile);
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
}

###	[topspin_buy_buttons]
###		@id		Default: the current store's ID
function topspin_shortcode_buy_buttons($atts) {
	##	Outputs the current store grid
	global $store;
	global $post;
	//Query string append sign
	$permalinkEnabled = (get_option('permalink_structure')!='') ? true : false;
	$queryAppendSign = ($permalinkEnabled) ? '?' : '&';
	
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
		$storedata['prev_page'] = ($page==1) ? '' : get_permalink($post->ID).$queryAppendSign.'page='.($page-1);
		$storedata['next_page'] = ($storedata['curr_page']<$storedata['total_pages']) ? get_permalink($post->ID).$queryAppendSign.'page='.($page+1) : '';
	}
	ob_start();
	##	Template File
	$templateMode = $store->getSetting('topspin_template_mode');
	$templatefile = 'templates/topspin-'.$templateMode.'/item-listings.php';
	##	3.1
	if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/item-listings.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/item-listings.php'; }
	elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-'.$templateMode.'/item-listings.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-'.$templateMode.'/item-listings.php'; }
	##	3.0.0
	if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/item-listings.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/item-listings.php'; }
	elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/item-listings.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/item-listings.php'; }
	include($templatefile);
	$html = ob_get_contents();
	ob_end_clean();
	return $html;
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
	$featuredItems = $store->getStoreFeaturedItems($storeID);
	if(count($featuredItems)) {
		ob_start();
		##	Template File
		$templateMode = $store->getSetting('topspin_template_mode');
		$templatefile = 'templates/topspin-'.$templateMode.'/featured-item.php';
		##	3.1
		if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/featured-item.php'; }
		elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-'.$templateMode.'/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-'.$templateMode.'/featured-item.php'; }
		##	3.0.0
		if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/featured-item.php'; }
		elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/featured-item.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/featured-item.php'; }
		$html = '';
		foreach($featuredItems as $featureditem) {
			include($templatefile);
			ob_flush();
			$html .= ob_get_contents();
		}
		ob_end_clean();
		return $html;
	}
}

### [topspin_store_nav_menu]
###		@id		Default: the current store's ID
function topspin_shortcode_store_nav_menu($atts) {
	global $store;
	global $post;
	$defaults = array(
        'id' => (isset($atts['id'])) ? $atts['id'] : $store->getStoreId($post->ID)
	);
	$a = shortcode_atts($defaults,$atts);
	$storeID = $a['id'];
	$storelist = $store->stores_get_nested_list();
	if($store->getSetting('topspin_navmenu') && count($storelist)) {
		ob_start();
		##	Template File
		$templateMode = $store->getSetting('topspin_template_mode');
		$templatefile = 'templates/topspin-'.$templateMode.'/nav-menu.php';
		##	3.1
		if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/nav-menu.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-'.$templateMode.'/nav-menu.php'; }
		elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-'.$templateMode.'/nav-menu.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/nav-menu.php'; }
		##	3.0.0
		if(file_exists(TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/nav-menu.php')) { $templatefile = TOPSPIN_CURRENT_THEME_PATH.'/topspin-templates/nav-menu.php'; }
		elseif(file_exists(TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/nav-menu.php')) { $templatefile = TOPSPIN_CURRENT_THEMEPARENT_PATH.'/topspin-templates/nav-menu.php'; }
		include($templatefile);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}

?>