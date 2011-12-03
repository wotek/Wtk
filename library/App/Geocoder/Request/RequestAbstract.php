<?php

/*
 * RequestAbstract.php 
 * 
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

abstract class App_Geocoder_Request_RequestAbstract {

	/**
	 * Http client
	 *
	 * @var Zend_Http_Client 
	 */
	private $_httpClient;

	/**
	 * Indicates response format.
	 * 
	 * Ouput may have following values:
	 * + json
	 * + xml
	 * 
	 * @var string 
	 */
	protected $output = null;

	/**
	 * 
	 * @param array $options 
	 */
	public function __construct(array $options = array()) {
		iconv_set_encoding('output_encoding', 'UTF-8');
		iconv_set_encoding('input_encoding', 'UTF-8');
		iconv_set_encoding('internal_encoding', 'UTF-8');

		if (!empty($options))
			$this->setOptions($options);
	}

	/**
	 * Sets options
	 * 
	 * @param array $options 
	 */
	protected function setOptions(array $options) {
		foreach ($options as $option => $value) {
			$this->setParam($option, $value);
		}
	}

	/**
	 * Sets request param
	 *
	 * @param string $param_name
	 * @param mixed $value 
	 * @return App_Geocoder_Request_RequestAbstract
	 */
	abstract public function setParam($param_name, $value);

	/**
	 * Returns http client
	 *
	 * @return Zend_Http_Client 
	 */
	public function getHttpClient($uri = null, $config = array()) {
		if (null === $this->_httpClient) {
			if (null === $uri)
				$uri = $this->_getServiceUri();

			$this->_httpClient = new Zend_Http_Client($uri, $config);
		}
		return $this->_httpClient;
	}

	/**
	 * @todo: Perform request common stuff
	 * 
	 * * dispatch http client
	 * * Check for errors 
	 * * Return response
	 */
	public function processRequest() {
		// @see Zend_Service_Audioscrobbler
	}

	// @todo: abstract function validateRequestParams()

	/**
	 * Disaptch request
	 * 
	 * @return Response
	 */
	abstract public function dispatch();

	/**
	 * Should return service URI
	 * 
	 * @return string
	 */
	abstract protected function _getServiceUri();

	/**
	 * Should build GET query params array
	 * 
	 * @return array
	 */
	abstract protected function _buildQueryParamsArray();

	/**
	 * Returns expected resopnse data format
	 * 
	 * @return string 
	 */
	public function getOutputFormat() {
		if (null === $this->output)
			throw new App_Geocoder_Request_Exception('Output property not set.
				You have define response data format');

		return $this->_output;
	}

}