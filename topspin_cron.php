<?php

### Hooks
add_filter('cron_schedules','topspin_cron_schedules');
add_action('topspin_cron_fetch_items','topspin_cron_rebuild');

### Cron Schedules
function topspin_cron_schedules($schedules) {
	// add a 'weekly' schedule to the existing set
	$schedules['every_5_min'] = array(
		'interval' => 300,
		'display' => __('Every 5 Minutes')
	);
	$schedules['every_30_seconds'] = array(
		'interval' => 30,
		'display' => __('Every 30 Seconds')
	);
	return $schedules;
}

###	Cron Functions
function topspin_cron_rebuild() {
	global $store;
	###	Rebuild
	$store->rebuildAll();
}

?>