<?php

/*
 * Navigation.php created 2012-03-06 14:26:06
 *
 * @category   App_Wizard
 * @package    App_Wizard
 * @version    $Id$
 * @author     Wojciech Zalewski
 */

class App_Form_Wizard_Page_Navigation {

	const NEXT = 'next';
	const BACK = 'back';
	const CANCEL = 'cancel';

	/**
	 *
	 * @var array 
	 */
	protected $_elements = array();

	/**
	 * 
	 */
	public function __construct(array $options = array()) {
		$this->setOptions($options);
		
		if(empty($this->_elements))
			$this->_elements = $this->getDefaultElements ();

	}

	/**
	 *
	 * @param array $options 
	 */
	public function setOptions(array $options) {
		foreach($options as $option => $value) {
			$this->setOption($option, $value);
		}
	}

	/**
	 *
	 * @param string $option
	 * @param mixed $value 
	 */
	public function setOption($option, $value) {
		// todo. really todo, to be able to handle links/comments/ in form_action area.
	}

	/**
	 * Returns wizard default navigation buttons
	 *
	 * @return array 
	 */
	public function getDefaultElements() {
		return array(
			self::CANCEL => self::getCancelButton(),
			self::BACK => self::getBackButton(),
			self::NEXT => self::getNextButton(),
		);
	}

	/**
	 *
	 * @param Zend_Form_Element $element
	 * @return \App_Form_Wizard_Page_Navigation 
	 */
	public function addElement(Zend_Form_Element $element) {
		array_push($this->_elements, $element);
		return $this;
	}

	/**
	 *
	 * @param string $element 
	 * @return void
	 */
	public function disableElement($element) {
		if (array_key_exists($element, $this->_elements))
			unset($this->_elements[$element]);
	}

	/**
	 *
	 * @return array 
	 */
	public function getElements() {
		return $this->_elements;
	}
	
	/**
	 *
	 * @return \App_Form_Element_Back 
	 */
	public static function getBackButton() {
		return new App_Form_Element_Back(
						App_Form_Wizard_WizardAbstract::FORM_PREV_BUTTON,
						array(
							'label' => _('Back') // should be configurable.
						)
		);
	}

	/**
	 *
	 * @return \App_Form_Element_Next 
	 */
	public static function getNextButton($label = 'Next') {
		return new App_Form_Element_Next(
						App_Form_Wizard_WizardAbstract::FORM_NEXT_BUTTON,
						array(
							'label' => _('Next') // to translate
						)
		);
	}

	/**
	 *
	 * @return \App_Form_Element_Cancel 
	 */
	public static function getCancelButton() {
		return new App_Form_Element_Cancel(
						App_Form_Wizard_WizardAbstract::FORM_CANCEL_BUTTON,
						array(
							'label' => _('Cancel')
						)
		);
	}

}