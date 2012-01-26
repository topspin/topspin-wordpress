<?php
/*
 *	3.1 UPGRADE NOTICE
 *	--------------------
 *	
 	2012-01-26
	 	- Remove 3.0.4 script (2012-01-26)
	2011
	 	- Runs 3.0.4 upgrade script (if necessary)
	 	- Creates New Table 'topspin_item_images'
	 	- Adds New Settings Option 'topspin_template_mode'
	 	- Rebuilds the database
 */

global $store;

##	Creates New Table 'topspin_item_images'
topspin_run_sql_file('topspin_items_images.sql');
topspin_run_sql_file('topspin_artists.sql');

##	Adds New Settings Option 'topspin_template_mode'
if(!$store->settingExist('topspin_template_mode')) { $store->setSetting('topspin_template_mode','standard'); }

?>