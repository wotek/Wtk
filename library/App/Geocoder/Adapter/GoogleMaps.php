<?php

/*
 * GoogleMaps.php 
 * 
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Geocoder_Adapter_GoogleMaps extends App_Geocoder_Adapter_AdapterAbstract {
	/**
	 * Response class 
	 * 
	 * @var string
	 */
	protected $_responseClass = 'App_Geocoder_Adapter_GoogleMaps_Response';

	/**
	 * Constructor
	 * 
	 * @param array $options Options to construct adapter with
	 */
	public function __construct(array $options) {
		if (!isset($options['apikey']))
			throw new App_Geocoder_Adapter_Exception('Missing API key!');
	}

	/**
	 * Geocode address
	 * 
	 * @todo Refactor geocode move some common stuff to abstract
	 *
	 * @param string $location
	 * @throws App_Geocoder_Exception
	 * @return App_Geocoder_Adapter_GoogleMaps_Response 
	 */
	public function geocode($location) {
		if (!is_string($location))
			throw new App_Geocoder_Exception('Location should be string.');

		$this->setRequestParam('address', $location);
		// Dispatch request
		$response = $this->getRequest()->dispatch();
		// move packing into response class to abstract
		$responseClass = $this->getResponseClass();

		return new $responseClass($response, 'json');
	}

	/**
	 * Reverse geocoding
	 *
	 * @param int $latitude
	 * @param int $longitude 
	 */
	public function reverseGeocode($latitude, $longitude) {}

	/**
	 * Returns location coordinates
	 * 
	 * @return App_Geocoder_Point
	 */
	public function coordinates() {}

}