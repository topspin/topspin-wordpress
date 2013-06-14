<?php
/**
 * Topspin_Artist_API
 *
 * @package Topspin
 * @subpackage API
 * @link https://docs.topspin.net/tiki-index.php?page=Artist+Search+API
 */
class Topspin_Artist_API extends Topspin_API {

	/**
	 * Calls the API for artists
	 *
	 * @access public
	 * @param array $params (default: array())
	 * @return void
	 */
	public function getList($params=array()) {
		$res = $this->call('api/v1/artist', $params);
		return $res;
	}

}

?>