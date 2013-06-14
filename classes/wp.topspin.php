<?php
/**
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin {

	/**
	 * Retrieves a list of timeout options
	 * 
	 * @access public
	 * @static
	 * @return array
	 */
	public static function getTimeoutOptions() {
		return array(
			'604800' => '1 week',
			'1210000' => '2 weeks',
			'1814000' => '3 weeks',
			'2630000' => '1 month',
			'5259000' => '2 months',
			'7889000' => '3 months'
		);
	}

	/**
	 * Checks if there are artists in WordPress
	 *
	 * @access public
	 * @global object $wpdb
	 * @static
	 * @return bool
	 */
	public static function hasArtists() {
		global $wpdb;
		$sql = "SELECT COUNT(ID) FROM %s p WHERE p.post_type = '%s'";
		$esql = sprintf($sql, $wpdb->posts, TOPSPIN_CUSTOM_POST_TYPE_ARTIST);
		$count = $wpdb->get_var($esql);
		return ($count) ? true : false;
	}

	/**
	 * Checks if an artist is saved in the Cache Settings
	 *
	 * @access public
	 * @global object $topspin_artist_ids
	 * @static
	 * @return bool
	 */
	public static function hasSyncedArtists() {
		global $topspin_artist_ids;
		if(count($topspin_artist_ids)) {
			if(is_string($topspin_artist_ids) && strlen(trim($topspin_artist_ids))) { return true; }
			else if(is_array($topspin_artist_ids)) {
				if(count($topspin_artist_ids)==1) { return (strlen(trim($topspin_artist_ids[0]))) ? true : false; }
				else { return true; }
			}
		}
		return false;
	}
	
	/**
	 * Checks if the post types are defined in WordPress and that they exist
	 *
	 * @access public
	 * @static
	 * @return bool
	 */
	public static function hasPostTypes() {
		return (post_type_exists(TOPSPIN_CUSTOM_POST_TYPE_ARTIST) && post_type_exists(TOPSPIN_CUSTOM_POST_TYPE_STORE) && post_type_exists(TOPSPIN_CUSTOM_POST_TYPE_OFFER) && post_type_exists(TOPSPIN_CUSTOM_POST_TYPE_PRODUCT)) ? true : false;
	}

	/**
	 * Checks if the current artist is checked for caching
	 * 
	 * @access public
	 * @global object $topspin_artist_ids
	 * @static
	 * @return bool
	 */
	public static function artistIsChecked($artistId) {
		global $topspin_artist_ids;
		if($topspin_artist_ids && is_array($topspin_artist_ids)) { return (in_array($artistId,$topspin_artist_ids)) ? true : false; }
		return false;
	}

	/**
	 * Merges the active offer types into the available offer types array
	 * 
	 * @access public
	 * @static
	 * @param array $offerTypes An array of offer types
	 * @param array $activeTypes An array of active offer types
	 * @return void
	 */
	public static function mergeOfferTypes($offerTypes, $activeTypes) {
		$offerTypesPool = array_keys($offerTypes);
		$mergedOfferTypes = array();
		if(count($activeTypes)) {
			foreach($activeTypes as $type) {
				// Append to merged array only if the tag is in the pool
				if(in_array($type, $offerTypesPool)) { array_push($mergedOfferTypes, $type); }
			}
		}
		foreach($offerTypes as $key=>$name) {
			if(!in_array($key, $mergedOfferTypes)) { array_push($mergedOfferTypes, $key); }
		}
		return $mergedOfferTypes;
	}
	
	/* !----- MENUS ----- */
	public static function updateMenus($data) {
		update_option('topspin_menu_activated', ($data['menu_activated']=='on') ? 1 : 0);
		// Clear menu orders
		self::clearStoreOrders();
		// Update menus
		if(count($data['menus'])) {
			foreach($data['menus'] as $key=>$store_ID) {
				$order = ($key+1);
				$postData = array(
					'menu_order' => $order,
					'ID' => $store_ID
				);
				wp_update_post($postData);
				update_post_meta($store_ID, 'topspin_menu_order', $order);
				update_post_meta($store_ID, 'topspin_menu_display', 1);
			}
		}
	}
	
	/**
	 * Resets all store menu order to 0
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function clearStoreOrders() {
		global $wpdb;
		$sql = <<<EOD
		UPDATE {$wpdb->postmeta}
		SET {$wpdb->postmeta}.meta_value = 0
		WHERE {$wpdb->postmeta}.meta_key = 'topspin_menu_order'
EOD;
		$wpdb->query($sql);
		
		$sql2 = <<<EOD
		UPDATE {$wpdb->postmeta}
		SET {$wpdb->postmeta}.meta_value = 0
		WHERE {$wpdb->postmeta}.meta_key = 'topspin_menu_display'
EOD;
		$wpdb->query($sql2);
		
		$sql3 = <<<EOD
		UPDATE {$wpdb->posts}
		SET {$wpdb->posts}.menu_order = 0
		WHERE {$wpdb->posts}.post_type = %s
EOD;
		$wpdb->query($wpdb->prepare($sql3,array(TOPSPIN_CUSTOM_POST_TYPE_STORE)));
	}

	public static function MenuItemAdmin($store) {
		ob_start();
		include(sprintf('%sviews/pages/menus/item.php', TOPSPIN_PLUGIN_PATH));
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/**
	 * Outputs a menu item template
	 *
	 * @access public
	 * @static
	 * @param object $item
	 * @return void
	 */
	public static function MenuItem($item) {
		if(get_post_meta($item->ID, 'topspin_menu_display', 1)==1) { ?>
		<li class="topspin-store-navmenu">
			<a class="topspin-store-navmenu" href="<?php echo get_permalink($item->ID); ?>" data-post-id="<?php echo $item->ID; ?>"><?php echo $item->post_title; ?></a>
			<?php
			$args = array(
				'post_parent' => $item->ID
			);
			$storeChilds = WP_Topspin::getStores($args);
			if(count($storeChilds)) : ?>
				<ul class="topspin-store-navmenu"><?php foreach($storeChilds as $child) : self::MenuItem($child); endforeach; ?></ul>
			<?php endif; ?>
		</li>
		<?php }
	}

	/* !----- SPIN TAGS ----- */
	/**
	 * Retrieve all tags in the spin-tags taxonomy
	 * 
	 * @access public
	 * @static
	 * @return array|WP_Error
	 */
	public static function getSpinTags() {
		$spinTags = get_terms('spin-tags');
		$spinTagsParsed = array();
		if(count($spinTags)) {
			foreach($spinTags as $key=>$tag) { $spinTagsParsed[$tag->slug] = $tag; }
		}
		// Create the parsed array where the slug is the key
		return $spinTagsParsed;
	}
	
	/**
	 * Merges the active tags into the term object array
	 * 
	 * @access public
	 * @static
	 * @param array $tags An array of term objects
	 * @param array $activeTags An array of active tags
	 * @return void
	 */
	public static function mergeSpinTags($tags, $activeTags) {
		$tagsPool = array_keys($tags);
		$mergedTags = array();
		// If there are active tags
		if(count($activeTags)) {
			foreach($activeTags as $tag) {
				// Append to merged array only if the tag is in the pool
				if(in_array($tag, $tagsPool)) { array_push($mergedTags, $tag); }
			}
		}
		foreach($tags as $tag) {
			if(!in_array($tag->slug, $mergedTags)) { array_push($mergedTags, $tag->slug); }
		}
		return $mergedTags;
	}

	/* !----- STORE ----- */

	/**
	 * Retrieves a list of stores
	 * 
	 * @access public
	 * @static
	 * @param array $args (default: array())
	 * @return object The WP_Query object
	 */
	public static function getStores($args=array()) {
		$defaults = array(
			'post_type' => TOPSPIN_CUSTOM_POST_TYPE_STORE,
			'posts_per_page' => -1,
			'order' => 'asc',
			'orderby' => 'menu_order',
			'post_parent' => 0
		);

		// Get active stores
		$args = array_merge($defaults, $args);
		$stores = get_posts($args);

		// Sort stores
		$sortedStores = array();
		if(count($stores)) {
			$inactiveStores = array();
			// First get all checked
			foreach($stores as $store) {
				if(get_post_meta($store->ID, 'topspin_menu_display', 1)==1) { array_push($sortedStores, $store); }
				else { array_push($inactiveStores, $store); }
			}
			
			// Second get all inactive stores
			foreach($inactiveStores as $store) { array_push($sortedStores, $store); }
		}

		return $sortedStores;
	}

	/**
	 * Retrieves the given store's artist ID
	 *
	 * @param int $post_ID			The store post ID
	 * @return int
	 */
	public static function getStoreArtistId($post_ID=null) {
		$artist_id = false;
		if(!$post_ID) {
			global $post;
			if($post) { $post_ID = $post->ID; }
		}
		if($post_ID) {
			$artist_id = get_post_meta($post_ID, 'topspin_store_artist_id', 1);
		}
		return apply_filters('topspin_store_artist_id', $artist_id);
	}

	/**
	 * Retrieves the sale tag for the given store
	 *
	 * If a store post ID isn't passed, it will default to the current store
	 *
	 * @param int $post_ID			The store post ID
	 * @return string
	 */
	public static function getStoreSaleTag($post_ID=null) {
		if(!$post_ID) {
			global $post;
			if($post) { $post_ID = $post->ID; }
		}
		if($post_ID) { return get_post_meta($post_ID, 'topspin_store_sale_tag', 1); }
	}

	/**
	 * Updates the meta data for the store on WordPress
	 * 
	 * @access public
	 * @static
	 * @param int $post_ID
	 * @param object $store
	 * @return void
	 */
	public static function updateStoreMeta($post_ID, $store) {
		// Set defaults
		$defaults = array(
			'artist_id' => 0,
			'items_per_page' => 12,
			'show_all_items' => 0,
			'desc_length' => 255,
			'sale_tag' => '',
			'grid_columns' => 3,
			'default_sorting' => 'alphabetical',
			'default_sorting_by' => 'offertypes',
			'offer_type' => array(),
			'tags' => array(),
			'items_order' => array()
		);
		// Merge defaults
		$store = array_merge($defaults, $store);
		foreach($store as $key=>$value) {
			switch($key) {
				case 'items_visible':
					foreach($value as $offerPostId=>$isVisible) {
						update_post_meta($offerPostId, sprintf('topspin_offer_%d_visible', $post_ID), $isVisible);
					}
					break;
				case 'featured':
					if(count($value) && $value[0]!=0) { update_post_meta($post_ID, sprintf('topspin_store_%s', $key), $value); }
					else { update_post_meta($post_ID, sprintf('topspin_store_%s', $key), 0); }
					break;
				default:
					update_post_meta($post_ID, sprintf('topspin_store_%s', $key), $value);
					break;
			}
		}
	}
	
	public static function getFeaturedItems($post_ID) {
		return get_post_meta($post_ID, sprintf('topspin_store_featured'), 1);
	}

	/**
	 * Retrieves the store meta data
	 * 
	 * If the post ID is not set, it will default to the current post in the Loop
	 *
	 * @access public
	 * @static
	 * @global object $post
	 * @global object $wpdb
	 * @param mixed $post_ID (default: null)
	 * @return object
	 */
	public static function getStoreMeta($post_ID=null) {
		global $wpdb;
		if(!$post_ID) {
			global $post;
			$post_ID = $post->ID;
		}
		$sql = <<<EOD
	SELECT
		{$wpdb->postmeta}.meta_key,
		{$wpdb->postmeta}.meta_value
	FROM {$wpdb->postmeta}
	WHERE
		{$wpdb->postmeta}.meta_key LIKE 'topspin_store_%%'
		AND {$wpdb->postmeta}.post_id = %d
EOD;
		// Set defaults
		$meta = array(
			'artist_id' => 0,
			'items_per_page' => 12,
			'show_all_items' => 0,
			'desc_length' => 255,
			'sale_tag' => '',
			'grid_columns' => 3,
			'default_sorting' => 'alphabetical',
			'default_sorting_by' => 'offertypes',
			'offer_type' => array(),
			'tags' => array()
		);
		$data = $wpdb->get_results($wpdb->prepare($sql,array($post_ID)), OBJECT_K);
		if($data) {
			foreach($data as $record) {
				$key = str_replace('topspin_store_', '', $record->meta_key);
				switch($key) {
					case 'spin_tags':
					case 'offer_type':
					case 'tags':
					case 'items_order':
						$record->meta_value = maybe_unserialize($record->meta_value);
						break;
				}
				$meta[$key] = $record->meta_value;
			}
		}
		return (count($meta)) ? (object) $meta : false;
	}

	/* !----- ARTISTS ----- */

	/**
	 * Retrieves all artists
	 * 
	 * @access public
	 * @static
	 * @return object A WP_Query object
	 */
	public static function getArtists() {
		$args = array(
			'post_type' => TOPSPIN_CUSTOM_POST_TYPE_ARTIST,
			'posts_per_page' => -1
		);
		$artistQuery = new WP_Query($args);
		return $artistQuery;
	}
	
	/**
	 * Retrieves the WordPress post ID for the given artist
	 * 
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param object $artist
	 * @return int|bool The post ID if found, or false if not found
	 */
	public static function getArtistPostId($artist) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	{$wpdb->postmeta}.post_id
FROM {$wpdb->postmeta}
WHERE
	{$wpdb->postmeta}.meta_key = %s
	AND {$wpdb->postmeta}.meta_value = %s
EOD;
		$post_ID = $wpdb->get_var($wpdb->prepare($sql, array('topspin_artist_id', $artist->id)));
		return ($post_ID) ? $post_ID : false;
	}

	/**
	 * Create a post array for an artist
	 * 
	 * @access public
	 * @param object $artist
	 * @return array A WordPress post array
	 */
	public static function createArtist($artist) {
		$artistPost = array(
			'post_title' => $artist->name,
			'post_content' => $artist->description,
			'post_type' => TOPSPIN_CUSTOM_POST_TYPE_ARTIST,
			'post_status' => 'publish'
		);
		return $artistPost;
	}

	/**
	 * Updates the meta data for the artist on WordPress
	 * 
	 * @access public
	 * @static
	 * @param int $post_ID
	 * @param object $artist
	 * @return void
	 */
	public static function updateArtistMeta($post_ID, $artist) {
		foreach($artist as $key=>$value) { update_post_meta($post_ID, sprintf('topspin_artist_%s', $key), $value); }
	}
	
	/**
	 * Retrieves the Topspin Artist meta data
	 * 
	 * If the post ID is not set, it will default to the current post in the Loop
	 *
	 * @access public
	 * @static
	 * @global object $post
	 * @global object $wpdb
	 * @param mixed $post_ID (default: null)
	 * @return object
	 */
	public static function getArtistMeta($post_ID=null) {
		global $wpdb;
		if(!$post_ID) {
			global $post;
			$post_ID = $post->ID;
		}
		$sql = <<<EOD
	SELECT
		{$wpdb->postmeta}.meta_key,
		{$wpdb->postmeta}.meta_value
	FROM {$wpdb->postmeta}
	WHERE
		{$wpdb->postmeta}.meta_key LIKE 'topspin_artist_%%'
		AND {$wpdb->postmeta}.post_id = %d
EOD;
		$meta = array();
		$data = $wpdb->get_results($wpdb->prepare($sql,array($post_ID)), OBJECT_K);
		if($data) {
			foreach($data as $record) {
				$key = str_replace('topspin_artist_', '', $record->meta_key);
				switch($key) {
					case 'spin_tags':
						$record->meta_value = maybe_unserialize($record->meta_value);
						break;
				}
				$meta[$key] = $record->meta_value;
			}
		}
		return (count($meta)) ? (object) $meta : false;
	}

	/**
	 * Retrieves a list of stray artist post IDs
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @global array $topspin_cached_artists An array of post IDs that were updated
	 * @return array|bool
	 */
	public static function getStrayArtists() {
		global $wpdb, $topspin_cached_artists;
		if(count($topspin_cached_artists)) {
			$post_IDs = implode(',', $topspin_cached_artists);
			$sql = <<<EOD
SELECT
	{$wpdb->posts}.ID
FROM {$wpdb->posts}
WHERE
	{$wpdb->posts}.post_type = '%s'
	AND {$wpdb->posts}.ID NOT IN (%s)
EOD;
			$esql = sprintf($sql, TOPSPIN_CUSTOM_POST_TYPE_ARTIST, $post_IDs);
			$result = $wpdb->get_results($esql);
			if(count($result)) {
				$delete_IDs = array();
				foreach($result as $row) { array_push($delete_IDs, $row->ID); }
				return $delete_IDs;
			}
		}
	}
	
	/**
	 * Deletes multiple artist post and their meta data
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param array $delete_IDs An array of post IDs to delete
	 * @return void
	 */
	public static function deleteArtistIds($delete_IDs) {
		global $wpdb;
		$post_IDs = implode(',', $delete_IDs);
		$sql = <<<EOD
DELETE FROM {$wpdb->posts}
WHERE
	{$wpdb->posts}.post_type = '%s'
	AND {$wpdb->posts}.ID IN (%s)
EOD;
		$esql = sprintf($sql, TOPSPIN_CUSTOM_POST_TYPE_ARTIST, $post_IDs);
		$wpdb->query($esql);
		// Delete meta data
		self::deleteArtistIdsMeta($delete_IDs);
	}
	
	/**
	 * Deletes meta data of multiple artist posts
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param array $delete_IDs An array of post IDs to delete
	 * @return void
	 */
	public static function deleteArtistIdsMeta($delete_IDs) {
		global $wpdb;
		$post_IDs = implode(',', $delete_IDs);
		$sql = <<<EOD
DELETE FROM {$wpdb->postmeta}
WHERE
	{$wpdb->postmeta}.post_id IN (%s)
EOD;
		$esql = sprintf($sql, $post_IDs);
		$wpdb->query($esql);
	}

	/* !----- OFFERS ----- */

	/**
	 * Checks to see if the given offer post ID is on sale
	 * 
	 * @access public
	 * @static
	 * @param object $offer The offer post object
	 * @return void
	 */
	public static function isOnSale($offer) {
		$saleTag = self::getStoreSaleTag();
		$tags = wp_get_post_terms($offer->ID, 'spin-tags');
		if(count($tags)) {
			foreach($tags as $tag) {
				if($tag->slug==$saleTag) { return true; }
			}
		}
		return false;
	}

	/**
	 * Retrieves the parsed meta key name
	 * 
	 * @access public
	 * @static
	 * @param string $name
	 * @return string
	 */
	public static function offerMetaKey($name) {
		return sprintf('topspin_offer_%s', $name);
	}

	/**
	 * Retrieves an array of WordPress post IDs of all offers
	 *
	 * @access public
	 * @static
	 * @return array
	 */
	public static function getOfferPostIds() {
		global $wpdb;
		$sql = <<<EOD
SELECT
	p.ID
FROM {$wpdb->posts} p
WHERE
	p.post_type = %s
EOD;
		$esql = $wpdb->prepare($sql, array(TOPSPIN_CUSTOM_POST_TYPE_OFFER));
		return $wpdb->get_col($esql);
	}

	/**
	 * Retrieves the WordPress post ID for the given offer
	 * 
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param object $offer
	 * @return int|bool The post ID if found, or false if not found
	 */
	public static function getOfferPostId($offer) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	{$wpdb->postmeta}.post_id
FROM {$wpdb->postmeta}
WHERE
	{$wpdb->postmeta}.meta_key = %s
	AND {$wpdb->postmeta}.meta_value = %s
EOD;
		$post_ID = $wpdb->get_var($wpdb->prepare($sql, array('topspin_offer_id', $offer->id)));
		return ($post_ID) ? $post_ID : false;
	}

	/**
	 * Create a post array for an offer
	 * 
	 * @access public
	 * @param object $offer
	 * @return array A WordPress post array
	 */
	public static function createOffer($offer) {
		$offerPost = array(
			'post_title' => $offer->name,
			'post_content' => (isset($offer->description)) ? $offer->description : '',
			'post_type' => TOPSPIN_CUSTOM_POST_TYPE_OFFER,
			'post_status' => 'publish'
		);
		// If tags exists, create the terms
		if(isset($offer->tags)) {
			$offerPost['tax_input'] = array(
				'spin-tags' => $offer->tags
			);
		}
		return $offerPost;
	}

	/**
	 * Updates the meta data for the offer on WordPress
	 * 
	 * @access public
	 * @static
	 * @param int $post_ID
	 * @param object $offer
	 * @return void
	 */
	public static function updateOfferMeta($post_ID, $offer) {
		foreach($offer as $key=>$value) { update_post_meta($post_ID, sprintf('topspin_offer_%s', $key), $value); }
		// Set the campaign ID
		update_post_meta($post_ID, 'topspin_offer_campaign_id', Topspin_API::getCampaignId($offer));
	}
	
	/**
	 * Retrieves the Topspin Offer meta data
	 * 
	 * If the post ID is not set, it will default to the current post in the Loop
	 *
	 * @access public
	 * @static
	 * @global object $post
	 * @global object $wpdb
	 * @param mixed $post_ID (default: null)
	 * @return object
	 */
	public static function getOfferMeta($post_ID=null) {
		global $wpdb;
		if(!$post_ID) {
			global $post;
			$post_ID = $post->ID;
		}
		$sql = <<<EOD
	SELECT
		{$wpdb->postmeta}.meta_key,
		{$wpdb->postmeta}.meta_value
	FROM {$wpdb->postmeta}
	WHERE
		{$wpdb->postmeta}.meta_key LIKE 'topspin_offer_%%'
		AND {$wpdb->postmeta}.post_id = %d
EOD;
		$meta = array();
		$data = $wpdb->get_results($wpdb->prepare($sql,array($post_ID)), OBJECT_K);
		if($data) {
			foreach($data as $record) {
				$key = str_replace('topspin_offer_', '', $record->meta_key);
				switch($key) {
					case 'tags':
					case 'campaign':
						$record->meta_value = maybe_unserialize($record->meta_value);
						break;
				}
				$meta[$key] = $record->meta_value;
			}
		}
		return (count($meta)) ? (object) $meta : false;
	}

	/**
	 * Retrieves a list of stray offer post IDs
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @global array $topspin_cached_ids An array of post IDs that were updated
	 * @return array|bool
	 */
	public static function getStrayOffers() {
		global $wpdb, $topspin_cached_ids;
		if(count($topspin_cached_ids)) {
			$post_IDs = implode(',', $topspin_cached_ids);
			$sql = <<<EOD
SELECT
	{$wpdb->posts}.ID
FROM {$wpdb->posts}
WHERE
	{$wpdb->posts}.post_type = '%s'
	AND {$wpdb->posts}.ID NOT IN (%s)
EOD;
			$esql = sprintf($sql, TOPSPIN_CUSTOM_POST_TYPE_OFFER, $post_IDs);
			$result = $wpdb->get_results($esql);
			if(count($result)) {
				$delete_IDs = array();
				foreach($result as $row) { array_push($delete_IDs, $row->ID); }
				return $delete_IDs;
			}
		}
	}

	/**
	 * Deletes all associated product posts for the given offer post
	 *
	 * @param int $offerPostId
	 * @return bool
	 */
	public static function deleteOfferProducts($offerPostId) {
		global $wpdb;
		$sql = <<<EOD
DELETE FROM {$wpdb->postmeta}
WHERE
	{$wpdb->postmeta}.post_id = %d
	AND {$wpdb->postmeta}.meta_key = 'topspin_offer_product_post_id'
EOD;
		$esql = sprintf($sql, $offerPostId);
		return $wpdb->query($esql);
	}

	/**
	 * Adds the product post ID to a offer post ID
	 *
	 * @param int $offerPostId
	 * @param int $productPostId
	 * @return void
	 */
	public static function attachOfferProduct($offerPostId, $productPostId) {
		add_post_meta($offerPostId, 'topspin_offer_product_post_id', $productPostId);
	}

	/**
	 * Deletes multiple offer post and their meta data
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param array $delete_IDs An array of post IDs to delete
	 * @return void
	 */
	public static function deleteOfferIds($delete_IDs) {
		global $wpdb;
		$post_IDs = implode(',', $delete_IDs);
		$sql = <<<EOD
DELETE FROM {$wpdb->posts}
WHERE
	{$wpdb->posts}.post_type = '%s'
	AND {$wpdb->posts}.ID IN (%s)
EOD;
		$esql = sprintf($sql, TOPSPIN_CUSTOM_POST_TYPE_OFFER, $post_IDs);
		$wpdb->query($esql);
		// Delete meta data
		self::deleteOfferIdsMeta($delete_IDs);
	}
	
	/**
	 * Deletes meta data of multiple offer posts
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param array $delete_IDs An array of post IDs to delete
	 * @return void
	 */
	public static function deleteOfferIdsMeta($delete_IDs) {
		global $wpdb;
		$post_IDs = implode(',', $delete_IDs);
		$sql = <<<EOD
DELETE FROM {$wpdb->postmeta}
WHERE
	{$wpdb->postmeta}.post_id IN (%s)
EOD;
		$esql = sprintf($sql, $post_IDs);
		$wpdb->query($esql);
	}

	/**
	 * Retrieves the path to the cache folder (with trailing slash)
	 *
	 * @return string
	 */	
	public static function getCacheFolder() {
		$cacheFolder = WP_CONTENT_DIR . '/topspin-cache/';
		// If WordPress is a multisite, cache folder is inside of blogs.dir
		if(is_multisite()) {
			$cacheFolder = WP_CONTENT_DIR . '/blogs.dir/' . get_current_blog_id() . '/topspin-cache/';
		}
		return $cacheFolder;
	}
	
	/**
	 * Writes content to a file
	 *
	 * @param string $file		The server location of the file
	 * @param string $data		The data string to write
	 * @return bool
	 */
	public static function writeToFile($file, $data) {
		$fp = fopen($file, 'w');
		fwrite($fp, $data);
		fclose($fp);
		return true;
	}

  /**
   * Retrieves an array of gallery attachment posts
   *
   * @access public
   * @static
   * @param int $offer_ID
   */
  public static function getGallery($offer_ID) {
    $args = array(
      'post_parent' => $offer_ID,
      'numberposts' => -1,
      'post_status' => 'any',
      'post_type' => 'attachment',
      'exclude' => get_post_thumbnail_id($offer_ID)
    );
    $attachments = get_children($args);
    return $attachments;
  }
	
	/* !----- PRODUCTS ----- */

	/**
	 * Retrieves the WordPress post ID for the given product
	 * 
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param object $artist
	 * @return int|bool The post ID if found, or false if not found
	 */
	public static function getProductPostId($sku) {
		global $wpdb;
		$sql = <<<EOD
SELECT
	{$wpdb->postmeta}.post_id
FROM {$wpdb->postmeta}
WHERE
	{$wpdb->postmeta}.meta_key = %s
	AND {$wpdb->postmeta}.meta_value = %s
EOD;
		$post_ID = $wpdb->get_var($wpdb->prepare($sql, array('topspin_product_id', $sku->id)));
		return ($post_ID) ? $post_ID : false;
	}

	/**
	 * Create a post array for a product
	 * 
	 * @access public
	 * @param object $sku			A sku object returned by the Order API
	 * @return array				A WordPress post array
	 */
	public static function createProduct($sku) {
		$productPost = array(
			'post_title' => $sku->product_name,
			'post_content' => '',
			'post_type' => TOPSPIN_CUSTOM_POST_TYPE_PRODUCT,
			'post_status' => 'publish'
		);
		return $productPost;
	}

	/**
	 * Updates the meta data for the product on WordPress
	 * 
	 * @access public
	 * @static
	 * @param int $post_ID
	 * @param object $sku			A sku object returned by the Order API
	 * @return void
	 */
	public static function updateProductMeta($post_ID, $sku) {
		foreach($sku as $key=>$value) { update_post_meta($post_ID, sprintf('topspin_product_%s', $key), $value); }
	}
	
	/**
	 * Retrieves the Topspin Porudct meta data
	 * 
	 * If the post ID is not set, it will default to the current post in the Loop
	 *
	 * @access public
	 * @static
	 * @global object $post
	 * @global object $wpdb
	 * @param mixed $post_ID (default: null)
	 * @return object
	 */
	public static function getProductMeta($post_ID=null) {
		global $wpdb;
		if(!$post_ID) {
			global $post;
			$post_ID = $post->ID;
		}
		$sql = <<<EOD
	SELECT
		{$wpdb->postmeta}.meta_key,
		{$wpdb->postmeta}.meta_value
	FROM {$wpdb->postmeta}
	WHERE
		{$wpdb->postmeta}.meta_key LIKE 'topspin_product_%%'
		AND {$wpdb->postmeta}.post_id = %d
EOD;
		$meta = array();
		$data = $wpdb->get_results($wpdb->prepare($sql,array($post_ID)), OBJECT_K);
		if($data) {
			foreach($data as $record) {
				$key = str_replace('topspin_product_', '', $record->meta_key);
				switch($key) {
					case 'attributes':
					case 'weight':
						$record->meta_value = maybe_unserialize($record->meta_value);
						break;
				}
				$meta[$key] = $record->meta_value;
			}
		}
		return (count($meta)) ? (object) $meta : false;
	}

}

?>