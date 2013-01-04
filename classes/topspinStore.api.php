<?php
/**
 * Topspin_Store_API
 *
 * @package Topspin
 * @subpackage API
 * @link https://docs.topspin.net/tiki-index.php?page=Store+API
 */
class Topspin_Store_API extends Topspin_API {

	/**
	 * Calls the API for offers
	 *
	 * @access public
	 * @param array $params (default: array())
	 * @return object|bool
	 */
	public function getList($params=array()) {
		$res = $this->call('api/v1/offers', $params);
		return $res;
	}

	/**
	 * Calls the API for a specific offer
	 *
	 * @access public
	 * @param int $offer_id
	 * @return object|bool
	 */
	public function getOffer($offer_id) {
		$params = array(
			'offer_id' => $offer_id
		);
		$res = $this->call('api/v1/offers', $params);
		$lastRequest = $this->getLastRequest();
		// If authenticated
		if($lastRequest['http_code'] == '200') {
			// If the offer exists
			if($res->total_entries && count($res->offers)) { return $res->offers[0]; }
		}
	}
	
}

?>