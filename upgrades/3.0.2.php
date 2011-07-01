<?php

##	Resets Cron
wp_clear_scheduled_hook('topspin_cron_fetch_items');
wp_schedule_event(time(),'every_5_min','topspin_cron_fetch_items');

?>