<?php

add_action('topspin_notices_changes_saved', array('WP_Topspin_Notices', 'doChangesSaved'));
add_action('topspin_notices_synced', array('WP_Topspin_Notices', 'doSynced'));
add_action('topspin_notices_schedules_reset', array('WP_Topspin_Notices', 'doSchedulesReset'));
add_action('topspin_notices_prefetch_purged_all', array('WP_Topspin_Notices', 'doPrefetchPurgedAll'));
add_action('admin_notices', array('WP_Topspin_Notices', 'checkVerification'));
add_action('admin_notices', array('WP_Topspin_Notices', 'checkArtists'));
add_action('admin_notices', array('WP_Topspin_Notices', 'checkSyncedArtists'));
add_action('admin_notices', array('WP_Topspin_Notices', 'checkIsSyncingOffers'));
add_action('admin_notices', array('WP_Topspin_Notices', 'checkIsSyncingOffersImages'));
add_action('admin_notices', array('WP_Topspin_Notices', 'checkIsSyncingProducts'));

/**
 * Handles admin notices
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Notices {

	public static function output($message, $class='error') {
		$html = <<<EOD
<div class="%s">
   <p>%s</p>
</div>
EOD;
		echo sprintf($html, $class, $message);
	}
	
	/* !----- Topspin Notices ----- */

	public static function doChangesSaved() {
		add_action('admin_notices', array('WP_Topspin_Notices', 'changesSaved'));
	}

	public static function doSynced() {
		add_action('admin_notices', array('WP_Topspin_Notices', 'synced'));
	}

	public static function doSchedulesReset() {
		add_action('admin_notices', array('WP_Topspin_Notices', 'schedulesReset'));
	}
	
	public static function doPrefetchPurgedAll() {
		add_action('admin_notices', array('WP_Topspin_Notices', 'prefetchPurgedAll'));
	}

	/* !----- Admin Notices ----- */
	/**
	 * Displays the changes saved notice
	 *
	 * @access public
	 * @static
	 */
	public static function changesSaved() {
		self::output('Changes saved.', 'updated');
	}
	/**
	 * Displays the sync success notice
	 *
	 * @access public
	 * @static
	 */
	public static function synced() {
		self::output('Synced successfully.', 'updated');
	}
	/**
	 * Displays the schedules reset notice
	 *
	 * @access public
	 * @static
	 */
	public static function schedulesReset() {
		self::output('Schedules reset successfully.', 'updated');
	}
	/**
	 * Displays the prefetch purged all notice
	 *
	 * @access public
	 * @static
	 */
	public static function prefetchPurgedAll() {
		self::output('Prefetch cache purged successfully.', 'updated');
	}
	/**
	 * Checks if the API crendentials are verified
	 *
	 * @access public
	 * @static
	 */
	public static function checkVerification() {
		if(!TOPSPIN_API_VERIFIED) {
			self::output('Your API credentials are invalid.');
		}
	}
	/**
	 * Checks if there are artists available in WordPress and displays the notice
	 *
	 * @access public
	 * @static
	 */
	public static function checkArtists() {
		if(!TOPSPIN_HAS_ARTISTS) {
			self::output('There are currently no artists cached. Please run the <a href="admin.php?page=topspin/page/cache">artist cache</a>.');
		}
	}
	/**
	 * Checks if any artists are checked for syncing in WordPress and displays the notice
	 *
	 * @access public
	 * @static
	 */
	public static function checkSyncedArtists() {
		if(!TOPSPIN_HAS_SYNCED_ARTISTS) {
			self::output('You have no artists checked in your cache settings. Please <a href="admin.php?page=topspin/page/general">select an artist</a>.');
		}
	}
	/**
	 * Checks if offers are currently syncing and displays the notice
	 *
	 * @access public
	 * @static
	 */
	public static function checkIsSyncingOffers() {
		if(get_option('topspin_is_syncing_offers')) {
			self::output('Your offers are currently being updated via the API.', 'updated');
		}
	}
	/**
	 * Checks if offers images are currently syncing and displays the notice
	 *
	 * @access public
	 * @static
	 */
	public static function checkIsSyncingOffersImages() {
		if(get_option('topspin_is_syncing_offers_images')) {
  		$current = get_option('topspin_sync_offers_image_current');
  		$total = get_option('topspin_sync_offers_image_total');
			self::output(sprintf('Your images are currently being updated via the API. (Progress: %d/%d)', $current, $total), 'updated');
		}
	}
	/**
	 * Checks if products are currently syncing and displays the notice
	 *
	 * @access public
	 * @static
	 */
	public static function checkIsSyncingProducts() {
		if(get_option('topspin_is_syncing_products')) {
			self::output('Your products are currently being updated via the API.', 'updated');
		}
	}
	/**
	 * Displays the upgrade success notice
	 *
	 * @access public
	 * @static
	 */
	public static function upgradeSuccess() {
		self::output('Your Topspin store has been successfully migrated to the new version! Please allow 15-30 minutes for the plugin to pull in new data.', 'updated');		
	}

}

?>