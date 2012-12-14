<?php

global $tsQuery, $tsOffer;
$tsOffer = false;	//the currently active offer

/**
 * Retrieves the current offer
 * 
 * @access public
 * @param int $offer_ID
 * @return object|bool If the offer exists, returns the offer object or false if it doesn't exist
 */
function ts_get_offer($offer_ID) {
	$args = array(
		'offer_ID' => $offer_ID
	);
	$offerQuery = new TS_Query($args);
	if($offerQuery->have_offers()) {
		while($offerQuery->have_offers()) {
			$offerQuery->the_offer();
			return $offerQuery->offer;
		}
	}
}

/**
 * Load a template part into a template
 *
 * Note: Behaves like get_template_part()
 *
 * @return void
 */
function ts_get_template_part($args) {
	$args = func_get_args();
	$ext = '.php';
	$file = '';
	foreach($args as $key=>$slug) {
		if($key==0) { $file = $slug; }
		else { $file = sprintf('%s-%s', $file, $slug); }
	}
	// Retrieve the template path
	$path = WP_Topspin_Template::getTemplatePath();
	// Parse the defualt file name and the custom file name
	$loadFile = sprintf('%s%s', 'item', $ext);
	$file = sprintf('%s%s', $file, $ext);
	// Check if the file exists
	if(WP_Topspin_Template::fileExists($path, $file)) { $loadFile = $file; }
	// Include the file
	include(WP_Topspin_Template::getFile($loadFile));
}

/* !----- Template Tags ----- */
/**
 * Retrieves the number of columns of the current store
 *
 * @access public
 * @global object $post
 * @return int
 */
function ts_grid_columns() {
	global $post;
	$storeMeta = WP_Topspin::getStoreMeta($post->ID);
	return $storeMeta->grid_columns;
}
/**
 * Retrieves the current offer index in the TS_Query object
 *
 * @access public
 * @global object $tsQuery
 * @return int
 */
function ts_item_index() {
	global $tsQuery;
	return $tsQuery->getCurrentIndex();
}
/**
 * Retrieve the current item's column in the store page
 *
 * @access public
 * @global object $tsQuery
 * @return int
 */
function ts_item_column() {
	return ((ts_item_index() % ts_grid_columns()) + 1);
}
/**
 * Echoes the CSS class for each item
 * 
 * Applies the default "topspin-item" class and store-related CSS classes
 *
 * @access public
 * @global object $post The current store post object
 * @global object $tsOffer The current offer
 * @return void
 */
function ts_item_class() {
	global $post, $tsOffer;
	// Set the default class
	$classes = array('topspin-item');
	array_push($classes, 'topspin-item-id-' . $tsOffer->ID);
	// Retrieve store meta data
	if($post && $post->ID) {
		switch($post->post_type) {
			case TOPSPIN_CUSTOM_POST_TYPE_STORE:
				$storeMeta = WP_Topspin::getStoreMeta($post->ID);
				array_push($classes, 'topspin-item-grid-columns-'.$storeMeta->grid_columns);
				// Break if index is the end of the allowed columns
				$currentColumn = (($tsOffer->index%$storeMeta->grid_columns)+1);
				if($currentColumn==1) { array_push($classes, 'topspin-item-column-first'); }
				if($currentColumn==$storeMeta->grid_columns) { array_push($classes, 'topspin-item-column-last'); }
				break;
			case TOPSPIN_CUSTOM_POST_TYPE_OFFER:
				array_push($classes, 'topspin-item-single');
				break;
		}
	}

	// Echoes out the classes
	echo implode(' ', $classes);
}
/**
 * Checks to see if the current offer is new
 * 
 * @access public
 * @return bool
 */
function ts_is_new() {
	global $tsOffer;
	// Get the created date
	$createdDate = get_post_time('U', false, $tsOffer->ID);
	// Expiration date
	$expirationDate = $createdDate+TOPSPIN_NEW_ITEMS_TIMEOUT;
	return (time()<$expirationDate) ? true : false;
}

/**
 * Checks to see if the current offer is on sale
 * 
 * @access public
 * @return bool
 */
function ts_is_on_sale() {
	global $tsOffer;
	return WP_Topspin::isOnSale($tsOffer);
}

/**
 * Checks to see if the current offer is sold out
 *
 * @param object|int $offer (default: null)
 * @return bool
 */
function ts_is_sold_out($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { return (isset($offer->meta->in_stock) && $offer->meta->in_stock) ? false : true; }
	return true;
}

/**
 * Retrieves the offer type
 *
 * @param object|int $offer (default: null)
 * @return string
 */
function ts_get_the_offer_type($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { return $offer->offer_type; }
	else { return ''; }
}

/**
 * Checks to see if the current offer in the TS Loop has a thumbnail
 * 
 * @access public
 * @global object $offer (default: null)
 * @return bool
 */
function ts_has_thumbnail($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	return ($offer && has_post_thumbnail($offer->ID)) ? true : false;
}

/**
 * Echoes out the post thumbnail of the current offer
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @param string $size (default: full)
 * @return void
 */
function ts_the_thumbnail($size='full') {
	global $tsOffer;
	$thumb = ts_get_the_thumbnail($tsOffer,$size);
	echo $thumb;
}

	/**
	 * Returns the offer thumbnail image tag
	 * 
	 * @access public
	 * @param object|int $offer (default: null)
	 * @param string $size (default: full)
	 * @return string
	 */
	function ts_get_the_thumbnail($offer,$size='full') {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		$attr = array(
			'class' => 'topspin-item-thumbnail-image'
		);
		return get_the_post_thumbnail($offer->ID, $size, $attr);
	}


/**
 * Echoes the current offer's order number in the current store query
 *
 * @return void
 */
function ts_the_order_number() {
	echo ts_get_the_order_number();
}
	/**
	 * Retrieves the current offer's order number in the current store query
	 *
	 * @global object $tsOffer
	 * @global object $tsQuery
	 * @return int
	 */
	function ts_get_the_order_number() {
		global $tsOffer, $tsQuery;
		if($tsQuery) {
			$itemsOrder = (isset($tsQuery->query['items_order'])) ? $tsQuery->query['items_order'] : array();
			$key = array_search($tsOffer->ID, $itemsOrder);
			return $key;
		}
	}

/**
 * Echoes the current offer's ID
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @return int|bool
 */
function ts_the_ID($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	echo ts_get_the_ID($offer);
}

	/**
	 * Returns the current offer's ID
	 * 
	 * @access public
	 * @global object $tsOffer The current offer
	 * @param object|int $offer (default: null)
	 * @return int The WordPress post ID
	 */
	function ts_get_the_ID($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) { return $offer->ID; }
	}

/**
 * Echoes the current offer's campaign ID
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @return void
 */
function ts_the_offer_id() {
	echo ts_get_the_offer_id();
}

	/**
	 * Returns the current offer's campaign ID
	 * 
	 * @access public
	 * @global object $tsOffer The current offer
	 * @return int The offer's campaign ID
	 */
	function ts_get_the_offer_id() {
		global $tsOffer;
		if($tsOffer) { return $tsOffer->meta->id; }
	}

/**
 * Echoes the title of the offer
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @return void
 */
function ts_the_title() {
	echo ts_get_the_title();
}

	/**
	 * Returns the title of the offer
	 *
	 * If the offer is not set, it will return the current offer's title in the Topspin Loop
	 * 
	 * @access public
	 * @global object|int $offer (Optional) The current offer object or ID
	 * @return string The offer's post title
	 */
	function ts_get_the_title($offer=null) {
		$ID = false;
		// If it's a number
		if(is_int($offer)) { $ID = $offer; }
		// Else, an object
		else if(is_object($offer)) { $ID = $offer->ID; }
		// Else, not set
		else {
			global $tsOffer;
			if($tsOffer) { $ID = $tsOffer->ID; }
		}
		if($ID) { return get_the_title($ID); }
	}

/**
 * Echoes the content of the current offer
 * 
 * @access public
 * @return void
 */
function ts_the_content() {
	echo ts_get_the_content();
}

	/**
	 * Returns the name of the current offer
	 * 
	 * @access public
	 * @return string The offer's post content
	 */
	function ts_get_the_content() {
		global $tsOffer;
		if($tsOffer) { return $tsOffer->post_content; }
	}

/**
 * Echoes the permalink of the offer
 * 
 * @access public
 * @return void
 */
function ts_the_permalink() {
	global $tsOffer;
	echo get_permalink($tsOffer->ID);
}

	/**
	 * Returns the permalink of the offer
	 * 
	 * @access public
	 * @return string The offer's post permalink
	 */
	function ts_get_the_permalink() {
		global $tsOffer;
		$permalink = get_permalink($tsOffer->ID);
		return $permalink;
	}

/**
 * Echoes the name of the offer
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @return void
 */
function ts_the_name() {
	global $tsOffer;
	echo $tsOffer->meta->name;
}

/**
 * Echoes the price of the offer
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @return void
 */
function ts_the_price($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	echo ts_get_the_price($offer);
}

	/**
	 * Returns the price of the offer
	 * 
	 * @access public
	 * @global object $tsOffer The current offer
	 * @param object|int $offer (default: null)
	 * @return string
	 */
	function ts_get_the_price($offer=null) {
		$currency = '$';
		$price = 0;
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		// If it is a buy button
		if($offer->meta->offer_type=='buy_button') {
			$currency = Topspin_API::getCurrentSymbol($offer->meta->currency);
			$price = $offer->meta->price;
		}
		return sprintf('%s%0.2f', $currency, $price);
	}

/**
 * Echoes the offer URL of the offer
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @return void
 */
function ts_the_purchaselink() {
	global $tsOffer;
	switch($tsOffer->offer_type) {
		case 'buy_button':
			echo $tsOffer->meta->offer_url;
			break;
	}
}

function ts_prev_link() {
	global $tsOffer, $tsQuery;
	if($tsQuery->current_page>1) {
		$is_permalink = get_option('permalink_structure');
		$currentLink = get_permalink($tsQuery->query['post_ID']);
		$prevPage = $tsQuery->current_page-1;
		// Non-permalink
		$link = sprintf('%s&page=%d', $currentLink, $prevPage);
		// If is permalink, override!
		if($is_permalink) { $link = sprintf('%s%s', $currentLink, $prevPage); }
		return $link;
	}
}

function ts_next_link() {
	global $tsOffer, $tsQuery;
	if($tsQuery->current_page < $tsQuery->max_num_pages) {
		$is_permalink = get_option('permalink_structure');
		$currentLink = get_permalink($tsQuery->query['post_ID']);
		$nextPage = $tsQuery->current_page+1;
		// Non-permalink
		$link = sprintf('%s&page=%d', $currentLink, $nextPage);
		// If is permalink, override!
		if($is_permalink) { $link = sprintf('%s%s', $currentLink, $nextPage); }
		return $link;
	}
}

/**
 * Checks to see if the gallery exists for the offer
 *
 * @access public
 * @return bool
 */
function ts_have_gallery() {
	return (count(ts_gallery_images())>1) ? true : false;
}

/**
 * Retrieves the gallery images
 *
 * @access public
 * @global object $tsOffer The current offer
 * @return array An array of image objects
 */
function ts_gallery_images() {
	global $tsOffer;
	return $tsOffer->meta->campaign->product->images;
}

/**
 * Echoes the embed code for the offer
 * 
 * @access public
 * @global object $tsOffer The current offer
 * @param object|int $offer (default: null)
 * @return void
 */
function ts_the_embed_code($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	return ts_get_the_embed_code($offer);
}

	/**
	 * Returns the embed code of the offer
	 * 
	 * @access public
	 * @global object $tsOffer The current offer
	 * @param object|int $offer (default: null)
	 * @return string
	 */
	function ts_get_the_embed_code($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) { echo $offer->meta->embed_code; }
	}

/* !----- TS_Query ----- */

/**
 * Queries the given store for offers
 *
 * @package WordPress
 * @subpackage Topspin
 */
class TS_Query {

	public $query;				// holds the query that was passed to the ts_query object
	public $request;			// holds the SQL that was ran for the query
	public $found_offers;		// the total number of offers found matching the current query parameters
	public $max_num_pages;		// the total number of pages (found_offers / items_per_page)
	public $current_offer;		// index of offer currently being displayed
	public $offers;				// the offer currently being displayed
	public $offer;				// the offer currently being displayed

	/**
	 * Queries the given store for offers
	 * 
	 * #Arguments
	 * * post_ID int				The store post ID
	 * * offer_ID int				The offer post ID
	 * * artist_id int				The Topspin artist ID
	 * * items_per_page int
	 * * show_all_items bool
	 * * desc_length int
	 * * sale_tag string
	 * * grid_columns int
	 * * default_sorting string (alphabetical, chronological)
	 * * default_sorting_by string (offertype, tags, manual)
	 * * offer_type array
	 * * tags array
	 * * page int
	 *
	 * @access public
	 * @static
	 * @global object $wpdb
	 * @param array $args			An array of arguments to pass (see above)
	 * @param array $overrides		An array of arguments to pass after loading the store data
	 * @return void
	 */
	public function __construct($args=array(), $overrides=array()) {
		global $wpdb;

		// Merge defaults args
		// Set defaults
		$defaults = array(
			'post_ID' => 0,
			'offer_ID' => 0,
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
			'page' => 1
		);

		$args = array_merge($defaults, $args);

		$posts = array();
		$storeMeta = array();

		// Retrieve the store's meta if available
		if($args['post_ID']) { $storeMeta = (array) WP_Topspin::getStoreMeta($args['post_ID']); }

		// The first select statement for querying
		$sqlFirstSelectStatement = '';

		// Merge the store's meta and the args
		$args = array_merge($args, $storeMeta, $overrides);

		// Parse sorting/sorting by
		$unionSelectTags = $unionSelectOfferTypes = $unionSelectManualOrder = '';

		// Empty request
		$request = '';
		if($args['offer_ID']) {
			$_selectWhereOfferId = <<<EOD
		SELECT SQL_CALC_FOUND_ROWS up1.*,
		ut1.slug AS `tag`,
		(SELECT upm1.meta_value FROM {$wpdb->postmeta} upm1 WHERE upm1.meta_key = '%s' AND upm1.post_id = up1.ID) AS `offer_type`,
		(SELECT upm2.meta_value FROM {$wpdb->postmeta} upm2 WHERE upm2.meta_key = '%s' AND upm2.post_id = up1.ID) AS `artist_id`
		FROM {$wpdb->posts} up1
		LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
		LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
		LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
		WHERE
			1 = 1
			AND up1.post_type = '%s'
			AND up1.post_status = 'publish'
			AND up1.ID = %d
		GROUP BY up1.ID
EOD;
			$request = sprintf($_selectWhereOfferId,
				WP_Topspin::offerMetaKey('offer_type'),
				WP_Topspin::offerMetaKey('artist_id'),
				TOPSPIN_CUSTOM_POST_TYPE_OFFER,
				$args['offer_ID']
			);
		}
		else {
			switch($args['default_sorting_by']) {
				/* !----- Offer Type Sorting ----- */
				case 'offertype':
					// Set default clauses
					$_whereTagsInList = '';

					// If there are tags, create the WHERE clause for the loop
					if(count($args['tags'])) {
						$_whereTagsList = implode("','", $args['tags']);
						$_whereTagsInList = <<<EOD
			AND up1.ID IN (
				SELECT up2.ID
				FROM {$wpdb->posts} up2
				WHERE
					ut1.slug IN ('{$_whereTagsList}')
			)
EOD;
					}

					// If there are no offer types, pull all offer types
					if(!count($args['offer_type'])) {
						// Set the default subquery string
						$_selectOfferTypes = <<<EOD
		SELECT SQL_CALC_FOUND_ROWS up1.*,
		ut1.slug AS `tag`,
		(SELECT upm1.meta_value FROM {$wpdb->postmeta} upm1 WHERE upm1.meta_key = '%s' AND upm1.post_id = up1.ID) AS `offer_type`,
		(SELECT upm2.meta_value FROM {$wpdb->postmeta} upm2 WHERE upm2.meta_key = '%s' AND upm2.post_id = up1.ID) AS `artist_id`
		FROM {$wpdb->posts} up1
		LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
		LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
		LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
		WHERE
			1 = 1
			AND up1.post_type = '%s'
			AND up1.post_status = 'publish'
			%s
EOD;
	
						$sqlFirstSelectStatement = sprintf($_selectOfferTypes,
							WP_Topspin::offerMetaKey('offer_type'),
							WP_Topspin::offerMetaKey('artist_id'),
							TOPSPIN_CUSTOM_POST_TYPE_OFFER,
							$_whereTagsInList
						);
					}
					// If there are offer types
					else {
						// Set the default subquery string
						$_selectOfferTypes = <<<EOD
		SELECT %s up1.*,
		ut1.slug AS `tag`,
		(SELECT upm1.meta_value FROM {$wpdb->postmeta} upm1 WHERE upm1.meta_key = '%s' AND upm1.post_id = up1.ID) AS `offer_type`,
		(SELECT upm2.meta_value FROM {$wpdb->postmeta} upm2 WHERE upm2.meta_key = '%s' AND upm2.post_id = up1.ID) AS `artist_id`
		FROM {$wpdb->posts} up1
		LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
		LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
		LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
		WHERE
			1 = 1
			AND up1.post_type = '%s'
			AND up1.post_status = 'publish'
			AND up1.ID IN (
				SELECT upm2.post_id
				FROM {$wpdb->postmeta} upm2
				WHERE
					upm2.meta_key = '%s'
					AND upm2.meta_value = '%s'
			)
			%s
EOD;
						// Parse a SQL select statement for each offer type
						foreach($args['offer_type'] as $offer_type) {
							// If the first statement is not yet set, set it!
							if(!$sqlFirstSelectStatement) {
								$sqlFirstSelectStatement = sprintf($_selectOfferTypes,
									'SQL_CALC_FOUND_ROWS',
									WP_Topspin::offerMetaKey('offer_type'),
									WP_Topspin::offerMetaKey('artist_id'),
									TOPSPIN_CUSTOM_POST_TYPE_OFFER,
									WP_Topspin::offerMetaKey('offer_type'),
									$offer_type,
									$_whereTagsInList
								);
							}
							// If the first statement is already set, UNION!
							else {
								$unionSelectOfferTypes .= sprintf('UNION (%s)', sprintf($_selectOfferTypes,
									'',
									WP_Topspin::offerMetaKey('offer_type'),
									WP_Topspin::offerMetaKey('artist_id'),
									TOPSPIN_CUSTOM_POST_TYPE_OFFER,
									WP_Topspin::offerMetaKey('offer_type'),
									$offer_type,
									$_whereTagsInList
								));
							}
						}
					}
					break;
				/* !----- Tags Sorting ----- */
				case 'tags':
					// Set default clauses
					$_whereOfferTypesInList = '';
	
					// If there are offer types, create the WHERE clause for the loop
					if(count($args['offer_type'])) {
						$_whereOfferTypeMetaKey = WP_Topspin::offerMetaKey('offer_type');
						$_whereOfferTypesList = implode("','", $args['offer_type']);
						$_whereOfferTypesInList = <<<EOD
			AND up1.ID IN (
				SELECT upm2.post_id
				FROM {$wpdb->postmeta} upm2
				WHERE
					upm2.meta_key = '{$_whereOfferTypeMetaKey}'
					AND upm2.meta_value IN ('{$_whereOfferTypesList}')
			)
EOD;
					}
	
					// If there are no tags, pull all the tags
					if(!count($args['tags'])) {
						// Set the default subquery string
						$_selectTags = <<<EOD
		SELECT SQL_CALC_FOUND_ROWS up1.*,
		ut1.slug AS `tag`,
		(SELECT upm1.meta_value FROM {$wpdb->postmeta} upm1 WHERE upm1.meta_key = '%s' AND upm1.post_id = up1.ID) AS `offer_type`,
		(SELECT upm2.meta_value FROM {$wpdb->postmeta} upm2 WHERE upm2.meta_key = '%s' AND upm2.post_id = up1.ID) AS `artist_id`
		FROM {$wpdb->posts} up1
		LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
		LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
		LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
		WHERE
			1 = 1
			AND up1.post_type = '%s'
			AND up1.post_status = 'publish'
			%s
EOD;
	
						$sqlFirstSelectStatement = sprintf($_selectTags,
							WP_Topspin::offerMetaKey('offer_type'),
							WP_Topspin::offerMetaKey('artist_id'),
							TOPSPIN_CUSTOM_POST_TYPE_OFFER,
							$_whereOfferTypesInList
						);
					}
					// If there are tags
					else {
						// Set the default subquery string
						$_selectTags = <<<EOD
		SELECT %s up1.*,
		ut1.slug AS `tag`,
		(SELECT upm1.meta_value FROM {$wpdb->postmeta} upm1 WHERE upm1.meta_key = '%s' AND upm1.post_id = up1.ID) AS `offer_type`,
		(SELECT upm2.meta_value FROM {$wpdb->postmeta} upm2 WHERE upm2.meta_key = '%s' AND upm2.post_id = up1.ID) AS `artist_id`
		FROM {$wpdb->posts} up1
		LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
		LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
		LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
		WHERE
			1 = 1
			AND up1.post_type = '%s'
			AND up1.post_status = 'publish'
			AND ut1.slug = '%s'
			%s
EOD;
						// Parse a SQL select statement for each tag
						foreach($args['tags'] as $tag) {
							// If the first statement is not yet set, set it!
							if(!$sqlFirstSelectStatement) {
								$sqlFirstSelectStatement = sprintf($_selectTags,
									'SQL_CALC_FOUND_ROWS',
									WP_Topspin::offerMetaKey('offer_type'),
									WP_Topspin::offerMetaKey('artist_id'),
									TOPSPIN_CUSTOM_POST_TYPE_OFFER,
									$tag,
									$_whereOfferTypesInList
								);
							}
							// If the first statement is already set, UNION!
							else {
								$unionSelectTags .= sprintf('UNION (%s)', sprintf($_selectTags,
									'',
									WP_Topspin::offerMetaKey('offer_type'),
									WP_Topspin::offerMetaKey('artist_id'),
									TOPSPIN_CUSTOM_POST_TYPE_OFFER,
									$tag,
									$_whereOfferTypesInList
								));
							}
						}
					}
					break;
				/* !----- Manual Sorting ----- */
				case 'manual':
					// An array of item ID's in order
					$itemIds = (isset($args['items_order']) && count($args['items_order'])) ? $args['items_order'] : array();

					// Loop through each tag
					if(count($args['tags'])) {
						$_selectManualTags = <<<EOD
SELECT
up1.ID
FROM {$wpdb->posts} up1
LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
WHERE
	ut1.slug = '%s'
EOD;
						foreach($args['tags'] as $tag) {
							$selectManualTags = sprintf($_selectManualTags, $tag);
							$results = $wpdb->get_col($selectManualTags);
							foreach($results as $result_id) {
								// If the item ID is not yet in the order array, add it
								if(!in_array($result_id, $itemIds)) { array_push($itemIds, $result_id); }
							}
						}
					}

					$havingAnd = $findSetInList = '';
					if(count($itemIds)) {
						$findSetInList = implode(",", $itemIds);
					}

					$_selectManualOrder = <<<EOD
SELECT
	SQL_CALC_FOUND_ROWS up1.*,
	ut1.slug AS `tag`,
	(SELECT upm1.meta_value FROM {$wpdb->postmeta} upm1 WHERE upm1.meta_key = '%s' AND upm1.post_id = up1.ID) AS `offer_type`,
	(SELECT upm2.meta_value FROM {$wpdb->postmeta} upm2 WHERE upm2.meta_key = '%s' AND upm2.post_id = up1.ID) AS `artist_id`
	FROM {$wpdb->posts} up1
	LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
	LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
	LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
	WHERE
		1 = 1
		AND up1.post_type = '%s'
		AND up1.post_status = 'publish'
		AND up1.ID IN (%s)
	GROUP BY up1.ID
	ORDER BY FIND_IN_SET(ID, '%s')
EOD;
					$selectManualOrder = sprintf($_selectManualOrder,
						WP_Topspin::offerMetaKey('offer_type'),		// select meta_key as offer_tye
						WP_Topspin::offerMetaKey('artist_id'),		// select meta_key as artist_id
						TOPSPIN_CUSTOM_POST_TYPE_OFFER,				// the custom offer post type
						$findSetInList,								// where up1.ID IN list,
						$findSetInList								// order by find in set list
					);

					$sqlFirstSelectStatement = $selectManualOrder;

					break;
			}

			// Set the limit for pagination
			$limit = '';
			if($sqlFirstSelectStatement) {
				if(!$args['show_all_items'] || $args['items_per_page']>0) {
					$limitOffset = ($args['page']*$args['items_per_page']) - $args['items_per_page'];
					$limitCount = $args['items_per_page'];
					$limit = sprintf('LIMIT %d, %d', $limitOffset, $limitCount);
				}
			}

			// Group if there's an available select statement
			//$groupBy = (strlen($sqlFirstSelectStatement)) ? 'GROUP BY up1.ID' : '';

			$request = <<<EOD
{$sqlFirstSelectStatement}
{$unionSelectManualOrder}
{$unionSelectTags}
{$unionSelectOfferTypes}
{$limit}
EOD;
		}

		$offers = array();
		$offer_count = 0;
		// Make the query
		if(strlen(trim($request))) {
			$offers = $wpdb->get_results($request);
			$offer_count = $wpdb->get_var('SELECT FOUND_ROWS()');
		}
		
		// Set the query properties
		$this->query = $args;
		$this->current_offer = -1;
		$this->current_page = $args['page'];
		$this->offers = $offers;
		$this->offer = false;
		$this->offer_count = $offer_count;
		$this->request = $request;
		$this->found_offers = count($offers);
		if($args['offer_ID']) { $this->max_num_pages = 1; }
		else { $this->max_num_pages = ($args['items_per_page']>0) ? ceil($this->offer_count / $args['items_per_page']) : 1; }
	}

	/**
	 * Retrieves the current index of the ts query loop
	 * 
	 * @access public
	 * @return int
	 */
	public function getCurrentIndex() {
		return $this->current_offer;
	}

	/**
	 * Returns the next index number
	 * 
	 * @access public
	 * @return int
	 */
	public function getNextIndex() {
		return $this->getCurrentIndex()+1;
	}

	/**
	 * Checks to see if the next index exists for looping
	 * 
	 * @access public
	 * @return bool
	 */
	public function have_offers() {
		return (isset($this->offers[$this->getNextIndex()])) ? true : false;
	}
	
	/**
	 * Sets the current item to the next index in the loop
	 * 
	 * @access public
	 * @global int $tsOffer
	 * @return void
	 */
	public function the_offer() {
		global $tsOffer;
		$this->current_offer = $this->getNextIndex();
		$this->offer = $this->offers[$this->getCurrentIndex()];
		$this->offer->meta = WP_Topspin::getOfferMeta($this->offer->ID);
		$this->offer->index = $this->current_offer;
		$tsOffer = $this->offer;
	}

}

?>