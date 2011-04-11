<?php
/*
 *	Last Modified:		April 11, 2011
 *
 *	----------------------------------
 *	Change Log
 *	----------------------------------
 *	2011-04-11
 		- added new function topspin_rerun_check()
 		- fixed version upgrade script sorting
 		- updated file version format to be compatible with PHP lower than v5.2
 *	2011-04-08
 		- updated upgrade check
 *	2011-04-06
 		- updated topspin_upgrade()
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
	$versions = array();
	##	Find All Upgrade Script
	if($handle=opendir(TOPSPIN_PLUGIN_PATH.'/upgrades')) {
		while(false!==($file=readdir($handle))) {
			if($file!='.' && $file!='..') {
				$fileInfo = pathinfo($file);
				$fileVersion = str_replace('.'.$fileInfo['extension'],'',$fileInfo['basename']);
				##	Run only those between current version and update version
				if(version_compare($fileVersion,$currentVersion,'>=') && version_compare($fileVersion,TOPSPIN_VERSION,'<=')) {
					##	Run upgrade script
					array_push($versions,$fileVersion);
				}
			}
		}
		closedir($handle);
	}
	sort($versions);
	foreach($versions as $version) {
		$upgradeFile = TOPSPIN_PLUGIN_PATH.'/upgrades/'.$version.'.php';
		include($upgradeFile);
	}
	sleep(1);
	$store->rebuildAll();
	update_option('topspin_version',TOPSPIN_VERSION);
	update_option('topspin_update_check',1);
}

function topspin_rerun_upgrades() {
	global $store;
	##	Find All Upgrade Script
	$versions = array();
	if($handle=opendir(TOPSPIN_PLUGIN_PATH.'/upgrades')) {
		while(false!==($file=readdir($handle))) {
			if($file!='.' && $file!='..') {
				$fileInfo = pathinfo($file);
				$fileVersion = str_replace('.'.$fileInfo['extension'],'',$fileInfo['basename']);
				array_push($versions,$fileVersion);
			}
		}
		closedir($handle);
	}
	sort($versions);
	foreach($versions as $version) {
		$upgradeFile = TOPSPIN_PLUGIN_PATH.'/upgrades/'.$version.'.php';
		include($upgradeFile);
	}
	sleep(1);
	$store->rebuildAll();
	update_option('topspin_version',TOPSPIN_VERSION);
	update_option('topspin_update_check',1);
}

?>