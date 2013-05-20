<?php

add_action(WP_Topspin_Cron::SCHEDULE_NAME,				      array('WP_Topspin_Cron', 'doCron'));
add_action(WP_Topspin_Cron::IMAGE_SCHEDULE_NAME,				array('WP_Topspin_Cron', 'doCronImages'));
add_filter('cron_schedules',							              array('WP_Topspin_Cron', 'addCustomSchedules'));

/**
 * Handles the cron tasks of Topspin data on WordPress
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Cron {

  // Number of seconds to do API prefetching before the main cron is scheduled to run
	const PREFETCH_DELAY = 300;

  // Main cron settings
	const SCHEDULE_TIMEOUT = 'hourly';
	const SCHEDULE_NAME = 'topspin_cron_sync';

	// Image cron settings
	const IMAGE_SCHEDULE_NAME = 'topspin_cron_sync_images';
	const IMAGE_SCHEDULE_TIMEOUT = 'daily';

	/**
	 * Initializes the main and image cron events
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function initSchedules() {
		$next = self::nextScheduled();
		$nextImage = self::nextScheduledImage();
		// Check prefetching
		self::prefetch();
		// If there are no main cron scheduled, schedule a new one
		if(!$next) { wp_schedule_event(time()+5, self::SCHEDULE_TIMEOUT, self::SCHEDULE_NAME); }
		if(!$nextImage) { wp_schedule_event(time()+5, self::IMAGE_SCHEDULE_TIMEOUT, self::IMAGE_SCHEDULE_NAME); }
	}

	/**
	 * Reschedules the main cron event
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function resetSchedules() {
		wp_clear_scheduled_hook(self::SCHEDULE_NAME);
	}

	/**
	 * Reschedules the image cron event
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function resetSchedulesImages() {
		wp_clear_scheduled_hook(self::IMAGE_SCHEDULE_NAME);
	}

	/**
	 * Retrieves the next timestamp for the main cron event
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
	 * Retrieves the next timestamp for the image cron event
	 * 
	 * @access public
	 * @static
	 * @return int
	 */
	public static function nextScheduledImage() {
		$now = time();
		$time = wp_next_scheduled(self::IMAGE_SCHEDULE_NAME);
		return ($time) ? $time : 0;
	}

	/**
	 * Retrieves the next time difference for the main cron event
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
	 * Retrieves the next time difference for the image cron event
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function nextHitImages() {
		$time = self::nextScheduledImage();
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
	 * Runs the main cron and syncs all offers
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function doCron() {
		// Sync offers
		WP_Topspin_Cache::syncOffers(TOPSPIN_API_PREFETCHING);
		// Reset prefetch flag
		update_option('topspin_prefetched', false);
	}

	/**
	 * Runs the image cron and syncs all offer images
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function doCronImages() {
		// Sync offer images
		WP_Topspin_Cache::syncOffersImages(TOPSPIN_API_PREFETCHING);
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