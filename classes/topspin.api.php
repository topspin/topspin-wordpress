<?php
/**
 * Topspin_API
 *
 * @package Topspin
 * @subpackage API
 * @link https://docs.topspin.net/tiki-index.php?page=Topspin+APIs&structure=Dev+Center
 */
class Topspin_API {

	private $_credentials = false;
	private $_endpoint = 'http://app.topspin.net';

	// cUrl stored properties
	private $_lastRequest = false;

	/**
	 * Initializes the Topspin API
	 *
	 * @access public
	 * @param string $username
	 * @param string @password
	 * @return void
	 */
	public function __construct($username, $password) {
		$this->_credentials = array(
			'username' => $username,
			'password' => $password
		);
	}

	/**
	 * Parses the API action and the endpoint URL
	 * 
	 * If params is set, than it will be appended to the URL as query variables
	 *
	 * @access public
	 * @param string $action
	 * @param array $params (default: array())
	 * @return string
	 */
	public function getAPIUrl($action, $params=array()) {
		if(count($params)) { return sprintf('%s/%s?%s', $this->_endpoint, $action, http_build_query($params)); }
		else { return sprintf('%s/%s', $this->_endpoint, $action); }
	}

	/**
	 * Makes a call to the Topspin API
	 *
	 * @link https://docs.topspin.net/tiki-index.php?page=Topspin+APIs&structure=Dev+Center
	 * @access public
	 * @param string $action
	 * @param array $params (default: array())
	 * @param string $method (default: 'GET')
	 * @return object A JSON-encoded object
	 */
	public function call($action, $params=array(), $method='GET') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, sprintf('%s:%s', $this->_credentials['username'], $this->_credentials['password']));
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		switch($method) {
			case 'GET':
				curl_setopt($ch, CURLOPT_URL, $this->getAPIUrl($action, $params));
				break;
			case 'POST':
			default:
				curl_setopt($ch, CURLOPT_URL, $this->getAPIUrl($action));
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
				break;
		}
		$this->_lastParams = $params;
		$this->_lastResponse = curl_exec($ch);
		$this->_lastRequest = curl_getinfo($ch);
		curl_close($ch);
		return json_decode($this->_lastResponse);
	}

	/**
	 * Retrieve the last request information
	 *
	 * @access public
	 * @return array
	 */
	public function getLastRequest() {
		return $this->_lastRequest;
	}

	/**
	 * Checks to see if the credentials are valid
	 *
	 * @access public
	 * @return bool
	 */
	public function verifyCredentials() {
		$res = $this->call('api/v1/offers');
		$lastRequest = $this->getLastRequest();
		if($lastRequest['http_code'] == '200') { return true; }
		else { return false; }
	}

	/* !----- Static Methods ----- */

	/**
	 * Retrieves a list of all offer types avaiable in the Topspin API
	 * 
	 * @access public
	 * @static
	 * @return array An array of all offer types
	 */
	public static function getOfferTypes() {
		return array(
			'buy_button' => 'Buy Button',
			'email_for_media' => 'Email for Media',
			'bundle_widget' => 'Bundle Widget',
			'single_track_player_widget' => 'Single Track Player Widget'
		);
	}

	/**
	 * Retrieves a list of currency symbols
	 * 
	 * @access public
	 * @static
	 * @return array
	 */
	public static function getCurrencies() {
		return array(
			'USD' => '$',
			'GBP' => '&pound;',
			'EUR' => '&euro;',
			'JPY' => '&yen;',
			'AUD' => '&#xA5;',
			'CAD' => '$'
		);
	}

	/**
	 * Retrieves the currency symbol
	 * 
	 * @access public
	 * @static
	 * @param string $currency The country abbreviation
	 * @return string
	 */
	public static function getCurrentSymbol($currency) {
		$currencies = self::getCurrencies();
		return (isset($currencies[$currency])) ? $currencies[$currency] : '';
	}

	/**
	 * Extract the campaign ID from the offer's mobile URL
	 *
	 * @access public
	 * @static
	 * @param object $offer
	 * @return string
	 */
	public static function getCampaignId($offer) {
		$url = (isset($offer->mobile_url)) ? $offer->mobile_url : false;
		return self::getCampaignIdByMobileUrl($url);
	}

	/**
	 * Extract the campaign ID from the offer's a mobile URL
	 *
	 * @access public
	 * @static
	 * @param object $offer
	 * @return string
	 */
	public static function getCampaignIdByMobileUrl($url) {
		if($url) {
			$data = parse_url($url);
			if(isset($data['query'])) {
				$params = array();
				parse_str($data['query'], $params);
				if(isset($params['campaign_id'])) { return $params['campaign_id']; }
			}
		}
		return false;
	}

}

?>