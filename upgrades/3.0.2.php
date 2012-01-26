<?php
/*
 *	3.0.2 UPGRADE NOTICE
 *	--------------------
 *	2012-01-26
 		- Added comments
 */

//	Resets Cron
wp_clear_scheduled_hook('topspin_cron_fetch_items');
wp_schedule_event(time(),'every_5_min','topspin_cron_fetch_items');

?>