<?php

// Actions
add_action('admin_bar_menu',				array('WP_Topspin_Hooks_Controller', 'adminBarMenu'), 100);
add_action('add_meta_boxes',				array('WP_Topspin_Hooks_Controller', 'addMetaBoxes'));
add_action('admin_enqueue_scripts',			array('WP_Topspin_Hooks_Controller', 'adminEnqueueScripts'));
add_action('admin_init',					array('WP_Topspin_HookS_Controller', 'adminInit'));
add_action('admin_menu',					array('WP_Topspin_Hooks_Controller', 'adminMenu'));
add_action('after_setup_theme',				array('WP_Topspin_Hooks_Controller', 'afterSetupTheme'));
add_action('init',							array('WP_Topspin_Hooks_Controller', 'init'));
add_action('parse_query',					array('WP_Topspin_Hooks_Controller', 'parse_query'));
add_action('save_post',						array('WP_Topspin_Hooks_Controller', 'savePost'));
add_action('wp_enqueue_scripts',			array('WP_Topspin_Hooks_Controller', 'wpEnqueueScripts'));
add_action('wp_head',						array('WP_Topspin_Hooks_Controller', 'wpHead'));

// Filters
add_filter('the_content',					array('WP_Topspin_Hooks_Controller', 'theContent'));

/**
 * Handles WordPress hooks
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Hooks_Controller {

	/**
	 * Adds Topspin metaboxes
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function addMetaBoxes() {
		/* !----- Store Metaboxes ----- */
		// Store Settings
		add_meta_box('topspin-store-settings', 'Store Settings', array('WP_Topspin_CMS_Controller', 'topspin_metabox_store_settings'), TOPSPIN_CUSTOM_POST_TYPE_STORE, 'normal', 'high');
		// Featured Items
		add_meta_box('topspin-store-featured', 'Featured Items', array('WP_Topspin_CMS_Controller', 'topspin_metabox_store_featured'), TOPSPIN_CUSTOM_POST_TYPE_STORE, 'normal', 'high');	
		// Preview
		add_meta_box('topspin-store-preview', 'Preview', array('WP_Topspin_CMS_Controller', 'topspin_metabox_store_preview'), TOPSPIN_CUSTOM_POST_TYPE_STORE, 'normal', 'high');
		// Offer Types
		add_meta_box('topspin-store-offertypes', 'Offer Types', array('WP_Topspin_CMS_Controller', 'topspin_metabox_store_offertypes'), TOPSPIN_CUSTOM_POST_TYPE_STORE, 'side', 'default');
		// Tags
		add_meta_box('topspin-store-tags', 'Tags', array('WP_Topspin_CMS_Controller', 'topspin_metabox_store_tags'), TOPSPIN_CUSTOM_POST_TYPE_STORE, 'side', 'default');
		/* !----- Offer Metaboxes ----- */
		// Sync
		add_meta_box('topspin-offer-sync', 'Sync', array('WP_Topspin_CMS_Controller', 'topspin_metabox_offer_sync'), TOPSPIN_CUSTOM_POST_TYPE_OFFER, 'side', 'default');
	}
	
	/**
	 * Modifies the WordPress admin bar
	 * 
	 * @global object $wp_admin_bar
	 * @access public
	 * @static
	 * @return void
	 */
	public static function adminBarMenu() {
		global $wp_admin_bar;
		// Add menu parent
		$wp_admin_bar->add_node(array(
			'id' => 'topspin_node',
			'title' => sprintf('<img src="%s/resources/images/logo-adminbar.png" alt="Topspin" />', TOPSPIN_PLUGIN_URL),
			'href' => admin_url('admin.php?page=topspin/page/general')
		));
	}
	/**
	 * Enqueues CSS and JS for admins
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function adminEnqueueScripts() {
		// Register
		wp_register_script('topspin-admin', sprintf('%s/resources/js/topspin.admin.js', TOPSPIN_PLUGIN_URL), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));
		wp_register_style('topspin-admin', sprintf('%s/resources/css/topspin.admin.css', TOPSPIN_PLUGIN_URL));
		// Enqueue
		wp_enqueue_script('topspin-admin');
		wp_enqueue_style('topspin-admin');
	}
	/**
	 * Initializes in the admin section
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function adminInit() {
		// Register settings
		register_setting('topspin_general', 'topspin_api_username');
		register_setting('topspin_general', 'topspin_api_key');
		register_setting('topspin_general', 'topspin_default_store_page_id');
		register_setting('topspin_general', 'topspin_template_mode');
		register_setting('topspin_general', 'topspin_new_items_timeout');
		register_setting('topspin_general', 'topspin_artist_ids');
		register_setting('topspin_general', 'topspin_post_type_artist');
		register_setting('topspin_general', 'topspin_post_type_offer');
		register_setting('topspin_general', 'topspin_post_type_store');
		register_setting('topspin_advanced_settings', 'topspin_api_prefetching');
		// Do action
		do_action('topspin_post_action');
		// Load constants
		define('TOPSPIN_MENU_ACTIVATED', get_option('topspin_menu_activated'));
	}
	/**
	 * Initializes admin menus
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function adminMenu() {
		// Add the parent Topspin menu page
		add_menu_page('Topspin', 'Topspin', 'edit_posts', 'topspin/page/general', array('WP_Topspin_CMS_Controller', 'page_general'));
		// Add submenus
		add_submenu_page('topspin/page/general', 'Settings', 'Settings', 'edit_posts', 'topspin/page/general', array('WP_Topspin_CMS_Controller', 'page_general'));
		if(TOPSPIN_API_VERIFIED && TOPSPIN_POST_TYPE_DEFINED) {
			add_submenu_page('topspin/page/general', 'Advanced Settings', 'Advanced Settings', 'edit_posts', 'topspin/page/advanced-settings', array('WP_Topspin_CMS_Controller', 'page_advancedSettings'));
			add_submenu_page('topspin/page/general', 'Cache', 'Cache', 'edit_posts', 'topspin/page/cache', array('WP_Topspin_CMS_Controller', 'page_cache'));
			add_submenu_page('topspin/page/general', 'Cron', 'Cron', 'edit_posts', 'topspin/page/cron', array('WP_Topspin_CMS_Controller', 'page_cron'));
			add_submenu_page('topspin/page/general', 'Nav Menus', 'Nav Menus', 'edit_posts', 'topspin/page/menus', array('WP_Topspin_CMS_Controller', 'page_menus'));
		}
	}
	/**
	 * Adds additional image sizes
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function afterSetupTheme() {
		// Create the image sizes
		add_image_size('wp-list-table-thumb', 100, 100, 1);
		add_image_size('topspin-default-grid-thumb', 205, 205, 1);
		add_image_size('topspin-default-single-thumb', 250, 250, 1);	
	}
	/**
	 * Initializes the plugin
	 *
	 * Registers the custom offer post type and load topspin constants
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function init() {
		// Load constants
		define('TOPSPIN_CUSTOM_POST_TYPE_ARTIST', apply_filters('topspin_custom_post_type_artist', ($artistPostType=get_option('topspin_post_type_artist')) ? $artistPostType : 'topspin-artist'));			// internal post type
		define('TOPSPIN_CUSTOM_POST_TYPE_STORE', apply_filters('topspin_custom_post_type_store', ($storePostType=get_option('topspin_post_type_store')) ? $storePostType : 'topspin-store'));				// public post type
		define('TOPSPIN_CUSTOM_POST_TYPE_OFFER', apply_filters('topspin_custom_post_type_offer', ($offerPostType=get_option('topspin_post_type_offer')) ? $offerPostType : 'topspin-offer'));				// public post type
		// Register the custom post types
		WP_Topspin_CMS_Controller::topspin_register_post_type();
		// Register the custom taxonomies
		WP_Topspin_CMS_Controller::topspin_register_taxonomies();
		// Set globals
		global $topspin_artist_ids;
		// If the artist Ids is not yet set, retrieve it from the database
		if(!$topspin_artist_ids) { $topspin_artist_ids = get_option('topspin_artist_ids'); }
		// Load constants
		define('TOPSPIN_API_USERNAME', get_option('topspin_api_username'));
		define('TOPSPIN_API_KEY', get_option('topspin_api_key'));
		define('TOPSPIN_DEFAULT_STORE_PAGE_ID', get_option('topspin_default_store_page_id'));
		define('TOPSPIN_TEMPLATE_MODE', get_option('topspin_template_mode'));
		define('TOPSPIN_NEW_ITEMS_TIMEOUT', get_option('topspin_new_items_timeout'));
		define('TOPSPIN_API_PREFETCHING', get_option('topspin_api_prefetching'));
		// Instantiate some classes
		global $topspin_artist_api, $topspin_store_api;
		$topspin_artist_api = new Topspin_Artist_API(TOPSPIN_API_USERNAME, TOPSPIN_API_KEY);
		$topspin_store_api = new Topspin_Store_API(TOPSPIN_API_USERNAME, TOPSPIN_API_KEY);
		// Load other constants
		define('TOPSPIN_API_VERIFIED', $topspin_store_api->verifyCredentials());
		define('TOPSPIN_POST_TYPE_DEFINED', WP_Topspin::hasPostTypes());
		define('TOPSPIN_HAS_ARTISTS', WP_Topspin::hasArtists());
		define('TOPSPIN_HAS_SYNCED_ARTISTS', WP_Topspin::hasSyncedArtists());
		// Initialize cron schedules
		WP_Topspin_Cron::initSchedules();
		// Topspin is finished initializing
		do_action('topspin_init');
	}
	/**
	 * Initial parse query hook
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function parse_query($query) {
		// If it's the store archive (store front page)
		if(is_post_type_archive(TOPSPIN_CUSTOM_POST_TYPE_STORE)) {
			// If a default store page ID is set, alter the query
			if(TOPSPIN_DEFAULT_STORE_PAGE_ID>0) {
				$query->set('p', TOPSPIN_DEFAULT_STORE_PAGE_ID);
			}
		}
	}
	/**
	 * Hook callback for when a post gets saved
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function savePost($post_ID) {
		// If the post type is set
		if(isset($_POST['post_type'])) {
			// If the post is a store
			if(TOPSPIN_CUSTOM_POST_TYPE_STORE==$_POST['post_type']) {
				// If the user can edit the post
				if(current_user_can('edit_post', $post_ID)) {
					// Do nothing if autosave
					if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
					// Verify nonce
					if(!wp_verify_nonce($_POST['topspin_nonce'], plugin_basename(TOPSPIN_PLUGIN_FILE))) return;
					WP_Topspin::updateStoreMeta($post_ID, $_POST['topspin']);
				}
			}
		}
	}
	/**
	 * Overrides the_content() with the TopSpin default template
	 * 
	 * @access public
	 * @static
	 * @global object $post
	 * @global object $tsQuery
	 * @param mixed $content
	 * @return void
	 */
	public static function theContent($content) {
		// Cast globals
		global $post, $tsQuery;
		// Switch based on post type
		switch($post->post_type) {
			// Override the content for stores
			case TOPSPIN_CUSTOM_POST_TYPE_STORE:	
				$page = (get_query_var('page')) ? get_query_var('page') : 1;
				$content = '';
				// Get menus
				if(get_option('topspin_menu_activated')) { $content .= WP_Topspin_Template::getContents('menus.php'); }
				// Get featured
				$featuredItems = WP_Topspin::getFeaturedItems($post->ID);
				if($featuredItems && count($featuredItems)) {
					foreach($featuredItems as $offer_ID) {
						$args = array(
							'offer_ID' => $offer_ID
						);
						$tsQuery = new TS_Query($args);
						$vars = array(
							'args' => $args,
							'tsQuery' => $tsQuery
						);
						$content .= WP_Topspin_Template::getContents('featured.php', $vars);
					}
				}
				// Query for the store items
				$args = array(
					'post_ID' => $post->ID,
					'page' => $page
				);
				$tsQuery = new TS_Query($args);
				$vars = array(
					'args' => $args,
					'tsQuery' => $tsQuery
				);
				$content .= WP_Topspin_Template::getContents('index.php', $vars);
				// Retrieve pagination
				if($tsQuery->max_num_pages>1) { $content .= WP_Topspin_Template::getContents('pager.php', $vars); }
				break;
			// Override the content for offers
			case TOPSPIN_CUSTOM_POST_TYPE_OFFER:
				$content = '';
				// Get menus
				if(get_option('topspin_menu_activated')) { $content .= WP_Topspin_Template::getContents('menus.php'); }
				// Query for the store item
				$args = array(
					'offer_ID' => $post->ID
				);
				$tsQuery = new TS_Query($args);
				$vars = array(
					'args' => $args,
					'tsQuery' => $tsQuery
				);
				$content .= WP_Topspin_Template::getContents('single.php', $vars);
				break;
		}
		return $content;
	}
	
	/**
	 * Enqueues scripts and styles in the frontend
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function wpEnqueueScripts() {
		// Enqueue core JS/CSS
		wp_register_script('jquery-fancybox', sprintf('%s/resources/js/fancybox/jquery.fancybox.pack.js', TOPSPIN_PLUGIN_URL), array('jquery'));
		wp_register_script('topspin-core', sprintf('http://cdn.topspin.net/javascripts/topspin_core.js?aId=%d&amp;timestamp=%d', WP_Topspin::getStoreArtistId(), time()));
		wp_register_script('topspin', sprintf('%s/resources/js/topspin.js', TOPSPIN_PLUGIN_URL), array('jquery', 'jquery-fancybox', 'topspin-core'));
		wp_enqueue_script('topspin');
		// Enqueue template JS/CSS
		wp_register_style('fancybox', sprintf('%s/resources/js/fancybox/jquery.fancybox.css', TOPSPIN_PLUGIN_URL));
		wp_register_style('topspin-template', WP_Topspin_Template::getFileUrl('style.css'));
		wp_enqueue_style('fancybox');
		wp_enqueue_style('topspin-template');
	}
	/**
	 * Callback on the HTML head
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public static function wpHead() {
		include(sprintf('%s/views/wp_head.php', TOPSPIN_PLUGIN_PATH));
	}

	/**
	 * Handles custom column output for each post
	 *
	 * @access public
	 * @static
	 * @param string $column
	 * @param int $post_ID
	 * @return void
	 */
	public static function offerCustomColumnOutput($column, $post_ID) {
		switch($column) {
			case 'id':
				ts_the_ID();
				break;
			case 'post-thumbnail':
				if(ts_has_thumbnail()) { echo ts_get_the_thumbnail($post_ID,'wp-list-table-thumb'); }
				break;
			case 'price':
				ts_the_price($post_ID);
				break;
		}
	}
	
	/**
	 * Handles custom column output for each post
	 *
	 * @access public
	 * @static
	 * @param string $column
	 * @param int $post_ID
	 * @return void
	 */
	public static function artistCustomColumnOutput($column, $post_ID) {
		switch($column) {
			case 'id':
				the_ID();
			case 'post-thumbnail':
				if(has_post_thumbnail($post_ID)) { the_post_thumbnail('wp-list-table-thumb'); }
				break;
			case 'price':
				ts_the_price($post_ID);
				break;
		}
	}

	/**
	 * Modifies the offer custom columns
	 *
	 * @access public
	 * @static
	 * @param array $columns
	 * @return array
	 */
	function offerCustomColumns($columns) {
		$cols = array(
			'cb' => $columns['cb'],
			'id' => 'ID',
			'post-thumbnail' => 'Thumbnail',
			'title' => $columns['title'],
			'price' => 'Price',
			'date' => $columns['date']
		);
		return $cols;
	}
	
	/**
	 * Modifies the artists custom columns
	 *
	 * @access public
	 * @static
	 * @param array $columns
	 * @return array
	 */
	function artistCustomColumns($columns) {
		$cols = array(
			'cb' => $columns['cb'],
			'id' => 'ID',
			'post-thumbnail' => 'Thumbnail',
			'title' => $columns['title'],
			'date' => $columns['date']
		);
		return $cols;
	}

}

?>