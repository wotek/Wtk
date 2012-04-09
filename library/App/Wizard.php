<?php

/*
 * Wizard.php created 2012-02-26 14:39:33
 *
 * @category   wypozycz.pl
 * @package    wypozycz.pl 
 * @version    $Id$
 * @author     Wojciech Zalewski
 */

class App_Form_Wizard extends App_Form_Wizard_WizardAbstract {

	const OPTION_PAGES = 'pages';
	const OPTION_NAMESPACE = 'namespace';
	const OPTION_FORM_ACTION = 'form_action';
	const OPTION_FORM_CANCEL_ACTION = 'form_cancel_action';

	private function __construct() {
		// use factory method
	}

	/**
	 *
	 * @param array $options
	 * @return App_Form_Wizard 
	 */
	public static function factory($options) {
		if ($options instanceof Zend_Config)
			$options = $options->toArray();

		if (!is_array($options))
			throw new App_Form_Wizard_Exception(
					'Invalid options. Argument should be a Zend_Config instance 
					or array.'
			);

		// Create instance
		$instance = new self;
		// Set options
		$instance->setOptions($options);

		return $instance;
	}

	/**
	 * Sets options
	 *
	 * @param array $options 
	 */
	public function setOptions(array $options = array()) {
		foreach ($options as $option => $value) {
			$this->setOption($option, $value);
		}
		return $this;
	}

	/**
	 * Set option
	 * 
	 * @param string $option
	 * @param mixed $value 
	 */
	public function setOption($option, $value) {
		switch ($option) {
			case self::OPTION_PAGES:
				$this->setPages($value);
				break;

			case self::OPTION_NAMESPACE:
				$this->setDefaultNamespaceName($value);
				break;

			case self::OPTION_FORM_ACTION:
				$this->setFormAction($value);
				break;

			case self::OPTION_FORM_CANCEL_ACTION:
				$this->setFormCancelAction($value);
				break;

			default:
				throw new App_Form_Wizard_Exception("Invalid option given: $option");
				break;
		}
	}

}