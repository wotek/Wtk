<?php

/*
 * Geocoder.php
 *
 * @category   App
 * @package    App_Geocoder
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Geocoder {
	/**
	 * Default Zend_Registry key
	 */
	const DEFAULT_REGISTRY_KEY = 'App_Geocoder';
	/**
	 * Geocoder adapter
	 * 
	 * @var App_Geocoder_Adapter_AdapterAbstract
	 */
	protected $_adapter;

	/**
	 * Cache connector
	 * 
	 * @todo: implement cache abillity for geocoder
	 *
	 * @var type 
	 */
	protected $_cache;

	/**
	 * Facotry method to return geocoder adapter
	 * 
	 * @param array $options factory options
	 * @return App_Geocoder
	 */
	public static function factory(array $options = array()) {
		if (empty($options))
			$options = self::getOptions();

		// determine adapter to use for geocoding
		$adapter = $options['adapter'];
		/**
		 * Prepare adapter class name
		 */
		$adapterName = array(
			isset($options['adapterNamespace']) ?
					$options['adapterNamespace'] : App_Geocoder_Adapter_AdapterAbstract::DEFAULT_ADATPER_NAMESPACE,
			$options['adapter']
		);
		$adapterName = implode('_', $adapterName);
		/*
		 * Load the adapter class.  This throws an exception
		 * if the specified class cannot be loaded.
		 */
		// @codeCoverageIgnoreStart
		if (!class_exists($adapterName)) {
			require_once 'Zend/Loader.php';
			Zend_Loader::loadClass($adapterName);
		}
		// @codeCoverageIgnoreEnd
		
		// create new instance
		$instance = new self;
		// Set adapter
		$instance->setAdapter($adapterName, $options);

		return $instance;
	}

	/**
	 * Perform geocoding
	 * 
	 * @param string $location
	 * @return App_Geocoder_Response_ResponseAbstract 
	 */
	public function geocode($location) {
		return $this->getAdapter()->geocode($location);
	}

	/**
	 * Retrive geocoder configuration from application config
	 *
	 * @throws App_Geocoder_Exception
	 * @return array configuration array 
	 */
	protected static function getOptions() {
		if (Zend_Registry::isRegistered(self::DEFAULT_REGISTRY_KEY)) {
			return Zend_Registry::get(self::DEFAULT_REGISTRY_KEY);
		}
		throw new App_Geocoder_Exception(
				'You need to provide valid configuration.'
		);
	}

	/**
	 * Returns geocoder adapter
	 *
	 * @return App_Geocoder_GeocoderAbstract 
	 */
	public function getAdapter() {
		return $this->_adapter;
	}

	/**
	 * Sets adapter
	 *
	 * @param string|App_Geocoder_AdapterAbstract $adapter
	 * @param array $options
	 * @return App_Geocoder 
	 */
	public function setAdapter($adapter, array $options = array()) {
		if ($adapter instanceof App_Geocoder_AdapterAbstract)
			$this->_adapter = $adapter;

		if (is_string($adapter))
			$this->_adapter = new $adapter($options);
		else
			throw new App_Geocoder_Adapter_Exception(
					'Adapter should be adapter class name 
					or App_Geocoder_AdapterAbstract instance.'
			);

		return $this;
	}

//	public function setCache() {
//		
//	}
//
//	public function getCache() {
//		
//	}

}