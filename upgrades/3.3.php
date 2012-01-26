<?php

/*
 *	3.3 UPGRADE NOTICE
 *	--------------------
 *
 	2011
	 	- Add new field to the topspin_store table (internal_name)
	 	- Add new field to the topspin_items table (campaign_id)
	 	- Create the topspin_orders table
	 	- Create the topspin_orders_items table
 */

global $wpdb;
global $store;

// Checks and adds a new field to the topspin_tags table (internal_name)
if(!topspin_table_column_exists('topspin_stores','internal_name','VARCHAR(255)')) {
	topspin_table_column_add('topspin_stores','internal_name','VARCHAR(255)');
}

// Checks and adds a new field to the topspin_items table (campaign_id)
if(!topspin_table_column_exists('topspin_items','campaign_id','INT')) {
	topspin_table_column_add('topspin_items','campaign_id','INT');
}

// Create new tables
topspin_run_sql_file('topspin_orders.sql');
topspin_run_sql_file('topspin_orders_items.sql');

?>