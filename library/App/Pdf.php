<?php

/*
 * Pdf.php 
 * 
 * @category   App
 * @package    App_Pdf
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Pdf {

	/**
	 * Adapter for pdf generation
	 * 
	 * @var App_Pdf_Adapter_Abstract 
	 */
	protected $_adapter;

	/**
	 * Factory for pdf adapters classes
	 *
	 * @param App_Pdf_Adapter_Abstract $adapter
	 * @param array $config 
	 * @return App_Pdf_Adapter_Abstract
	 * @throws App_Pdf_Exception
	 */
	public static function factory($adapter, array $config = array()) {
		if ($adapter instanceof App_Pdf_Adapter_Abstract)
			$this->setAdapter($adapter);

		if (!is_string($adapter) || empty($adapter)) {
			require_once 'App/Pdf/Exception.php';
			throw new App_Pdf_Exception('Adapter name must be specified in a string');
		}

		/*
		 * Form full adapter class name
		 */
		$adapterNamespace = 'App_Pdf_Adapter';
		if (isset($config['adapterNamespace'])) {
			if ($config['adapterNamespace'] != '') {
				$adapterNamespace = $config['adapterNamespace'];
			}
			unset($config['adapterNamespace']);
		}

		// Adapter no longer normalized- see http://framework.zend.com/issues/browse/ZF-5606
		$adapterName = $adapterNamespace . '_';
		$adapterName .= str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($adapter))));
		/*
		 * Load the adapter class.  This throws an exception
		 * if the specified class cannot be loaded.
		 */
		if (!class_exists($adapterName)) {
			require_once 'Zend/Loader.php';
			Zend_Loader::loadClass($adapterName);
		}

		/*
		 * Create an instance of the adapter class.
		 * Pass the config to the adapter class constructor.
		 */
		$pdfAdapter = new $adapterName($config);

		/*
		 * Verify that the object created is a descendent of the abstract adapter type.
		 */
		if (!$pdfAdapter instanceof App_Pdf_Adapter_Abstract) {
			/**
			 * @see Zend_Db_Exception
			 */
			require_once 'App/Pdf/Exception.php';
			throw new App_Pdf_Exception("Adapter class '$adapterName' does not extend App_Pdf_Adapter_Abstract");
		}

		return $pdfAdapter;
	}

	/**
	 * Set pdf generating adapter
	 * 
	 * @param App_Pdf_Adapter_Abstract $adapter 
	 */
	public function setAdapter(App_Pdf_Adapter_Abstract $adapter) {
		$this->_adapter = $adapter;
	}

	/**
	 * Returns adapter
	 * 
	 * @return App_Pdf_Adapter_Abstract 
	 */
	public function getAdapter() {
		return $this->_adapter;
	}
}