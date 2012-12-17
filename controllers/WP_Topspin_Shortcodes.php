<?php

add_shortcode('topspin_store_nav_menu',				array('WP_Topspin_Shortcodes', 'storeNavMenu'));
add_shortcode('topspin_featured_item',				array('WP_Topspin_Shortcodes', 'featuredItem'));
add_shortcode('topspin_buy_buttons',				array('WP_Topspin_Shortcodes', 'buyButtons'));
add_shortcode('topspin_store_item',					array('WP_Topspin_Shortcodes', 'storeItem'));

/**
 * Handles WordPress shortcodes
 *
 * @package WordPress
 * @subpackage Topspin
 */
class WP_Topspin_Shortcodes {

	/**
	 * Displays the store nav menus
	 *
	 * @shortcode [topspin_store_nav_menu]
	 * @access public
	 * @static
	 * @param array $atts
	 * @return string
	 */
	public static function storeNavMenu($atts) {
		return WP_Topspin_Template::getContents('menus.php');
	}

	/**
	 * Displays the store grid
	 *
	 * #Parameters
	 * * int	id				The store post ID
	 * 
	 * @shortcode [topspin_buy_buttons]
	 * @access public
	 * @static
	 * @param array $atts
	 * @return string
	 */
	public static function buyButtons($atts) {
		$content = '';
		// If attribute isn't set, retrieve it based on the current post's ID
		if(!$atts) {
			global $post;
			if($post) {
				$new_store_ID = WP_Topspin_Upgrade_Controller::_getStorePostIdByLegacyPostId($post->ID);
				if($new_store_ID) {
					$atts['id'] = $new_store_ID;
				}
			}
		}
		if(isset($atts['id'])) {
			// Query for the store items
			$args = array(
				'post_ID' => $atts['id'],
				'page' => 1
			);
			$tsQuery = new TS_Query($args);
			$vars = array(
				'args' => $args,
				'tsQuery' => $tsQuery
			);
			$content = WP_Topspin_Template::getContents('index.php', $vars);
			// Retrieve pagination
			if($tsQuery->max_num_pages>1) { $content .= WP_Topspin_Template::getContents('pager.php', $vars); }
		}
		return $content;
	}

	/**
	 * Displays a single featured item 
	 * 
	 * #Parameters
	 * * int	id				The offer post ID
	 * 
	 * @shortcode [topspin_featured_item]
	 * @access public
	 * @static
	 * @param array $atts
	 * @return string
	 */
	public static function featuredItem($atts) {
		// Query for the specified item
		$args = array(
			'offer_ID' => $atts['id']
		);
		$tsQuery = new TS_Query($args);
		$vars = array(
			'args' => $args,
			'tsQuery' => $tsQuery
		);
		$content = WP_Topspin_Template::getContents('featured.php', $vars);
		return $content;
	}

	/**
	 * Displays a single store item 
	 *
	 * #Parameters
	 * * int	id				The offer post ID
	 * 
	 * @shortcode [topspin_store_item]
	 * @access public
	 * @static
	 * @param array $atts
	 * @return string
	 */
	public static function storeItem($atts) {
		// Query for the specified item
		$args = array(
			'offer_ID' => $atts['id']
		);
		$tsQuery = new TS_Query($args);
		$vars = array(
			'args' => $args,
			'tsQuery' => $tsQuery
		);
		$content = WP_Topspin_Template::getContents('single.php', $vars);
		return $content;
	}

}

?>