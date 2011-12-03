<?php

/*
 * ResponseAbstract.php 
 * 
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

abstract class App_Geocoder_Response_ResponseAbstract {
	const LOCATION_COLLECTION_CLASS = 'App_Geocoder_Locations';
	const DEFAULT_LOCATION_CLASS = 'App_Geocoder_Location';

	/**
	 * Raw response body
	 *
	 * @var string 
	 */
	protected $_responseRawBody;

	/**
	 * Response format type
	 *
	 * @var string 
	 */
	protected $_responseFormat;

	/**
	 * Response status
	 * 
	 * @var string 
	 */
	protected $_responseStatus;

	/**
	 * Response object
	 * 
	 * @var stdClass 
	 */
	protected $_response;
	
	/**
	 * Constructor
	 *
	 * @param string $responseRawBody
	 * @param string $responseFormat 
	 * 
	 * @throws App_Geocoder_Adapter_Response_Exception
	 */
	public function __construct($responseRawBody, $responseFormat) {
		//		$this->setResponseRawBody($responseRawBody);
		/**
		 * Set response format
		 */
		$this->setResponseFormat($responseFormat);

		/**
		 * Unpack response
		 */
		switch ($this->getResponseFormat()) {
			case App_Geocoder_Adapter_AdapterAbstract::RESPONSE_JSON:
				$this->_response = json_decode($responseRawBody);
				break;
			case App_Geocoder_Adapter_AdapterAbstract::RESPONSE_JSON:
				throw new App_Geocoder_Adapter_Response_Exception(
						'Unsupported format type: ' . $this->getResponseFormat()
				);
				break;
			default:
				throw new App_Geocoder_Adapter_Response_Exception(
						'Unsupported format type: ' . $this->getResponseFormat()
				);
		}
		// @todo Move somewhere else - might differ on adapter
		// Set response statuses
		$this->setResponseStatus($this->_response->status);

		// check if response is valid
		if (false === $this->isValid())
			throw new App_Geocoder_Response_Exception(
					'Response is invalid - ' . $this->getResponseStatus()
			);

		// response ok, consume it
		$this->processResponse();
	}

	/**
	 * Sets response status
	 * 
	 * @param string $status 
	 */
	protected function setResponseStatus($status) {
		$this->_responseStatus = $status;
	}

	/**
	 * Returns response format
	 * 
	 * @return string 
	 */
	public function getResponseStatus() {
		return $this->_responseStatus;
	}

	/**
	 * Sets response raw body
	 *
	 * @param string $body 
	 * @return void
	 */
	protected function setResponseRawBody($responseRawBody) {
		$this->_responseRawBody = $responseRawBody;
	}

	/**
	 * Returns response
	 * 
	 * @throws App_Geocoder_Response_Exception
	 * @return stdClass 
	 */
	protected function getResponse() {
		if (null === $this->_response)
			throw new App_Geocoder_Response_Exception(
					'Something went wrong _response is empty!'
			);

		return $this->_response;
	}

	/**
	 * Sets response format
	 *
	 * @param type $format 
	 * @return void
	 */
	protected function setResponseFormat($responseFormat) {
		$this->_responseFormat = $responseFormat;
	}

	/**
	 * Returns response format
	 * 
	 * @return string 
	 */
	protected function getResponseFormat() {
		return $this->_responseFormat;
	}

	/**
	 * Tells wheter response is valid
	 */
	abstract public function isValid();

	/**
	 * Tells wheres response has results
	 */
	abstract public function hasResults();

	/**
	 * Should process response and resturn App_Geocoder_Locations
	 */
	abstract protected function processResponse();

	/**
	 * Returns locations
	 * 
	 * @return App_Geocoder_Locations
	 */
	public function getLocations() {
		if (null === $this->_locations) {
			$this->_locations = new App_Geocoder_Locations();
		}
		return $this->_locations;
	}

	/**
	 * Create App_Geocoder_Location from partial data
	 * 
	 * @param stdClass
	 * @return App_Geocoder_Location
	 */
	abstract protected function createLocation(stdClass $data);
}