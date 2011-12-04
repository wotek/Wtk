<?php

/*
 * Abstract.php 
 * 
 * @category   App
 * @package    App_Pdf
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

abstract class App_Pdf_Adapter_Abstract {

	/**
	 *
	 * @param mixed $config 
	 */
	public function __construct($config) {
		if ($config instanceof Zend_Config) {
			$config = $config->toArray();
		}
		if (is_array($config)) {
			$this->setOptions($config);
		}
	}

	/**
	 * Set options from array
	 *
	 * @param array $options 
	 * @return void
	 */
	public function setOptions(array $options) {
		foreach ($options as $name => $value) {
			$this->{$this->_getMethodname($name)}($value);
		}
	}
	
	/**
	 * Returns proper method name
	 *
	 * @param string $name
	 * @return string 
	 */
	protected function _getMethodname($name) {
		$filter = new Zend_Filter_Word_UnderscoreToCamelCase();
		return 'set' . ucfirst($filter->filter($name));
	}

	/**
	 * Proxy to call setters methods
	 *
	 * @param string $method
	 * @param array $arguments 
	 */
	public function __call($method, $arguments) {
		if (method_exists(get_class_methods($this), $method))
			return call_user_func_array(array($this, $method), $arguments);
	}

	/**
	 * Execute command
	 */
	abstract public function exec();

	/**
	 * Conver to PDF
	 */
	abstract public function generate();
}