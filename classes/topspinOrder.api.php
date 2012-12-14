<?php
/**
 * Topspin_Order_API
 *
 * @package Topspin
 * @subpackage API
 * @link https://docs.topspin.net/tiki-index.php?page=Order+API
 */
class Topspin_Order_API extends Topspin_API {

	/**
	 * Calls the API for orders
	 *
	 * @access public
	 * @param array $params (default: array())
	 * @return void
	 */
	public function getList($params=array()) {
		$res = $this->call('api/v1/order', $params);
		return $res;
	}

	/**
	 * Calls the API for SKUs
	 *
	 * @access public
	 * @param array $params (default: array())
	 * @return void
	 */
	public function getSkus($params=array()) {
		$res = $this->call('api/v1/sku', $params);
		return $res;
	}

}

?>