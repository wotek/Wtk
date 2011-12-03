<?php

/*
 * Request.php 
 * 
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Geocoder_Adapter_GoogleMaps_Request extends App_Geocoder_Request_RequestAbstract {
	/**
	 * Service URI
	 */
	const SERVICE_URI = 'http://maps.googleapis.com/maps/api/geocode/';
	/**
	 * Indicates response format.
	 * 
	 * Ouput may have following values:
	 * + json
	 * + xml
	 *
	 * @var string 
	 */
	protected $output = 'json';

	/**
	 * Address we want to geocode
	 * 
	 * @var string 
	 */
	protected $address;

	/**
	 * Textual latitude/longitude value for which we want to obtain the closest
	 * human readable address
	 *
	 * @var string 
	 */
	protected $latlng;

	/**
	 * Bouding box of viewport within which to bias geocode results
	 *
	 * @var string 
	 */
	protected $bounds;

	/**
	 * The region code, specified as a ccTLD (top-level domain)
	 * two character value
	 *
	 * @var string 
	 */
	protected $region = 'pl';

	/**
	 * Language to use for output result
	 *
	 * @var string 
	 */
	protected $language = 'pl';

	/**
	 * API key
	 *
	 * @var string 
	 */
	protected $key;

	/**
	 * Indicates whether or not the geocoding request comes from a device 
	 * with a location sensor
	 * 
	 * Allowed only: true | false
	 *
	 * @var string
	 */
	protected $sensor = 'false';

	/**
	 * Sets API key
	 *
	 * @param string $key
	 * @return App_Geocoder_Adapter_Google_Maps_Request 
	 */
	public function setApiKey($key) {
		$this->key = $key;
		return $this;
	}

	/**
	 * Dispatch request
	 * 
	 * @throws App_Geocoder_Request_Exception
	 * @return string Response body
	 */
	public function dispatch() {
		/**
		 * query param
		 */
		$queryParams = $this->_buildQueryParamsArray();
		/**
		 * Setup http client
		 */
		$this->getHttpClient()->setMethod(Zend_Http_Client::GET);
		$this->getHttpClient()->setParameterGet($queryParams);

		$response = $this->getHttpClient()->request();

		if ($response->isError())
			throw new App_Geocoder_Request_Exception('
				An error occured sending the geocode request.
				HTTP Status code: ' . $response->getStatus()
			);

		return $response->getBody();
	}

	/**
	 * build query part
	 * 
	 * @return array
	 */
	protected function _buildQueryParamsArray() {
		$queryArray = array();
		foreach ($this->getAvailableRequestParams() as $param_name) {
			$queryArray[$param_name] = $this->{$param_name};
		}

		if (isset($queryArray['output']))
			unset($queryArray['output']);
		
		return $queryArray;
	}

	/**
	 * Returns service uri base path
	 *
	 * @return string
	 */
	public function _getServiceUri() {
		return self::SERVICE_URI . $this->output;
	}

	/**
	 * Sets request param
	 *
	 * @param string $param_name
	 * @param mixed $value 
	 * @return App_Geocoder_Adapter_Google_Maps_Request
	 */
	public function setParam($param_name, $value) {
		if (false === $this->_validateParam($param_name))
			throw new App_Geocoder_Adapter_Request_Exception(
					'Invalid request param name!'
			);

		$this->{$param_name} = $value;
		return $this;
	}

	/**
	 * Validate param name
	 *
	 * @param string $param_name 
	 * @return bool
	 */
	protected function _validateParam($param_name) {
		return in_array($param_name, $this->getAvailableRequestParams());
	}

	/**
	 * Returns available params names
	 *
	 * @return array 
	 */
	public function getAvailableRequestParams() {
		return array_keys(get_class_vars(get_class($this)));
	}

}