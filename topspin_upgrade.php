<?php
/*
 *	Last Modified:		April 8, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
 *	2011-04-08
 		- updated upgrade check
 *	2011-04-06
 *		- updated topspin_upgrade()
 			Now does incremental/sequential upgrades (fixed for users who skips versions, all upgrades between the current version and updated version is now processed.
 			rebuild database after upgrading
 */

if(version_compare(get_option('topspin_version'),TOPSPIN_VERSION,'<')) {
	update_option('topspin_update_check',0);
	add_action('init','topspin_upgrade');
}
else {
	if(!get_option('topspin_update_check')) {
		add_action('init','topspin_upgrade');
	}
}

function topspin_upgrade() {
	global $store;
	$currentVersion = get_option('topspin_version');
	##	Find All Upgrade Script
	if($handle=opendir(TOPSPIN_PLUGIN_PATH.'/upgrades')) {
		while(false!==($file=readdir($handle))) {
			if($file!='.' && $file!='..') {
				$fileInfo = pathinfo($file);
				$fileVersion = $fileInfo['filename'];
				##	Run only those between current version and update version
				if(version_compare($fileVersion,$currentVersion,'>=') && version_compare($fileVersion,TOPSPIN_VERSION,'<=')) {
					##	Run upgrade script
					$upgradeFile = TOPSPIN_PLUGIN_PATH.'/upgrades/'.$fileVersion.'.php';
					include($upgradeFile);
				}
			}
		}
		closedir($handle);
	}
	sleep(1);
	$store->rebuildAll();
	update_option('topspin_version',TOPSPIN_VERSION);
	update_option('topspin_update_check',1);
}

?>