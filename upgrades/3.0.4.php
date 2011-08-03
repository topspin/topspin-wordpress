<?php

global $wpdb;
global $store;

$timestamp = time();

##	Adds 'last_modifed' Column into 'topspin_items' table
$sqlCheckItemLastModifiedField = <<<EOD
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE
	TABLE_SCHEMA = '{$wpdb->dbname}'
	AND TABLE_NAME = '{$wpdb->prefix}topspin_items'
	AND COLUMN_NAME = 'last_modified'
EOD;
if(!$wpdb->get_var($sqlCheckItemLastModifiedField)) {
	$sqlUpgradeTable = <<<EOD
ALTER TABLE `{$wpdb->prefix}topspin_items` ADD `last_modified` TIMESTAMP NOT NULL ;
EOD;
	$wpdb->query($sqlUpgradeTable);

	##	Updates all rows in 'topspin_items' 'last_modified' column with the current timestamp
	$wpdb->update($wpdb->prefix.'topspin_items',array('last_modified'=>date('Y-m-d H:i:s',$timestamp)),null,array('%s'));
}

?>