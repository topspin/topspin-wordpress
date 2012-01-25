<?php
/*
 *	Last Modified:		July 26, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
 *	2011-07-26
 		- File created
 		- New function topspin_get_item_photos()
 		- New function topspin_get_item()
 */

function topspin_get_item_photos($item_id) {
	/*
	 *	Retrieves all the images of the specified item
	 *
	 *	PARAMETERS
	 *		@item_id (int)		The item's ID
	 *
	 *	RETURNS
	 *		A multi-dimensional array of the item's photos
	 */
	global $store;
	$images = $store->getItemImages($item_id);
	return $images;
}

function topspin_get_item($item_id) {
	/*
	 *	Retrieves all the information of the specified item
	 *
	 *	PARAMETERS
	 *		@item_id (int)		The item's ID
	 *
	 *	RETURNS
	 *		An array of the item's data
	 */
	global $store;
	$item = $store->getItem($item_id);
	$item['campaign'] = unserialize($item['campaign']);
	return $item;
}

function topspin_get_store_items($store_id) {
	/*
	 *	Retrieves the item list for the specified store
	 *
	 *	PARAMETERS
	 *		@store_id (int)		The store ID
	 *
	 *	RETURNS
	 *		An array of the store's items
	 */
	global $store;
	return $store->getStoreItems($store_id);
}

function topspin_get_most_popular_items($limit=null) {
	/*
	 *	Retrieves the most popular items
	 *
	 *	PARAMETERS
	 *		@limit (int)		How many items to return
	 *
	 *	RETURNS
	 *		An array of the most popular items
	 */
	global $store;
	return $store->product_get_most_popular_list($limit);
}

function topspin_get_nav_menu($echo=true) {
	/*
	 *	Retrieves the nav menu
	 *
	 *	PARAMETERS
	 *		@echo (bool)		Echo the menu out or just return the HTML
	 */
	if($echo) { echo topspin_shortcode_store_nav_menu(0); }
	else { return topspin_shortcode_store_nav_menu(0); }
}

?>