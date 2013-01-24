<?php

/**
 * Topspin AJAX Response object
 *
 * @package WordPress
 */
class TS_AjaxResponse {

	private $_status;
	private $_response;

	/**
	 * Creates a new AJAX response object
	 *
	 * @access public
	 * @param string $status (default: 'error')
	 * @param string $response (default: null)
	 */
	public function __construct($status='error', $response=null) {
		$this->_status = $status;
		$this->_response = $response;
	}

	/**
	 * Sets the new status for the AJAX Response
	 *
	 * @access public
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->_status = $status;
	}

	/**
	 * Sets the new response for the AJAX Response
	 *
	 * @access public
	 * @param string $response
	 * @return void
	 */
	public function setResponse($response) {
		$this->_response = $response;
	}

	/**
	 * Returns JSON-encoded string of the status and response object
	 *
	 * @access public
	 * @return string
	 */
	public function output() {
		header('Content-type: application/json');
		$args = array(
			'status' => $this->_status,
			'response' => $this->_response
		);
		return json_encode($args);
	}

}

?>