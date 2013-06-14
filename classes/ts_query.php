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
 * Retrieves the current product
 *
 * @access public
 * @param int $product_ID
 * @return object|bool If the product ecists, returns the product object or false if it doesn't exist
 */
function ts_get_product($product_ID) {
	$tsProduct = new TS_Product($product_ID);
	return ($tsProduct) ? $tsProduct : false;
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
	if($tsQuery && is_object($tsQuery)) { return $tsQuery->getCurrentIndex(); }
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
 * Checks to see if the offer is new. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return bool						True if the item is new.
 */
function ts_is_new($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) {
		// Get the created date
		$createdDate = get_post_time('U', false, $offer->ID);
		// Expiration date
		$expirationDate = $createdDate + TOPSPIN_NEW_ITEMS_TIMEOUT;
		return (time() < $expirationDate) ? true : false;
	}
	return false;
}

/**
 * Checks to see if the offer is on sale. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return bool						True if the item is on sale.
 */
function ts_is_on_sale($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { return WP_Topspin::isOnSale($tsOffer); }
	else { return false; }
}

/**
 * Checks to see if the offer is sold out. If no offer is specified, it will default to the current offer in The Topspin Loop.
 *
 * @access public
 * @param object|int $offer			(default: null)
 * @return bool						True if the item is sold out.
 */
function ts_is_sold_out($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) {
		$soldOut = (isset($offer->meta->in_stock) && $offer->meta->in_stock) ? false : true; 
		if($soldOut && $prodMeta = ts_get_product($offer->meta->product_post_id)){
			if( is_numeric ($prodMeta->meta->product_max_backorder_quantity) 
				&& is_numeric ($prodMeta->meta->product_sold_unshipped_quantity)
				&& ($prodMeta->meta->product_max_backorder_quantity-$prodMeta->meta->product_sold_unshipped_quantity)>0	)
			{
				$soldOut = false;
			}
		}
		return $soldOut;
	}
	return true;
}

/**
 * Checks to see if the offer is visible or not.
 *
 * @access public
 * @param object|int $offer			(default: null)
 * @return bool						True if the item is visible in the current store.
 */
function ts_is_visible($offer=null) {
	global $tsQuery;
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { return (isset($offer->is_visible) && $offer->is_visible) ? true : false; }
	return true;
}

/**
 * Retrieves the offer type. If no offer is specified, it will default to the current offer in The Topspin Loop.
 *
 * @access public
 * @param object|int $offer			(default: null)
 * @return string					The current offer type.
 */
function ts_get_the_offer_type($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { return $offer->offer_type; }
	return '';
}

/**
 * Checks to see if the offer has a thumbnail. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return bool						True if the offer has a thumbnail.
 */
function ts_has_thumbnail($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { return has_post_thumbnail($offer->ID); }
	return false;
}

/**
 * Echoes the current offer's thumbnail. This tag must be used within The Topspin Loop.
 *
 * @access public
 * @global object $tsOffer			The current offer
 * @param string $size				(default: full)
 * @return void
 */
function ts_the_thumbnail($size='full') {
	global $tsOffer;
	$thumb = ts_get_the_thumbnail($tsOffer, $size);
	echo $thumb;
}

	/**
	 * Retrieves the offer's thumbnail image tag.
	 * 
	 * @access public
	 * @param object|int $offer		(default: null)
	 * @param string $size			(default: full)
	 * @return string				The offer's thumbnail image tag.
	 */
	function ts_get_the_thumbnail($offer, $size='full') {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) {
			$attr = array(
				'class' => 'topspin-item-thumbnail-image'
			);
			return get_the_post_thumbnail($offer->ID, $size, $attr);
		}
		return '';
	}

/**
 * Echoes the current offer's order number in the current store query
 *
 * @access public
 * @return void
 */
function ts_the_order_number() {
	echo ts_get_the_order_number();
}
	/**
	 * Retrieves the current offer's order number in the current store query.
	 *
	 * @access public
	 * @global object $tsOffer
	 * @global object $tsQuery
	 * @return int					The current offer's order number in the store query.
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
 * Echoes the offer's post ID. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_ID($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_ID($offer); }
}

	/**
	 * Retrieve the offer's post ID. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return int|bool					The offer's post ID or false on failure.
	 */
	function ts_get_the_ID($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) { return $offer->ID; }
		return false;
	}

/**
 * Echoes the offer's campaign ID. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_offer_id($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_offer_id($offer); }
}

	/**
	 * Retrieve the offer's campaign ID. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return int|bool					The offer's campaign ID or false on failure.
	 */
	function ts_get_the_offer_id($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) { return $offer->meta->id; }
		return false;
	}

/**
 * Echoes the offer's title. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_title($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_title($offer); }
}

	/**
	 * Retrieve the offer's title. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return string					The offer's title.
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
 * Echoes the offer's content/description. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_content($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_content($offer); }
}

	/**
	 * Retrieve the offer's content/description. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return string					The offer's content/description.
	 */
	function ts_get_the_content($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) { return $offer->post_content; }
	}

/**
 * Echoes the offer's permalink. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_permalink($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_permalink($offer); }
}

	/**
	 * Retrieve the offer's permalink. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return string					The offer's permalink.
	 */
	function ts_get_the_permalink($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) { return get_permalink($offer->ID); }
	}

/**
 * Echoes the offer's name. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_name($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo $offer->meta->name; }
}

/**
 * Echoes the offer's price. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_price($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_price($offer); }
}

	/**
	 * Retrieve the offer's price. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return string					The offer's price.
	 */
	function ts_get_the_price($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		// Set the defaults
		$currency = '$';
		$price = 0;
		// If it is a buy button
		if($offer->meta->offer_type=='buy_button') {
			$currency = Topspin_API::getCurrentSymbol($offer->meta->currency);
			$price = $offer->meta->price;
		}
		return sprintf('%s%0.2f', $currency, $price);
	}

/**
 * Echoes the offer's purchase link. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_purchaselink($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_purchaselink($offer); }
}

	/**
	 * Retrieve the offer's purchase link. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return string					The offer's purchase link.
	 */
	function ts_get_the_purchaselink($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) {
			switch($offer->offer_type) {
				case 'buy_button':
					return $offer->meta->offer_url;
					break;
			}
		}
	}

/**
 * Retrieve the previous link for the current store. This tag must be used within The Topsin Loop.
 *
 * @access public
 * @global object $tsOffer
 * @global object $tsQuery
 * @return string					A URL for the previous page for the store query.
 */
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
	return '';
}

/**
 * Retrieve the next link for the current store. This tag must be used within The Topsin Loop.
 *
 * @access public
 * @global object $tsOffer
 * @global object $tsQuery
 * @return string					A URL for the next page for the store query.
 */
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
	return '';
}

/**
 * Checks to see if the gallery exists for the offer. This tag must be used within The Topspin Loop.
 *
 * @access public
 * @return bool						True if the current offer has more than 1 image.
 */
function ts_have_gallery() {
	return (count(ts_gallery_images())>1) ? true : false;
}

/**
 * Retrieve the gallery images. This tag must be used within The Topspin Loop.
 *
 * @access public
 * @global object $tsOffer			The current offer
 * @return array|bool				An array of image objects or false on failure.
 */
function ts_gallery_images() {
	global $tsOffer;
	if($tsOffer) { return isset($tsOffer->meta->campaign->product->images) ? $tsOffer->meta->campaign->product->images : false; }
	return false;
}

/**
 * Echoes the offer's embed code. If no offer is specified, it will default to the current offer in The Topspin Loop.
 * 
 * @access public
 * @param object|int $offer			(default: null)
 * @return void
 */
function ts_the_embed_code($offer=null) {
	if(!$offer) {
		global $tsOffer;
		$offer = $tsOffer;
	}
	else if(is_int($offer)) { $offer = ts_get_offer($offer); }
	if($offer) { echo ts_get_the_embed_code($offer); }
}

	/**
	 * Retrieve the offer's embed code. If no offer is specified, it will default to the current offer in The Topspin Loop.
	 * 
	 * @access public
	 * @param object|int $offer			(default: null)
	 * @return string					The offer's embed code.
	 */
	function ts_get_the_embed_code($offer=null) {
		if(!$offer) {
			global $tsOffer;
			$offer = $tsOffer;
		}
		else if(is_int($offer)) { $offer = ts_get_offer($offer); }
		if($offer) { return $offer->meta->embed_code; }
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
	 * * show_hidden bool			Shows hidden products? (applies to manual sorting only)
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
			'page' => 1,
			'show_hidden' => (is_admin()) ? true : false
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
	(SELECT upm2.meta_value FROM {$wpdb->postmeta} upm2 WHERE upm2.meta_key = '%s' AND upm2.post_id = up1.ID) AS `artist_id`,
	(SELECT upm3.meta_value FROM {$wpdb->postmeta} upm3 WHERE upm3.meta_key = '%s' AND upm3.post_id = up1.ID) AS `is_visible`
	FROM {$wpdb->posts} up1
	LEFT JOIN {$wpdb->term_relationships} utr1 ON up1.ID = utr1.object_id
	LEFT JOIN {$wpdb->term_taxonomy} utt1 ON utr1.term_taxonomy_id = utt1.term_taxonomy_id
	LEFT JOIN {$wpdb->terms} ut1 ON utt1.term_id = ut1.term_id
	WHERE
		1 = 1
		AND up1.post_type = '%s'
		AND up1.post_status = 'publish'
		%s
	GROUP BY up1.ID
	%s
	ORDER BY FIND_IN_SET(ID, '%s')
EOD;
					$selectManualOrder = sprintf($_selectManualOrder,
						WP_Topspin::offerMetaKey('offer_type'),													// select meta_key as offer_tye
						WP_Topspin::offerMetaKey('artist_id'),													// select meta_key as artist_id
						WP_Topspin::offerMetaKey(sprintf('%d_visible', $args['post_ID'])),						// select topspin is visible  where topspin store visible meta key
						TOPSPIN_CUSTOM_POST_TYPE_OFFER,															// the custom offer post type
						($findSetInList) ? sprintf('AND up1.ID IN (%s)', $findSetInList) : '',					// where up1.ID IN list,
						($args['show_hidden']) ? '' : 'HAVING `is_visible` IN(true, 1, \'true\')',				// having show hidden?
						$findSetInList																			// order by find in set list
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
		// Retrieve the gallery
		$this->offer->gallery = WP_Topspin::getGallery($this->offer->ID);
		// Map the Topspin structure with the gallery structure
		$this->offer->meta->campaign->product->images = array();
		if(count($this->offer->gallery)) {
  		foreach($this->offer->gallery as $image) {
    		$imageSrc = wp_get_attachment_image_src($image->ID, 'full');
    		$image = array(
      		'source_url' => $imageSrc[0],
      		'small_url' => $imageSrc[0],
      		'medium_url' => $imageSrc[0],
      		'large_url' => $imageSrc[0]
    		);
    		array_push($this->offer->meta->campaign->product->images, (object) $image);
  		}
		}
		$tsOffer = $this->offer;
	}

}

?>