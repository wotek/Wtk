<?php

/*
 * Page.php created 2012-02-26 14:39:08
 *
 * @category   App_Wizard
 * @package    App_Wizard
 * @version    $Id$
 * @author     Wojciech Zalewski
 */

class App_Form_Wizard_Page extends App_Form_Wizard_Page_PageAbstract {

	/**
	 *
	 * @param array $options 
	 */
	public function __construct(array $options = array()) {
		$this->setOptions($options);
	}

	/**
	 *
	 * @param array $options
	 * @return \App_Form_Wizard_Page_PageAbstract 
	 */
	public function setOptions(array $options = array()) {
		foreach ($options as $option => $value) {
			$this->setOption($option, $value);
		}
		return $this;
	}

	/**
	 * Set page option
	 *
	 * @param string $option
	 * @param mixed $value
	 * @throws App_Form_Wizard_Exception 
	 * @return void
	 */
	public function setOption($option, $value) {
		switch ($option) {
			case self::OPTION_FORMS:
				$this->setSubForms($value);
				break;

			case self::OPTION_NAME:
				$this->setName($value);
				break;

			default:
				throw new App_Form_Wizard_Exception("Unknown option: $option");
				break;
		}
	}

}