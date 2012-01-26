<?php

/*
 *	3.1.1 UPGRADE NOTICE
 *	--------------------
 *
 	2012-01-26
	 	- Update script to use builtin functions (2012-01-26)
	2011
 		- Adds 'poster_image_source' Column into 'topspin_items' table
 */

global $wpdb;
global $store;

// Add "poster_image_source" column into "topspin_items" table
if(!topspin_table_column_exists('topspin_items','poster_image')) {
	topspin_table_column_add('topspin_items','poster_image');
}

?>