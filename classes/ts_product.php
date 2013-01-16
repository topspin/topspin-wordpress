<?php

class TS_Product {

	/**
	 * Creates a new product object
	 *
	 * @access public
	 * @param object|int $product			The product post ID
	 */
	public function __construct($post) {
		// If it's a number, retrieve the psot
		if(is_numeric($post)) { $this->post = get_post($post); }
		else { $this->post = $post; }
		// Retrieve the metadata
		$this->loadMetaData();
	}
	
	/**
	 * Loads the custom meta data
	 *
	 * @access public
	 * @return void
	 */
	public function loadMetaData() {
		$data = get_post_custom($this->post->ID);
		$meta = array();
		foreach($data as $key=>$value) {
			switch($key) {
				case '_edit_lock':
					// do nothing
					break;
				case 'topspin_product_id':
				case 'topspin_product_in_stock_quantity':
				case 'topspin_product_factory_sku':
				case 'topspin_product_max_backorder_quantity':
				case 'topspin_product_artist_id':
				case 'topspin_product_artist_name':
				case 'topspin_product_reserve_quantity':
				case 'topspin_product_available':
				case 'topspin_product_product_name':
				case 'topspin_product_sold_unshipped_quantity':
				case 'topspin_product_sold_shipped_quantity':
				case 'topspin_product_poster_image':
				default:
					$meta[str_replace('topspin_', '', $key)] = $value[0];
					break;
				case 'topspin_product_weight':
				case 'topspin_product_attributes':
					$meta[str_replace('topspin_', '', $key)] = maybe_unserialize($value[0]);
					break;
			}
		}
		$this->meta = (object) $meta;
	}

}

?>