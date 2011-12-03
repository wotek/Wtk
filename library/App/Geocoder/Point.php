<?php

/*
 * Point geometry
 * 
 * Point.php
 *
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Geocoder_Point {

	/**
	 * Object latitude
	 * 
	 * @var string 
	 */
	protected $latitude;

	/**
	 * Object longitude
	 * 
	 * @var string 
	 */
	protected $longitude;

	/**
	 * Contructor
	 *
	 * @param array $options 
	 */
	public function __construct($latitude, $longitude) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	/**
	 * Returns object latitude
	 * 
	 * @return string 
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * Returns object longitude
	 *
	 * @return string 
	 */
	public function getLongitude() {
		return $this->longitude;
	}

	/**
	 * Returns object as array
	 * 
	 * @return array 
	 */
	public function toArray() {
		return array('latitude' => $this->getLatitude(), 'longitude' => $this->getLongitude());
	}

}