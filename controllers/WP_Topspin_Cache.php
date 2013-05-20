<?php

/**
 * Handles caching of Topspin data into WordPress
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Cache {

	/**
	 * Retrieves the last cached time string
	 *
	 * #Scope
	 * * artists
	 * * offers
	 *
	 * @access public
	 * @static
	 * @param string $scope
	 * @param bool $humanTimeDiff		If true, will return the human_time_diff() instead of the actual time
	 * @return string
	 */
	public static function lastCached($scope, $humanTimeDiff=true) {
		$time = get_option(sprintf('topspin_last_cache_%s', $scope));
		// If returning the human time diff
		if($humanTimeDiff) { return ($time) ? human_time_diff($time) : 'Never'; }
		// Else, return the timestamp
		else { return $time; }
	}

	/**
	 * Pulls artists into WordPress
	 *
	 * @access public
	 * @static
	 * @global object $topspin_artist_api
	 * @global object $topspin_cached_artists
	 * @uses WP_MediaHandler						Saves the artist's avatar image
	 * @param bool $prefetch						Sync from prefetch if available? (default: true)
	 * @return void
	 */
	public static function syncArtists($prefetch=false) {
		set_time_limit(0);
		global $topspin_artist_api, $topspin_cached_artists;
		$topspin_cached_artists = array();
		$results = false;
		// Retrieve the prefetched JSON file
		$prefetchedFile = WP_Topspin::getCacheFolder() . 'artists.json';		
		// If load from prefetch and the prefetch file exists, load it into the result set
		if($prefetch && file_exists($prefetchedFile)) {
			$artistsJson = file_get_contents($prefetchedFile);
			$results = json_decode($artistsJson);
		}
		// Fetch a new one if it doesn't exist
		else {
			$params = array(
				'page' => 1,
				'per_page' => 100
			);
			$results = $topspin_artist_api->getList($params);
		}
		// Loop through and cache it!
		if($results && $results->total_entries) {
			foreach($results->artists as $artist) {
				// Retrieve the post ID for the artist
				$artistPostId = WP_Topspin::getArtistPostId($artist);
				// Create a new post array for update/create
				$artistPost = WP_Topspin::createArtist($artist);
				// Create if not exists
				if(!$artistPostId) {
					$artistPostId = wp_insert_post($artistPost);
				}
				// Update if exists
				else {
					$artistPost['ID'] = $artistPostId;
					$artistPostId = wp_update_post($artistPost);
				}
				if($artistPostId) {
					// Update the artist post meta
					WP_Topspin::updateArtistMeta($artistPostId, $artist);
					if($artist->avatar_image && strlen($artist->avatar_image)) {
						if(has_post_thumbnail($artistPostId)) {
							// Save the new file
							$artistAvatarFile = WP_MediaHandler::cacheURL($artist->avatar_image);
							// If the file is cached successfully
							if($artistAvatarFile) {
								$artistAttachmentId = get_post_thumbnail_id($artistPostId);
								$artistAttachmentFile = get_attached_file($artistAttachmentId);
								// Delete the old file
								if(file_exists($artistAttachmentFile)) {
									$pathInfo = pathinfo($artistAttachmentFile);
									// Delete the original file
									unlink($artistAttachmentFile);
									// Delete all thumbnail files
									$globPath = sprintf('%s/%s-*', $pathInfo['dirname'], $pathInfo['filename']);
									$globFiles = glob($globPath);
									if($globFiles !== false) {
										foreach($globFiles as $file) {
											if(file_exists($file)) { unlink($file); }
										}
									}
								}
								// Update attachment file
								update_attached_file($artistAttachmentId, $artistAvatarFile['path']);
								// Re-generate attachment metadata
								if(!function_exists('wp_generate_attachment_metadata')) { require_once(sprintf('%swp-admin/includes/image.php', ABSPATH)); }
								$attachmentData = wp_generate_attachment_metadata($artistAttachmentId, $artistAvatarFile['path']);
								wp_update_attachment_metadata($artistAttachmentId, $attachmentData);
							}
							else { error_log('Topspin Error: Failed caching artist avatar.'); }
						}
						else {
							$artistAttachmentId = WP_MediaHandler::saveURL($artist->avatar_image, $artistPostId);
							if($artistAttachmentId) { set_post_thumbnail($artistPostId, $artistAttachmentId); }
						}
					}
					// Add to the global cached array
					array_push($topspin_cached_artists, $artistPostId);
				}
			}
			// Purge stray artists
			self::purgeStrayArtists();
			// Update last cached time
			update_option('topspin_last_cache_artists', time());
		}
	}

	/**
	 * Pulls offers into WordPress
	 * 
	 * @access public
	 * @static
	 * @global array $topspin_artist_ids
	 * @global array $topspin_cached_ids			An array of post IDs that were updated
	 * @global array $topspin_cached_terms			An array of terms that were updated
	 * @param bool $prefetch						Sync from prefetch if available? (default: true)
	 * @param bool $force							Force sync?
	 * @return void
	 */
	public static function syncOffers($prefetch=true, $force=false) {
		set_time_limit(0);
		global $topspin_artist_ids, $topspin_cached_ids, $topspin_cached_terms;
		if($force || (TOPSPIN_API_VERIFIED && TOPSPIN_POST_TYPE_DEFINED && TOPSPIN_HAS_ARTISTS && TOPSPIN_HAS_SYNCED_ARTISTS)) {
			$topspin_cached_ids = array();
			$topspin_cached_terms = array();
			if($topspin_artist_ids && is_array($topspin_artist_ids)) {
    		// Set syncing flag to true
    		update_option('topspin_is_syncing_offers', true);
				foreach($topspin_artist_ids as $artist_id) {
					$results = false;
					// Retrieve the prefetched JSON file
					$prefetchedFile = WP_Topspin::getCacheFolder() . 'offers-' . $artist_id . '.json';
					// If load from prefetch and the prefetch file exists, load it into the result set
					if($prefetch && file_exists($prefetchedFile)) {
						$offersJson = file_get_contents($prefetchedFile);
						$results = json_decode($offersJson);
						// If there are data
						if(is_array($results) && count($results)) {
							// Loop through each API data object and sync it!
							foreach($results as $offersData) {
								self::syncOffersData($offersData);
							}
						}
					}
					// Else, load from the API
					else {
						$params = array(
							'artist_id' => $artist_id
						);
						self::syncOffersPage($params);
					}
				}
				// Purge stray offers that are not in the global cached array
				self::purgeStrayOffers();
				self::purgeStrayTerms();
				// Do action
				do_action('topspin_finish_sync_offers');
			}
		}
	}

  /**
   * Pulls offers images into WordPress
   *
   * @access public
   * @static
   * @return void
   */
  public static function syncOffersImages() {
    $st = microtime(true);
		set_time_limit(0);
		global $topspin_artist_ids, $topspin_cached_ids, $topspin_cached_terms;
		if($topspin_artist_ids && is_array($topspin_artist_ids)) {
  		// Set syncing flag to true
  		update_option('topspin_is_syncing_offers_images', true);
			foreach($topspin_artist_ids as $artist_id) {
				$results = false;
				$params = array(
					'artist_id' => $artist_id
				);
				self::syncOffersImagesPage($params);
			}
      // Do action
      do_action('topspin_finish_sync_offers_images');
      $et = microtime(true);
      error_log('Image Caching Bench: '.($et-$st).' seconds');
		}
  }

	/**
	 * Pulls offers into WordPress based on parameters.
	 *
	 * Upon caching, the post ID gets stored into the global variable for
	 * verification after the caching has finished to delete existing posts
	 * that weren't found.
	 * 
	 * @access public
	 * @static
	 * @global object $topspin_store_api
	 * @param array $params
	 * @return void
	 */
	public static function syncOffersPage($params) {
		global $topspin_store_api;
		$defaults = array(
			'page' => 1,
			'per_page' => 100
		);
		$params = array_merge($defaults, $params);
		$results = $topspin_store_api->getList($params);
		self::syncOffersData($results);
		// If it's not the last page, sync the next page
		if($results->current_page < $results->total_pages) {
			$nextPageParams = array(
				'page' => $results->current_page+1
			);
			$params = array_merge($params, $nextPageParams);
			self::syncOffersPage($params);
		}
	}

	/**
	 * Pulls offers images into WordPress based on parameters.
	 * 
	 * @access public
	 * @static
	 * @global object $topspin_store_api
	 * @param array $params
	 * @return void
	 */
  public static function syncOffersImagesPage($params) {
		global $topspin_store_api;
		$defaults = array(
			'page' => 1,
			'per_page' => 100
		);
		$params = array_merge($defaults, $params);
		$results = $topspin_store_api->getList($params);
		// Store total image count
		if($results) { update_option('topspin_sync_offers_image_total', $results->total_entries); }
		self::syncOffersImageData($results);
		// If it's not the last page, sync the next page
		if($results->current_page < $results->total_pages) {
			$nextPageParams = array(
				'page' => $results->current_page+1
			);
			$params = array_merge($params, $nextPageParams);
			self::syncOffersImagesPage($params);
		}
		// Last page, delete the counts
		else {
  		delete_option('topspin_sync_offers_image_current');
  		delete_option('topspin_sync_offers_image_total');
		}
  }

	/**
	 * Syncs a Topspin API offer results into WordPress
	 *
   * @access public
   * @static
	 * @global array $topspin_cached_ids			An array of post IDs that were updated
	 * @global array $topspin_cached_terms			An array of terms that were updated
	 * @param object $results						The Topspin returned response
	 * @return void
	 */
	public static function syncOffersData($results) {
		global $topspin_cached_ids, $topspin_cached_terms;
		if($results && $results->total_entries) {
			foreach($results->offers as $key=>$offer) {
				// Cache the offer
				$offerPostId = self::cacheOffer($offer);
				// Add to the global cached array
				array_push($topspin_cached_ids, $offerPostId);
				// Set the tags for the offer
				if(isset($offer->tags) && $offer->tags) {
					foreach($offer->tags as $tag) {
						if(!in_array($tag, $topspin_cached_terms)) { array_push($topspin_cached_terms, $tag); }
					}
				}
			}
		}
	}

  /**
   * Syncs a Topspin API offer image results into WordPress
   *
   * @access public
   * @static
	 * @param object $results						The Topspin returned response
	 * @return void
   */
  public static function syncOffersImageData($results) {
		if($results && $results->total_entries) {
			foreach($results->offers as $key=>$offer) {
  			update_option('topspin_sync_offers_image_current', $key);
    		// Retrieve the post ID for the offer
    		$offerPostId = WP_Topspin::getOfferPostId($offer);
    		// Cache the offer image
    		self::cacheOfferImage($offer, $offerPostId);
			}
		}
  }

	/**
	 * Syncs an individual offer post ID
	 *
	 * Updates the thumbnail, and all meta data
	 *
	 * @global object $topspin_store_api
	 * @param int $offer_id						The WordPress post ID for the offer
	 * @return bool
	 */
	public static function syncOffersSingle($offer_id) {
		global $topspin_store_api;
		// Retrieve the cached offer meta data
		$offerMeta = WP_Topspin::getOfferMeta($offer_id);
		// Retrieve the new offer meta data
		$newOffer = $topspin_store_api->getOffer($offerMeta->id);
		// Cache this offer
		$offerPostId = self::cacheOffer($newOffer);
		// Cache this offers image
		self::cacheOfferImage($newOffer, $offerPostId);
		// Returns true
		return true;
	}

	/**
	 * Retrieves all offers in the database and pulls in all it's product data
	 *
	 * @param bool $force							Force sync?
	 * @return void
	 */
	public static function syncProducts($force=false) {
		$currentTime = time();
		$lastCached = self::lastCached('products', false);
		$elapsedTime = $currentTime - $lastCached;
		$cacheDelay = 60;
		// If never cached or elapsed time is grater than an hour
		if($force || (TOPSPIN_API_VERIFIED && TOPSPIN_POST_TYPE_DEFINED && TOPSPIN_HAS_ARTISTS && TOPSPIN_HAS_SYNCED_ARTISTS && ($elapsedTime>=$cacheDelay))) {
			// Set syncing flag to true
			update_option('topspin_is_syncing_products', true);
			$offerPostIds = WP_Topspin::getOfferPostIds();
			$totalOffers = count($offerPostIds);
			if($totalOffers) {
				// Loop through each offer post ID and cache the product
				foreach($offerPostIds as $key=>$offer_id) {
					self::cacheProduct($offer_id);
				}
			}
			// Do action
			do_action('topspin_finish_sync_products');
		}
	}

	/**
	 * Syncs an individual offer post ID product and inventory data
	 *
	 * @param int $offer_id						The WordPress post ID for the offer
	 * @return bool 
	 */
	public static function syncProductsSingle($offer_id) {
		// Cache this offer's product and inventory data
		$success = self::cacheProduct($offer_id);
		// Returns true
		return ($success) ? true : false;
	}

	/**
	 * Caches the offer into WordPress
	 *
	 * @access public
	 * @static
	 * @param object $offer			       The offer data returned from the Topspin API
	 * @return int|bool				         The new offer ID if successful
	 */
	public static function cacheOffer($offer) {
		// Retrieve the post ID for the offer
		$offerPostId = WP_Topspin::getOfferPostId($offer);
		// Create a new post array for update/create
		$offerPost = WP_Topspin::createOffer($offer);
		// Create if not exists
		if(!$offerPostId) {
			$offerPostId = wp_insert_post($offerPost);
		}
		// Update if exists
		else {
			$offerPost['ID'] = $offerPostId;
			$offerPostId = wp_update_post($offerPost);
		}
		if($offerPostId) {
			// Update the offer post meta
			WP_Topspin::updateOfferMeta($offerPostId, $offer);
		}
		return ($offerPostId) ? $offerPostId : false;
	}

  /**
   * Caches the offer's image into WordPress
   *
   * @access public
   * @static
   * @param object $offer             The offer data returned from the Topspin API
   * @param int $offerPostID          The WordPress offer post ID to attach the offer to
   * @return void
   */
  public static function cacheOfferImage($offer, $offerPostId) {
		// If an image exists, cache it!
		if(isset($offer->poster_image) && strlen($offer->poster_image)) {
  		// Retrieve the thumb post ID
			$thumbPostId = get_post_thumbnail_id($offerPostId);
			if((bool) $thumbPostId) {
				// Save the new file
				$thumbFile = WP_MediaHandler::cacheURL($offer->poster_image);
				// If the file was successfully cached
				if($thumbFile) {
					$thumbAttachment = get_attached_file($thumbPostId);
					// Delete the old file
					if(file_exists($thumbAttachment)) {
						$pathInfo = pathinfo($thumbAttachment);
						// Delete the original file
						unlink($thumbAttachment);
						// Delete all thumbnail files
						$globPath = sprintf('%s/%s-*', $pathInfo['dirname'], $pathInfo['filename']);
						$globFiles = glob($globPath);
						if($globFiles !== false) {
							foreach($globFiles as $file) {
								if(file_exists($file)) { unlink($file); }
							}
						}
					}
					// Update attachment file
					update_attached_file($thumbPostId, $thumbFile['path']);
					// Re-generate attachment metadata
					if(!function_exists('wp_generate_attachment_metadata')) { require_once(sprintf('%swp-admin/includes/image.php', ABSPATH)); }
					$attachmentData = wp_generate_attachment_metadata($thumbPostId, $thumbFile['path']);
					wp_update_attachment_metadata($thumbPostId, $attachmentData);
				}
				else { error_log('Topspin Error: Failed caching offer image.'); }
			}
			// Else, no featured image is yet attached
			else {
				// Set the offer thumbnail
				$thumbPostId = WP_MediaHandler::saveURL($offer->poster_image, $offerPostId);
				if($thumbPostId) { set_post_thumbnail($offerPostId, $thumbPostId); }
			}
		}
		// Deletes old attachments
		self::purgeOldImages($offerPostId, array($thumbPostId));
		// Cache gallery images for the offer
		if(isset($offer->campaign->product->images) && $offer->campaign->product->images && is_array($offer->campaign->product->images) && count($offer->campaign->product->images)) {
  		foreach($offer->campaign->product->images as $image) {
    		// If product image is not the same as the poster image, skip it!
    		if($image->source_url === $offer->poster_image_source) { continue; }
    		// Cache and save the source URL and attach it to the given post ID
    		$attachmentId = WP_MediaHandler::saveURL($image->source_url, $offerPostId);
  		}
		}
  }

	/**
	 * Caches the offer's product and inventory into WordPress via the Order API
	 *
	 * @global object $topspin_order_api
	 * @param int $offerPostId
	 * @param object $offer
	 * @return void
	 */
	public static function cacheProduct($offerPostId) {
		global $topspin_order_api;
		// Set default in stock
		$inStock = 0;
		$offer = WP_Topspin::getOfferMeta($offerPostId);
		if($offer) {
			// E4M are always in stock
			if(in_array($offer->offer_type, array('email_for_media'))) { $inStock++; }
			// Videos, digital items, and tickets are always in stock
			if(in_array($offer->product_type, array('video', 'digital_package', 'ticket'))) { $inStock++; }
			// Campaigns where product type is package, track, or other media is always in stock
			if(isset($offer->campaign->product) && in_array($offer->campaign->product->type, array('package', 'track', 'other_media'))) { $inStock++; }
			// Else, request the product
			else if(isset($offer->mobile_url)) {
				$params = array(
					'campaign_id' => Topspin_API::getCampaignIdByMobileUrl($offer->mobile_url)
				);
				$res = $topspin_order_api->getSkus($params);
				if($res && $res->status=='ok') {
					if(count($res->response->skus)) {
						// Purge attached products
						WP_Topspin::deleteOfferProducts($offerPostId);
						foreach($res->response->skus as $sku) {
							// Increment the in stock quantity if the sku is available
							if($sku->available) {
								$inStock += $sku->in_stock_quantity;
							}
							// Retrieve the post ID for the product
							$productPostId = WP_Topspin::getProductPostId($sku);
							// Create a new post array for update/create
							$productPost = WP_Topspin::createProduct($sku);
							// Create if not exists
							if(!$productPostId) {
								$productPostId = wp_insert_post($productPost);
							}
							// Update if exists
							else {
								$productPost['ID'] = $productPostId;
								$productPostId = wp_update_post($productPost);
							}
							if($productPostId) {
								// Update the product post meta
								WP_Topspin::updateProductMeta($productPostId, $sku);
								// Attach this product post ID to the offer
								WP_Topspin::attachOfferProduct($offerPostId, $productPostId);
							}
						}
					}
				}
			}
			update_post_meta($offerPostId, 'topspin_offer_in_stock_quantity', $inStock);
			update_post_meta($offerPostId, 'topspin_offer_in_stock', ($inStock) ? true : false);
			return true;
		}
		return false;
	}

	/**
	 * Purges the database of stray artist posts that are not found in the global cached array
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return void
	 */
	public static function purgeStrayArtists() {
		$delete_IDs = WP_Topspin::getStrayArtists();
		if($delete_IDs) { WP_Topspin::deleteArtistIds($delete_IDs); }
	}

	/**
	 * Purges the database of stray offer posts that are not found in the global cached array
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return void
	 */
	public static function purgeStrayOffers() {
		$delete_IDs = WP_Topspin::getStrayOffers();
		if($delete_IDs) { WP_Topspin::deleteOfferIds($delete_IDs); }
	}

	/**
	 * Purges the database of stray terms that are not found in the global cached array
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @return void
	 */
	public static function purgeStrayTerms() {
		global $topspin_cached_terms;
		$terms = get_terms('spin-tags');
		// Loops through all terms in the spin tags taxonomy and delete those that are not found in the global cached array
		foreach($terms as $term) {
			if(!in_array($term->name, $topspin_cached_terms)) {
				wp_delete_term($term->term_id, 'spin-tags');
			}
		}
	}

  /**
   * Retrieves all attached images for the given offer and deletes the corresponding post and deletes the file
   *
   * @access public
   * @static
   * @param int $offerPostId        The offer post ID to retrieve attachments
   * @param array $excludes         (default: array) An array of attachment post ID to exclude
   * @return void
   */
  public static function purgeOldImages($offerPostId, $excludes=array()) {
    $attachments = WP_Topspin::getGallery($offerPostId);
    // If attachments are found, loop through and force delete all
    if($attachments && is_array($attachments) && count($attachments)) {
      foreach($attachments as $attachment) {
        if(!in_array($attachment->ID, $excludes)) {
          wp_delete_attachment($attachment->ID, true);
        }
      }
    }
  }

	/**
	 * Purges the cached prefetch files
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function purgePrefetch() {
		$files = glob(WP_Topspin::getCacheFolder() . '*');
		if(count($files)) {
			foreach($files as $file) {
				if(file_exists($file)) { unlink($file); }
			}
		}
	}

}

?>