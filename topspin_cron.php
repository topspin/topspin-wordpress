<?php

### Hooks
add_action('topspin_cron_fetch_items',array($store,'rebuildAll'));
add_action('wp','topspin_register_cron');

### Functions
function topspin_register_cron() {
	if(!wp_next_scheduled('topspin_cron_fetch_items')) {
		wp_schedule_event(time(),'hourly','topspin_cron_fetch_items');
	}
}

?>