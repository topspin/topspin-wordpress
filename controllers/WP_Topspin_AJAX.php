<?php

add_action('wp_ajax_topspin_store_preview_update',			array('WP_Topspin_AJAX', 'previewUpdate'));
add_action('wp_ajax_topspin_view_more',						array('WP_Topspin_AJAX', 'viewMore'));
add_action('wp_ajax_nopriv_topspin_view_more',				array('WP_Topspin_AJAX', 'viewMore'));
add_action('wp_ajax_topspin_resync_offer',					array('WP_Topspin_AJAX', 'resyncOffer'));
add_action('wp_ajax_topspin_resync_offer_inventory',		array('WP_Topspin_AJAX', 'resyncOfferInventory'));

/**
 * Handles WP-AJAX callbacks
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_AJAX {

	/**
	 * Retrieves the admin preview grid
	 * 
	 * @access public
	 * @static
	 * @return string A JSON-encoded string
	 */
	public static function previewUpdate() {

		$ret = array('error'=>1);

		$settings = (isset($_GET['settings'])) ? $_GET['settings'] : array();

		// Set defaults
		$defaults = array(
			'items_per_page' => -1,
			'show_all_items' => true,
			'default_sorting' => (isset($settings['default_sorting'])) ? $settings['default_sorting'] : null,
			'default_sorting_by' => (isset($settings['default_sorting_by'])) ? $settings['default_sorting_by'] : null,
			'tags' => (isset($settings['tags'])) ? $settings['tags'] : null,
			'offer_type' => (isset($settings['offer_type'])) ? $settings['offer_type'] : null
		);

		global $tsQuery;
		$tsQuery = new TS_Query($settings, $defaults);

		if($tsQuery->have_offers()) {
	
			// Make the HTML
			ob_start();
			include(TOPSPIN_PLUGIN_PATH . '/views/metabox/store/preview/grid.php');
			$html = ob_get_contents();
			ob_end_clean();

			// Create the response
			$ret = array(
				'status' => 'success',
				'response' => $html
			);
		}
	
		echo json_encode($ret);
		die();
	
	}
	/**
	 * Retrieves the lightbox content for the offer
	 * 
	 * @access public
	 * @static
	 * @return string A JSON-encoded string
	 */
	public static function viewMore() {
		$ajaxResponse = new TS_AjaxResponse();
		$offer_id = esc_attr($_GET['offer_id']);
		// Make the query
		$args = array(
			'offer_ID' => $offer_id
		);
		$tsQuery = new TS_Query($args);
		ob_start();
		include(WP_Topspin_Template::getFile('lightbox.php'));
		$response = ob_get_contents();
		ob_end_clean();
		// Update the AJAX Response object
		$ajaxResponse->setStatus('success');
		$ajaxResponse->setResponse($response);
		echo $ajaxResponse->output();
		die();
	}
	/**
	 * Re-syncs the given offer
	 * 
	 * @access public
	 * @static
	 * @return string A JSON-encoded string
	 */
	public static function resyncOffer() {
		$ajaxResponse = new TS_AjaxResponse();
		if($_SERVER['REQUEST_METHOD']=='POST' && current_user_can('edit_posts')) {
			$offer_id = esc_attr($_POST['offer_id']);
			$success = WP_Topspin_Cache::syncOffersSingle($offer_id);
			if($success) { $ajaxResponse->setStatus('success'); }
		}
		echo $ajaxResponse->output();
		die();
	}
	/**
	 * Re-syncs the given offer's product and inventory data
	 *
	 * @access public
	 * @static
	 * @return string A JSON-encoded string
	 */
	public static function resyncOfferInventory() {
		$ajaxResponse = new TS_AjaxResponse();
		if($_SERVER['REQUEST_METHOD']=='POST' && current_user_can('edit_posts')) {
			$offer_id = esc_attr($_POST['offer_id']);
			$success = WP_Topspin_Cache::syncProductsSingle($offer_id);
			if($success) { $ajaxResponse->setStatus('success'); }
		}
		echo $ajaxResponse->output();
		die();
	}

}

?>