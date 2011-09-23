<?php

/*
 *
 *	Last Modified:			August 12, 2011
 *
 *	--------------------------------------
 *	Change Log
 *	--------------------------------------
 *	2011-08-12
 		- Added order parameter
 */

add_action('wp_ajax_topspin_get_items','topspin_ajax_get_items');

function topspin_ajax_get_items() {
	global $store;
	$offer_types = explode(',',$_GET['offer_types']);
	$tags = explode(',',$_GET['tags']);
	$order = (isset($_GET['order']) && strlen($_GET['order'])) ? $_GET['order'] : null;
	$itemsList = $store->getFilteredItems($offer_types,$tags,null,$order);
	echo json_encode($itemsList);
	die();
}

?>