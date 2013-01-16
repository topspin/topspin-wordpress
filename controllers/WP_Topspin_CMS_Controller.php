<?php

/**
 * Handles the CMS routing to specific views
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_CMS_Controller {

	/**
	 * Displays the Topspin general settings page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function page_general() {
		include(sprintf('%s/views/pages/general.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the Topspin advanced settings page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function page_advancedSettings() {
		include(sprintf('%s/views/pages/advanced-settings.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the Topspin cache page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function page_cache() {
		include(sprintf('%s/views/pages/cache.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the Topspin cron page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function page_cron() {
		include(sprintf('%s/views/pages/cron.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the Topspin menus page
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function page_menus() {
		include(sprintf('%s/views/pages/menus.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the store settings metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_store_settings($post) {
		include(sprintf('%s/views/metabox/store/settings.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the store featured metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_store_featured($post) {
		include(sprintf('%s/views/metabox/store/featured.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the store preview metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_store_preview($post) {
		include(sprintf('%s/views/metabox/store/preview.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the store offer types metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_store_offertypes($post) {
		include(sprintf('%s/views/metabox/store/offertypes.php', TOPSPIN_PLUGIN_PATH));
	}
	
	/**
	 * Displays the store tags metabox content
	 * 
	 * @access public
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_store_tags($post) {
		include(sprintf('%s/views/metabox/store/tags.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the offer sync metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_offer_sync($post) {
		include(sprintf('%s/views/metabox/offer/sync.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the offer poster image metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_offer_poster_image($post) {
		global $tsOffer;
		$tsOffer = ts_get_offer($post->ID);
		include(sprintf('%s/views/metabox/offer/poster-image.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the offer content metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_offer_content($post) {
		global $tsOffer;
		$tsOffer = ts_get_offer($post->ID);
		include(sprintf('%s/views/metabox/offer/content.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the offer spin tags metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_offer_spin_tags($post) {
		global $tsOffer;
		$tsOffer = ts_get_offer($post->ID);
		include(sprintf('%s/views/metabox/offer/spin-tags.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Displays the product inventory metabox content
	 * 
	 * @access public
	 * @static
	 * @param object $post
	 * @return void
	 */
	public static function topspin_metabox_product_inventory($post) {
		global $tsProduct;
		$tsProduct = ts_get_product($post->ID);
		include(sprintf('%s/views/metabox/product/inventory.php', TOPSPIN_PLUGIN_PATH));
	}
	/**
	 * Registers the custom post types
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function topspin_register_post_type() {
		$showInMenu = (TOPSPIN_GROUP_PANELS) ? false : true;
		$showInAdminBar = (TOPSPIN_GROUP_PANELS) ? false : true;
	
		// Register Artists
		$artistLabels = array(
			'name' => 'Artists',
			'singular_name' => 'Artist',
			'add_new' => 'Add Artist',
			'add_new_item' => 'Add New Artist',
			'edit_item' => 'Edit Artist',
			'new_item' => 'New Artist',
			'all_items' => 'All Artists',
			'view_item' => 'View Artist',
			'search_items' => 'Search Artists',
			'not_found' =>  'No artists found',
			'not_found_in_trash' => 'No artists found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Artists'
		);
		$artistArgs = array(
			'labels' => $artistLabels,
			'public' => false,
			'public_queryable' => false,
			'show_ui' => false,
			'show_in_menu' => false,
			'show_in_admin_bar' => false,
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'thumbnail')
		);
		register_post_type(TOPSPIN_CUSTOM_POST_TYPE_ARTIST, $artistArgs);
		// Register Offers
		$labels = array(
			'name' => 'Offers',
			'singular_name' => 'Offer',
			'add_new' => 'Add Offer',
			'add_new_item' => 'Add New Offer',
			'edit_item' => 'Edit Offer',
			'new_item' => 'New Offer',
			'all_items' => 'All Offers',
			'view_item' => 'View Offer',
			'search_items' => 'Search Offers',
			'not_found' =>  'No offers found',
			'not_found_in_trash' => 'No offers found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Offers'
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'public_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => $showInMenu,
			'show_in_admin_bar' => $showInAdminBar,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'editor', 'thumbnail')
		);
		register_post_type(TOPSPIN_CUSTOM_POST_TYPE_OFFER, $args);
		// Register Stores
		$labels = array(
			'name' => 'Stores',
			'singular_name' => 'Store',
			'add_new' => 'Add Store',
			'add_new_item' => 'Add New Store',
			'edit_item' => 'Edit Store',
			'new_item' => 'New Store',
			'all_items' => 'All Stores',
			'view_item' => 'View Store',
			'search_items' => 'Search Stores',
			'not_found' =>  'No stores found',
			'not_found_in_trash' => 'No stores found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Stores'
		);	
		$args = array(
			'labels' => $labels,
			'public' => true,
			'public_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => $showInMenu,
			'show_in_admin_bar' => $showInAdminBar,
			'query_var' => true,
			'rewrite' => '/',
			'capability_type' => 'page',
			'has_archive' => true,
			'hierarchical' => true,
			'menu_position' => null,
			'supports' => array('title', 'page-attributes')
		);
		register_post_type(TOPSPIN_CUSTOM_POST_TYPE_STORE, $args);
		// Register Products
		$productLabels = array(
			'name' => 'Products',
			'singular_name' => 'Product',
			'add_new' => 'Add Product',
			'add_new_item' => 'Add New Product',
			'edit_item' => 'Edit Product',
			'new_item' => 'New Product',
			'all_items' => 'All Products',
			'view_item' => 'View Product',
			'search_items' => 'Search Products',
			'not_found' =>  'No products found',
			'not_found_in_trash' => 'No products found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Product'
		);
		$productArgs = array(
			'labels' => $productLabels,
			'public' => false,
			'public_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => $showInMenu,
			'show_in_admin_bar' => $showInAdminBar,
			'query_var' => false,
			'rewrite' => false,
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'thumbnail')
		);
		register_post_type(TOPSPIN_CUSTOM_POST_TYPE_PRODUCT, $productArgs);
		// Do action
		do_action('topspin_register_post_types');
	}
	/**
	 * Registers the custom taxonomies
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function topspin_register_taxonomies() {
		$labels = array(
			'name' => 'Spin Tags',
			'singular_name' => 'Spin Tag'
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => false,
			'show_ui' => false,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'show_admin_column' => false,
			'query_var' => true
		);
		register_taxonomy('spin-tags', array(TOPSPIN_CUSTOM_POST_TYPE_OFFER), $args);
	}

}

?>