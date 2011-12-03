<?php

/*
 * Location.php
 *
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Geocoder_Location {
	const STREET_NUMBER = 'street_number';
	const STREET = 'street';
	const SUBLOCALITY = 'sublocality';
	const LOCALITY = 'locality';
	const ADMINISTRATIVE_AREA_1 = 'administrative_area_1';
	const ADMINISTRATIVE_AREA_2 = 'administrative_area_2';
	const ADMINISTRATIVE_AREA_3 = 'administrative_area_3';
	const COUNTRY = 'country';
	const POSTAL_CODE = 'postal_code';
	const FORMATTED_ADDRESS = 'formatted_address';
	const GEOMETRY = 'geometry';

	const PARTIAL_MATCH = 'partial_match';

	/**
	 * Street number
	 * 
	 * @var string 
	 */
	protected $street_number;

	/**
	 * Street
	 * 
	 * @var string 
	 */
	protected $street;

	/**
	 * District
	 * 
	 * @var string 
	 */
	protected $sublocality;

	/**
	 * City
	 * 
	 * @var string 
	 */
	protected $locality;

	/**
	 *
	 * @var string 
	 */
	protected $administrative_area_1;

	/**
	 *
	 * @var string 
	 */
	protected $administrative_area_2;

	/**
	 *
	 * @var string 
	 */
	protected $administrative_area_3;

	/**
	 * Country
	 * 
	 * @var string 
	 */
	protected $country;

	/**
	 * Postal code
	 * 
	 * @var string 
	 */
	protected $postal_code;

	/**
	 * Formatted address
	 * 
	 * @var string 
	 */
	protected $formatted_address;

	/**
	 * Geomoetry
	 * 
	 * @var App_Geocoder_Point
	 */
	protected $geometry;

	/**
	 *
	 * @param type $data 
	 * @return void
	 */
	public function __construct($options) {
		$this->setOptions($options);
	}

	/**
	 * Sets location properties
	 *
	 * @param type $options 
	 * @return void
	 */
	public function setOptions($options) {
		foreach ($options as $option => $value) {
			$this->setOption($option, $value);
		}
	}

	/**
	 * Sets location property
	 *
	 * @param string $option
	 * @param mixed $value 
	 * @return void
	 */
	public function setOption($option, $value) {
		if (in_array($option, $this->getLocationProperties())) {
			$this->{$option} = $value;
		}
	}

	/**
	 * Returns location formatted address
	 * 
	 * @return string 
	 */
	public function getFormattedAddress() {
		return $this->formatted_address;
	}

	/**
	 * Returns location coordinates
	 * 
	 * @return App_Geocoder_Point
	 */
	public function getCoordinates() {
		return $this->geometry;
	}

	/**
	 *
	 * @return string 
	 */
	public function getCity() {
		return $this->locality;
	}

	/**
	 *
	 * @return string 
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 *
	 * @return string 
	 */
	public function getStreet() {
		return $this->street;
	}
	
	public function getPostCode() {
		return $this->postal_code;
	}
	
	public function getAdministrativeArea($area) {
		return $this->{$area};
	}

	/**
	 *
	 * @return string 
	 */
	public function getStreetNumber() {
		return $this->street_number;
	}

	/**
	 *
	 * @return array 
	 */
	protected function getLocationProperties() {
		return array_keys(get_class_vars(get_class($this)));
	}

	/**
	 * Set location geometry
	 *
	 * @param int $latitude
	 * @param int $longitude 
	 * @return void
	 */
	public function setLocationGeometry($latitude, $longitude) {
		$this->geometry = new App_Geocoder_Point($latitude, $longitude);
	}

	/**
	 * Returns location array
	 * 
	 * @return array
	 */
	public static function getLocationAddressArray() {
		return array_fill_keys(array(
					self::STREET,
					self::SUBLOCALITY,
					self::LOCALITY,
					self::ADMINISTRATIVE_AREA_1,
					self::ADMINISTRATIVE_AREA_2,
					self::ADMINISTRATIVE_AREA_3,
					self::COUNTRY,
					self::POSTAL_CODE,
					self::FORMATTED_ADDRESS,
					self::GEOMETRY
						), null);
	}

}