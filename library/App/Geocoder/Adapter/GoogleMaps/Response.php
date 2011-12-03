<?php

/*
 * Response.php 
 * 
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Geocoder_Adapter_GoogleMaps_Response extends App_Geocoder_Response_ResponseAbstract {
	// Status codes
	const STATUS_OK = "OK";
	const STATUS_ZERO_RESULTS = "ZERO_RESULTS";
	const STATUS_OVER_QUERY_LIMIT = "OVER_QUERY_LIMIT";
	const STATUS_REQUEST_DENIED = "REQUEST_DENIED";
	const STATUS_INVALID_REQUEST = "INVALID_REQUEST";

	// Address Component Types
	const ACT_STREET_ADDRESS = 'street_address';
	const ACT_ROUTE = 'route';
	const ACT_INTERSECTION = 'intersection';
	const ACT_POLITICAL = 'political';
	const ACT_COUNTRY = 'country';
	const ACT_ADMINISTRATIVE_AREA_LEVEL_1 = 'administrative_area_level_1';
	const ACT_ADMINISTRATIVE_AREA_LEVEL_2 = 'administrative_area_level_2';
	const ACT_ADMINISTRATIVE_AREA_LEVEL_3 = 'administrative_area_level_3';
	const ACT_COLLOQUIAL_AREA = 'colloquial_area';
	const ACT_LOCALITY = 'locality';
	const ACT_SUBLOCALITY = 'sublocality';
	const ACT_NEIGHBORHOOD = 'neighborhood';
	const ACT_PREMISE = 'premise';
	const ACT_SUBPREMISE = 'subpremise';
	const ACT_POSTAL_CODE = 'postal_code';
	const ACT_POSTAL_CODE_PREFIX = 'postal_code_prefix';
	const ACT_NATURAL_FEATURE = 'natural_feature';
	const ACT_AIRPORT = 'airport';
	const ACT_PARK = 'park';
	const ACT_POINT_OF_INTEREST = 'point_of_interest';
	const ACT_POST_BOX = 'post_box';
	const ACT_STREET_NUMBER = 'street_number';
	const ACT_FLOOR = 'floor';
	const ACT_ROOM = 'room';
	const ACT_FORMATTED_ADDRESS = 'formatted_address';

	// Location Types
	const LT_ROOFTOP = 'ROOFTOP';
	const LT_RANGE_INTERPOLATED = 'RANGE_INTERPOLATED';
	const LT_GEOMETRIC_CENTER = 'GEOMETRIC_CENTER';
	const LT_APPROXIMATE = 'APPROXIMATE';

	const NAME_SHORT = "short_name";
	const NAME_LONG = "long_name";

	/**
	 * Statuses which are considered valid
	 *
	 * @var array
	 */
	protected static $validStatuses = array(
		self::STATUS_OK,
		self::STATUS_ZERO_RESULTS
	);

	/**
	 * Statuses which are considered invalid
	 *
	 * @var array
	 */
	protected static $invalidStatuses = array(
		self::STATUS_OVER_QUERY_LIMIT,
		self::STATUS_REQUEST_DENIED,
		self::STATUS_INVALID_REQUEST
	);

	/**
	 * Found locations
	 *
	 * @var App_Geocoder_Locations 
	 */
	protected $_locations;

	/**
	 * Process response to create App_Geocoder_Location object
	 * 
	 * @return void
	 */
	protected function processResponse() {
		foreach ($this->_response->results as $result) {
			$this->getLocations()->addLocation(
					$this->createLocation($result)
			);
		}
	}

	/**
	 * Returns wheter response is valid
	 * 
	 * @return bool 
	 */
	public function isValid() {
		return ( in_array($this->getResponseStatus(), self::$validStatuses) );
	}

	/**
	 * Does response has any results
	 * 
	 * @return bool 
	 */
	public function hasResults() {
		return ( self::STATUS_ZERO_RESULTS != $this->getResponseStatus() );
	}

	/**
	 * Creates location
	 *
	 * @param stdClass $object
	 * @return App_Geocoder_Location 
	 */
	public function createLocation(stdClass $object) {
		$formattedAddressComponentsArray = $this->_extractLocationAddressComponents($object->address_components);
		$location = new App_Geocoder_Location($formattedAddressComponentsArray);
		// set formatted address
		$location->setOption(App_Geocoder_Location::FORMATTED_ADDRESS, $object->formatted_address);
		// set location geometry
		$location->setLocationGeometry(
				$object->geometry->location->lat, $object->geometry->location->lng
		);
		return $location;
	}

	/**
	 * Extract component location parts
	 *
	 * @param stdClass $address_components 
	 */
	protected function _extractLocationAddressComponents(array $address_components) {
		$formatted_array = App_Geocoder_Location::getLocationAddressArray();
		foreach ($address_components as $component) {
			$type = $this->_getComponentType($component->types);
			if ($type)
				$formatted_array[$type] = $component->{self::NAME_LONG};
		}
		return $formatted_array;
	}

	/**
	 * Lets guess what given data type is.
	 * @todo: rewrite to use switch statement
	 * 
	 * 
	 * @param array $component 
	 * @return string Addres Component type
	 */
	protected function _getComponentType(array $component_types) {
		if (in_array(self::ACT_STREET_NUMBER, $component_types))
			return App_Geocoder_Location::STREET_NUMBER;

		if (in_array(self::ACT_ROUTE, $component_types))
			return App_Geocoder_Location::STREET;

		if (in_array(self::ACT_LOCALITY, $component_types))
			return App_Geocoder_Location::LOCALITY;

		if (in_array(self::ACT_ADMINISTRATIVE_AREA_LEVEL_3, $component_types))
			return App_Geocoder_Location::ADMINISTRATIVE_AREA_3;

		if (in_array(self::ACT_ADMINISTRATIVE_AREA_LEVEL_2, $component_types))
			return App_Geocoder_Location::ADMINISTRATIVE_AREA_2;

		if (in_array(self::ACT_ADMINISTRATIVE_AREA_LEVEL_1, $component_types))
			return App_Geocoder_Location::ADMINISTRATIVE_AREA_1;

		if (in_array(self::ACT_COUNTRY, $component_types))
			return App_Geocoder_Location::COUNTRY;

		if (in_array(self::ACT_POSTAL_CODE, $component_types)
				&& !in_array(self::ACT_POSTAL_CODE_PREFIX, $component_types))
			return App_Geocoder_Location::POSTAL_CODE;
	}

}