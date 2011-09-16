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

?>