<?php

/*
 * Geocoded locations result set container
 * 
 * Locations.php
 *
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Geocoder_Locations implements ArrayAccess, Countable, Iterator {

	/**
	 * Array of locations
	 *
	 * @var array 
	 */
	protected $_locations = array();

	/**
	 * Cursor
	 * 
	 * @var int 
	 */
	private $_position = 0;

	/**
	 * Add location to container
	 *
	 * @param App_Geocoder_Location $location
	 * @return App_Geocoder_Locations 
	 */
	public function addLocation(App_Geocoder_Location $location) {
		array_push($this->_locations, $location);
		return $this;
	}

	/**
	 * Returns locations set
	 * 
	 * @return array
	 */
	public function getLocations() {
		return $this->_locations;
	}

	/**
	 * Wheter a offset exists
	 *
	 * @param type $offset 
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return isset($this->_locations[$offset]);
	}

	/**
	 * Offset to retrive
	 *
	 * @param mixed $offset 
	 * @return App_Geocoder_Location
	 */
	public function offsetGet($offset) {
		return $this->_locations[$offset];
	}

	/**
	 * Offset to set
	 *
	 * @param mixed $offset
	 * @param App_Geocoder_Location $value 
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		if (!$value instanceof App_Geocoder_Location)
			throw new App_Geocoder_Exception('Location should be instance of App_Geocoder_Location');

		$this->_locations[$offset] = $value;
	}

	/**
	 * Offset to unset
	 *
	 * @param mixed $offset 
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->_locations[$offset]);
	}

	/**
	 * Count elements
	 * 
	 * @return int
	 */
	public function count() {
		return count($this->_locations);
	}

	/**
	 * Returns current element
	 * 
	 * @return App_Geocoder_Location
	 */
	public function current() {
		return $this->_locations[$this->_position];
	}

	/**
	 * Move forward to next element
	 * 
	 * @return App_Geocoder_Locations
	 */
	public function next() {
		++$this->_position;
		return $this;
	}

	/**
	 * Return the key of the current element
	 * 
	 * @return scalar scalar on success, integer 0 on failure.
	 */
	public function key() {
		return $this->_position;
	}

	/**
	 * Checks if current position is valid
	 * 
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return isset($this->_locations[$this->_position]);
	}

	/**
	 * Rewind the Iterator to the first element
	 * 
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		$this->_position = 0;
	}

}