<?php
/*
 *	3.0.4 UPGRADE NOTICE
 *	--------------------
 *	2012-01-26
 		- Updated script to use builtin functions
 */

// Add "last_modified" column into "topspin_items" table
if(!topspin_table_column_exists('topspin_items','last_modified')) {
	global $wpdb;
	$timestamp = time();

	topspin_table_column_add('topspin_items','last_modified','TIMESTAMP');

	// Updates all rows in "topspin_items.last_modified" column with the current timestamp
	$wpdb->update($wpdb->prefix.'topspin_items',array('last_modified'=>date('c',$timestamp)),null,array('%s'));
}

?>