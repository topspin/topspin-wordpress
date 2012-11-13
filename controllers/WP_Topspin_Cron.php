<?php

add_action(WP_Topspin_Cron::SCHEDULE_NAME,				array('WP_Topspin_Cron', 'doCron'));
add_filter('cron_schedules',							array('WP_Topspin_Cron', 'addCustomSchedules'));

/**
 * Handles the cron tasks of Topspin data on WordPress
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Cron {

	const PREFETCH_DELAY = 300;					// number of seconds to prefetch before the cron event
	const SCHEDULE_TIMEOUT = 'every_30_min';
	const SCHEDULE_NAME = 'topspin_cron_sync';

	/**
	 * Initializes the WP-Cron events
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function initSchedules() {
		$next = self::nextScheduled();
		// Check prefetching
		self::prefetch();
		// If there are no cron scheduled, schedule a new one
		if(!$next) { wp_schedule_event(time(), self::SCHEDULE_TIMEOUT, self::SCHEDULE_NAME); }
	}

	/**
	 * Reschedules the cron event
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function resetSchedules() {
		$next = self::nextScheduled();
		wp_clear_scheduled_hook(self::SCHEDULE_NAME);
		// If the cron is scheduled, reschedule it!
		wp_schedule_event(time(), self::SCHEDULE_TIMEOUT, self::SCHEDULE_NAME);
	}

	/**
	 * Retrieves the next timestamp for the cron event
	 * 
	 * @access public
	 * @static
	 * @return int
	 */
	public static function nextScheduled() {
		$now = time();
		$time = wp_next_scheduled(self::SCHEDULE_NAME);
		return ($time) ? $time : 0;
	}

	/**
	 * Retrieves the next time difference for the cron event
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function nextHit() {
		$time = self::nextScheduled();
		if($time) { return 'about ' . human_time_diff($time); }
		else { return 'Not scheduled.'; }
	}

	/**
	 * Adds custom cron schedules
	 * 
	 * @access public
	 * @static
	 * @param array $schedules
	 * @return array
	 */
	public static function addCustomSchedules($schedules) {
		$schedules['every_30_min'] = array(
			'interval' => 1800,
			'display' => __('Once every 30 minutes')
		);
		$schedules['every_5_min'] = array(
			'interval' => 300,
			'display' => __('Once every 5 minutes')
		);
		$schedules['every_30_secs'] = array(
			'interval' => 30,
			'display' => __('Once every 30 seconds')
		);
		return $schedules;
	}

	/**
	 * Runs the cron
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function doCron() {
		// Sync Artists
		WP_Topspin_Cache::syncArtists(TOPSPIN_API_PREFETCHING);
		// Sync Offers
		WP_Topspin_Cache::syncOffers(TOPSPIN_API_PREFETCHING);
		// Reset prefetch flag
		update_option('topspin_prefetched', false);
	}
	
	/**
	 * Checks the next scheduled, and does the prefetching actions
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function prefetch() {
		$next = self::nextScheduled();
		// If the prefetch time has been met
		if(($next - self::PREFETCH_DELAY) <= time()) {
			// If not yet prefetched
			$prefetched = get_option('topspin_prefetched');
			if(!$prefetched) {
				do_action('topspin_cron_prefetching');	
				// Update prefetch flag to true so the next hit doesn't prefetch
				update_option('topspin_prefetched', true);
			}
		}
	}

}

?>