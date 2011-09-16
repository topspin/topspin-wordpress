<?php

/*
 *	3.2 UPGRADE NOTICE
 *	--------------------
 *	
 	- Upgrades the topspin_currency table (added unique key)
 	- Add new field to the topspin_tags table (artist_id)
 	- Runs topspin_store_featured_item.sql
 	- Migrate all store's featured_item field to the new featured item table
 	- rebuild database cache
 */

global $wpdb;
global $store;

// Checks for curreny unique key
$sqlCheckCurrencyKeys = <<<EOD
SHOW KEYS FROM {$wpdb->prefix}topspin_currency where Key_name = 'currency'
EOD;
$currencyKeys = $wpdb->get_results($sqlCheckCurrencyKeys);
//Adds the new key if it does not exists
if(!count($currencyKeys)) {
	$sqlUpgradeCurrencyTable = <<<EOD
	ALTER TABLE  `{$wpdb->prefix}topspin_currency` ADD UNIQUE (
	`currency`
	);
EOD;
	$wpdb->query($sqlUpgradeCurrencyTable);
}

// Checks and adds a new field to the topspin_tags table (artist_id)
if(!topspin_table_column_exists('topspin_tags','artist_id','INT')) {
	topspin_table_column_add('topspin_tags','artist_id','INT');
}

// Create new featured items table
topspin_run_sql_file('topspin_stores_featured_item.sql');

// Migrate all featured item ID to the new featured items table
$stores = $store->getStores('all');
foreach($stores as $_store) {
	$store->updateStoreFeaturedItems($_store->featured_item,$_store->store_id);
}

?>