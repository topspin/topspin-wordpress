<?php

/*
 *	3.1.2 UPGRADE NOTICE
 *	--------------------
 *	
 	- Forces re-run of all past upgrade scripts
 */


global $store;
##	Find All Upgrade Script
$versions = array();
if($handle=opendir(TOPSPIN_PLUGIN_PATH.'/upgrades')) {
	while(false!==($file=readdir($handle))) {
		if($file!='.' && $file!='..') {
			$fileInfo = pathinfo($file);
			$fileVersion = str_replace('.'.$fileInfo['extension'],'',$fileInfo['basename']);
			if($fileVersion=='3.1.2') { continue; }
			else { array_push($versions,$fileVersion); }
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

?>