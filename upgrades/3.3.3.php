<?php

/*
 *	3.3.3 UPGRADE NOTICE
 *	--------------------
 *
 	2011-01-25
	 	- Adds a new column to the topspin item images table
 */

//Drop Item Image Table
global $wpdb;
$wpdb->query("DROP TABLE ".$wpdb->prefix."topspin_items_images");

//Re-run SQL scripts
topspin_run_sql_file('topspin_currency.sql');
topspin_run_sql_file('topspin_currency_insert.sql');
topspin_run_sql_file('topspin_items.sql');
topspin_run_sql_file('topspin_items_tags.sql');
topspin_run_sql_file('topspin_items_images.sql');
topspin_run_sql_file('topspin_offer_types.sql');
topspin_run_sql_file('topspin_offer_types_insert.sql');
topspin_run_sql_file('topspin_orders.sql');
topspin_run_sql_file('topspin_orders_items.sql');
topspin_run_sql_file('topspin_settings.sql');
topspin_run_sql_file('topspin_stores.sql');
topspin_run_sql_file('topspin_stores_featured_items.sql');
topspin_run_sql_file('topspin_stores_offer_type.sql');
topspin_run_sql_file('topspin_stores_tag.sql');
topspin_run_sql_file('topspin_artists.sql');
topspin_run_sql_file('topspin_tags.sql');

//Re-run upgrade scripts
$runFiles = array(
	'3.0.2',
	'3.0.4',
	'3.1',
	'3.1.1',
	'3.1.2',
	'3.2',
	'3.3',
	'3.3.2'
);

foreach($runFiles as $version) {
	$upgradeFile = TOPSPIN_PLUGIN_PATH.'/upgrades/'.$version.'.php';
	include($upgradeFile);
}

?>