<?php

/*
 *	3.1 UPGRADE NOTICE
 *	--------------------
 *	
 	- Runs 3.0.4 upgrade script (if necessary)
 	- Creates New Table 'topspin_item_images'
 	- Adds New Settings Option 'topspin_template_mode'
 	- Rebuilds the database
 */

global $wpdb;
global $store;

##	Runs 3.0.4 upgrade script (if necessary)
$sqlCheckItemLastModifiedField = <<<EOD
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE
	TABLE_SCHEMA = '{$wpdb->dbname}'
	AND TABLE_NAME = '{$wpdb->prefix}topspin_items'
	AND COLUMN_NAME = 'last_modified'
EOD;
if(!$wpdb->get_var($sqlCheckItemLastModifiedField)) {
	$upgrade304File = TOPSPIN_PLUGIN_PATH.'/upgrades/3.0.4.php';
	if(file_exists($upgrade304File)) { include($upgrade304File); }
}

##	Creates New Table 'topspin_item_images'
topspin_run_sql_file('topspin_items_images.sql');
topspin_run_sql_file('topspin_artists.sql');

##	Adds New Settings Option 'topspin_template_mode'
if(!$store->settingExist('topspin_template_mode')) { $store->setSetting('topspin_template_mode','standard'); }

?>