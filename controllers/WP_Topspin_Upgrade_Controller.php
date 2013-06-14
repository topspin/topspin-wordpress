<?php

add_action('topspin_init',						array('WP_Topspin_Upgrade_Controller', 'init'));

/**
 * Handles the upgrade/migration process from previous Topspin version to 4.0
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Upgrade_Controller {

	/**
	 * Initializes the upgrade controller and checks to see if a script needs to run
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function init() {
		global $wpdb;
		$exists = self::tableExists($wpdb->prefix.'topspin_settings');
		if($exists) {
			$oldVersion = self::getPluginVersion();
			if($oldVersion > 0 && version_compare($oldVersion, '4.0.0', '<')) {
				self::doUpgrade();
				// Remove notices
				remove_action('admin_notices', array('WP_Topspin_Notices', 'checkArtists'));
				remove_action('admin_notices', array('WP_Topspin_Notices', 'checkSyncedArtists'));
				// Add notices
				add_action('admin_notices', array('WP_Topspin_Notices', 'upgradeSuccess'));
			}
		}
	}
	/**
	 * Retrieves the stored version from the database
	 *
	 * @access public
	 * @static
	 * @return float
	 */
	public static function getPluginVersion() {
		return get_option('topspin_version');
	}
	/**
	 * Runs the upgrade script for pre-4.0 versions
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function doUpgrade() {
		set_time_limit(0);
		// Hook into the finish sync offers action
		add_action('topspin_finish_sync_offers', array('WP_Topspin_Upgrade_Controller', 'finishSyncOffers'));
		$oldSettings = self::_getSettings();
		// Migrate general settings
		update_option('topspin_api_username', $oldSettings['topspin_api_username']->value);
		update_option('topspin_api_key', $oldSettings['topspin_api_key']->value);
		update_option('topspin_template_mode', $oldSettings['topspin_template_mode']->value);
		// Migrate artist IDs (array | string)
		$artist_ids = array();
		if(is_array($oldSettings['topspin_artist_id']->value)) { $artist_ids = $oldSettings['topspin_artist_id']->value; }
		else if(is_string($oldSettings['topspin_artist_id']->value)) { $artist_ids = array($oldSettings['topspin_artist_id']->value); }
		update_option('topspin_artist_ids', $artist_ids);
		// Run artist sync
		WP_Topspin_Cache::syncArtists(false);
		// Run offers sync
		WP_Topspin_Cache::syncOffers(false, true);
	}
	/**
	 * Callback for when the offer is finished syncing
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function finishSyncOffers() {
		self::migrateStores();
	}
	/**
	 * Migrates the old stores to the new stores format
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function migrateStores() {
		$oldStores = self::_getStores();
		if($oldStores && count($oldStores)) {
			foreach($oldStores as $store) {
				// Create the store
				$storeData = array(
					'post_title' => $store->post_title,
					'post_name' => $store->post_name,
					'post_parent' => $store->post_parent,
					'post_date' => $store->created_date,
					'post_type' => TOPSPIN_CUSTOM_POST_TYPE_STORE,
					'post_status' => 'publish'
				);
				// Retrieve offer types/tags
				$storeOfferTypes = self::_getStoreOfferTypes($store->store_id);
				$storeTags = self::_getStoreTags($store->store_id);
				// Retrieve the post if it exists
				$post_ID = self::_getStorePostId($store->store_id);
				// If the post ID doesn't exist, create it
				if(!$post_ID) { $post_ID = wp_insert_post($storeData); }
				// If the post was created successfully, migrate the store meta
				if($post_ID) {
					// Remap meta
					if($store->default_sorting_by=='tag') { $store->default_sorting_by = 'tags'; }
					// Map meta
					$storeMeta = array(
						'legacy_store_id' => $store->store_id,
						'artist_id' => self::_getSettingsValue('topspin_artist_id'),
						'items_per_page' => $store->items_per_page,
						'show_all_items' => $store->show_all_items,
						'desc_length' => 255,
						'sale_tag' => '',
						'grid_columns' => $store->grid_columns,
						'default_sorting' => $store->default_sorting,
						'default_sorting_by' => $store->default_sorting_by,
						'offer_type' => $storeOfferTypes,
						'tags' => $storeTags,
						'items_order' => array()
					);
					WP_Topspin::updateStoreMeta($post_ID, $storeMeta);
				}
			}
			update_option('topspin_version', TOPSPIN_VERSION);
		}
	}

	/* !----- Old Table Structure Methods ----- */
	/**
	 * Checks if a table exists
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return bool
	 */
	public static function tableExists($name) {
		global $wpdb;
		$sql = <<<EOD
SELECT table_name
FROM information_schema.tables
WHERE table_schema = '%s'
AND table_name = %s;
EOD;
		$exists = $wpdb->get_var($wpdb->prepare($sql, array(DB_NAME, $name)));
		return ($exists) ? true : false;
	}

	/**
	 * Retrieves an array of old topspin store settings
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return array			A key/value paired array of the old settings
	 */
	public static function _getSettings() {
		global $wpdb;
		$sql = <<<EOD
SELECT
	ts.name,
	ts.value
FROM {$wpdb->prefix}topspin_settings ts
EOD;
		return $wpdb->get_results($sql, OBJECT_K);
	}
	/**
	 * Retrieves an old Topspin settings value (from _topspin_settings)
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param string $name
	 * @return mixed
	 */
	public static function _getSettingsValue($name) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	ts.value
FROM {$wpdb->prefix}topspin_settings ts
WHERE
	ts.name = %s
EOD;
		return $wpdb->get_var($wpdb->prepare($sql, array($name)));
	}
	/**
	 * Retrieve a list of old stores
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return array
	 */
	public static function _getStores() {
		global $wpdb;
		$sql = <<<EOD
SELECT
	ts.*,
	p.post_title,
	p.post_name,
	p.post_parent
FROM {$wpdb->prefix}topspin_stores ts
LEFT JOIN {$wpdb->posts} p ON ts.post_id = p.ID
EOD;
		return $wpdb->get_results($sql);
	}
	/**
	 * Retrieves a list of checked offer types for a store
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return array
	 */
	public static function _getStoreOfferTypes($store_ID) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	tsot.type
FROM {$wpdb->prefix}topspin_stores_offer_type tsot
WHERE
	tsot.store_id = %d
	AND tsot.status = 1
EOD;
		return $wpdb->get_col($wpdb->prepare($sql, array($store_ID)));
	}
	/**
	 * Retrieves a list of checked tags for a store
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return array
	 */
	public static function _getStoreTags($store_ID) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	tst.tag
FROM {$wpdb->prefix}topspin_stores_tag tst
WHERE
	tst.store_id = %d
	AND tst.status = 1
EOD;
		$tagSlugs = array();
		$tags = $wpdb->get_col($wpdb->prepare($sql, array($store_ID)));
		if(is_array($tags) && count($tags)) {
			foreach($tags as $tag) {
				$term = get_term_by('name', $tag, 'spin-tags');
				if($term) { array_push($tagSlugs, $term->slug); }
			}
		}
		return $tagSlugs;
	}
	/**
	 * Retrieves the post ID for the old store ID if it already exists in the database
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param int $store_ID
	 * @return int
	 */
	public static function _getStorePostId($store_ID) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	pm.post_id
FROM {$wpdb->postmeta} pm
WHERE
	pm.meta_key = 'topspin_store_legacy_store_id'
	AND pm.meta_value = %d
EOD;
		return $wpdb->get_var($wpdb->prepare($sql, array($store_ID)));
	}
	
	/**
	 * Retieves the new store post ID based on a given legacy (pre 4.0) store post ID
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param int $legacy_post_ID
	 * @return int|bool
	 */
	public static function _getStorePostIdByLegacyPostId($legacy_post_ID) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	ts.store_id
FROM {$wpdb->prefix}topspin_stores ts
WHERE
	ts.post_id = %d
EOD;
		$legacy_store_ID = $wpdb->get_var($wpdb->prepare($sql, array($legacy_post_ID)));
		if($legacy_store_ID) {
			$post_ID = self::_getStorePostId($legacy_store_ID);
			return ($post_ID) ? $post_ID : false;
		}
	}

}

?>