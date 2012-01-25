<?php

/*
 *	3.1.1 UPGRADE NOTICE
 *	--------------------
 *	
 	- Adds 'poster_image_source' Column into 'topspin_items' table
 */

global $wpdb;
global $store;

##	Adds 'poster_image_source' Column into 'topspin_items' table
$sqlCheckPosterImageSourceField = <<<EOD
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE
	TABLE_SCHEMA = '{$wpdb->dbname}'
	AND TABLE_NAME = '{$wpdb->prefix}topspin_items'
	AND COLUMN_NAME = 'poster_image_source'
EOD;
if(!$wpdb->get_var($sqlCheckPosterImageSourceField)) {
	$sqlUpgradeTable = <<<EOD
ALTER TABLE `{$wpdb->prefix}topspin_items` ADD `poster_image_source` TEXT NOT NULL AFTER `poster_image` ;
EOD;
	$wpdb->query($sqlUpgradeTable);
}

?>