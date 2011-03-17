<?php

add_action('wp_ajax_topspin_get_items','topspin_ajax_get_items');

function topspin_ajax_get_items() {
	global $store;
	$offer_types = explode(',',$_GET['offer_types']);
	$tags = explode(',',$_GET['tags']);
	$store_id = $_GET['store_id'];
	$itemsList = $store->getFilteredItems($offer_types,$tags);
	echo json_encode($itemsList);
	die();
}

?>