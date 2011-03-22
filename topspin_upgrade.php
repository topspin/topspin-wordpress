<?php

add_action('init','topspin_upgrade');

function topspin_upgrade() {
	$currentVersion = get_option('topspin_version');
	##	If version is less or not set
	if(version_compare($currentVersion,TOPSPIN_VERSION,'<')) {
		##	Run upgrade script
		$upgradeFile = TOPSPIN_PLUGIN_PATH.'/upgrades/'.TOPSPIN_VERSION.'.php';
		if(file_exists($upgradeFile)) { include($upgradeFile); }
		##	Run upgrade script
		update_option('topspin_version',TOPSPIN_VERSION);
	}
}

?>