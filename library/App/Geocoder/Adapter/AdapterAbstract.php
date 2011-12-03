<?php

/*
 * AdapterAbstract.php
 * 
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

abstract class App_Geocoder_Adapter_AdapterAbstract {
	/**
	 * Default adapters namespace
	 */
	const DEFAULT_ADATPER_NAMESPACE = 'App_Geocoder_Adapter';
	/**
	 * Default request class filename
	 */
	const REQUEST_CLASS_NAME = 'Request';
	/**
	 * Default response class filename
	 */
	const RESPONSE_CLASS_NAME = 'Response';
	/**
	 * Responses formats
	 */
	const RESPONSE_JSON = 'json';
	const RESPONSE_XML = 'xml';

	/**
	 * Response class
	 *
	 * @var string 
	 */
	protected $_responseClass; // should be set in child class

	/**
	 * Response object
	 *
	 * @var App_Geocoder_Response_ResponseAbstract
	 */
	protected $_response;

	/**
	 * Request object
	 *
	 * @var App_Geocoder_Request_RequestAbstract
	 */
	protected $_request;

	/**
	 * Perform geocode on given location using currently set adapter
	 * 
	 * @param mixed $location
	 * @return App_Geocoder_Response
	 */
	abstract public function geocode($location);

	/**
	 * Perform reverse geocoding
	 *
	 * @param int $latitude
	 * @param int $longitude 
	 * @throws App_Geocoder_Exception
	 * @return App_Geocoder_Point
	 */
	public function reverseGeocode($latitude, $longitude) {
		throw new App_Geocoder_Exception('Method not implemented');
	}

	/**
	 * Returns place coordinates
	 * 
	 * @return App_Geocoder_Point
	 */
	abstract public function coordinates();

	/**
	 * Returns default response class
	 * 
	 * @throws App_Geocoder_Adapter_Exception
	 * @return string 
	 */
	protected function getResponseClass() {
		if (null === $this->_responseClass)
			throw new App_Geocoder_Adapter_Exception(
					'Missing _responseClass property. You need to provide 
				response class name to use as response container.'
			);

		$responseClassReflection = new ReflectionClass($this->_responseClass);
		if (!$responseClassReflection->isSubclassOf('App_Geocoder_Response_ResponseAbstract'))
			throw new App_Geocoder_Adapter_Exception(
					'Response class must inherit from App_Geocoder_Response_ResponseAbstract'
			);

		unset($responseClassReflection);

		return $this->_responseClass;
	}

	/**
	 * Returns request object
	 * 
	 * @param array $options Request options
	 * @throws App_Geocoder_Adapter_Exception 
	 * @return App_Geocoder_Request_RequestAbstract
	 */
	protected function getRequest(array $options = array()) {
		if (null === $this->_request) {
			$requestClassName = $this->__getAdapterRequestClassName(get_class($this));
			// try to load class @todo: use Zend_Loader to perform class load
			$this->_request = new $requestClassName($options);
			if (!$this->_request instanceof App_Geocoder_Request_RequestAbstract)
				throw new App_Geocoder_Adapter_Exception(
						'Invalid request class, should extends 
						App_Geocoder_Request_RequestAbstract'
				);
		}
		return $this->_request;
	}

	/**
	 * Sets request param value
	 *
	 * @param string $param_name
	 * @param mixed $value
	 * @return App_Geocoder_Request_RequestAbstract 
	 */
	public function setRequestParam($param_name, $value) {
		return $this->getRequest()->setParam($param_name, $value);
	}

	/**
	 * Returns adapter request class name.
	 *
	 * @param string $adapterName 
	 * @return string
	 */
	private function __getAdapterRequestClassName($adapterName) {
		return implode('_', array(
					$adapterName,
					self::REQUEST_CLASS_NAME
				));
	}

	/**
	 * Returns response
	 * 
	 * @return App_Geocoder_Adapter_Google_Maps_Response
	 */
	public function getResponse() {
		return $this->_response;
	}

}