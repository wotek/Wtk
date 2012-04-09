<?php

/*
 * PageAbstract.php created 2012-02-26 15:05:40
 *
 * @category   App_Wizard
 * @package    App_Wizard
 * @version    $Id$
 * @author     Wojciech Zalewski
 */

abstract class App_Form_Wizard_Page_PageAbstract {

	const OPTION_FORMS = 'forms';
	const OPTION_NAME = 'page_name';

	/**
	 *
	 * @var array 
	 */
	protected $_forms = array();

	/**
	 *
	 * @var App_Form_Wizard_Page_Form 
	 */
	protected $_form;

	/**
	 * Is page valid
	 *
	 * @var bool 
	 */
	protected $_isValid;

	/**
	 * This page name
	 * 
	 * @var string 
	 */
	protected $_pageName;

	/**
	 *
	 * @var App_Form_Wizard_Page_Navigation 
	 */
	protected $_navigation;

	/**
	 *
	 * @var string 
	 */
	protected $_previousPage;

	/**
	 *
	 * @var type 
	 */
	protected $_nextPage;

	/**
	 * Sets page name
	 * 
	 * @param string $name
	 * @return \App_Form_Wizard_Page_PageAbstract 
	 */
	public function setName($name) {
		$this->_pageName = $name;
		return $this;
	}

	/**
	 *
	 * @return string 
	 */
	public function getName() {
		return $this->_pageName; // should check if was set
	}

	/**
	 * Returns this page forms
	 *
	 * @return array 
	 */
	public function getSubForms() { // @todo: rename to getSubForms()
		return $this->_forms;
	}

	/**
	 * Set forms for this page
	 *
	 * @param array $forms Array of form instances (App_FormAbstract | App_Form_SubForm )
	 * @return \App_Form_Wizard_Page_PageAbstract 
	 */
	public function setSubForms(array $forms = array()) {
		foreach ($forms as $form) {
			$this->addSubForm($form);
		}
		return $this;
	}

	/**
	 * Add new form/subform for this page
	 *
	 * @param Zend_Form $form
	 * @return \App_Form_Wizard_Page_PageAbstract 
	 */
	public function addSubForm(Zend_Form_SubForm $form) {
		if ($form instanceof App_FormAbstract)
			$form->convertToSubForm();

		$this->_forms[self::getFormName($form)] = $form;
		return $this;
	}

	/**
	 *
	 * @param Zend_Form $form
	 * @return string 
	 */
	public static function getFormName(Zend_Form $form) {
		if (null === $form->getName())
			return get_class($form);

		return $form->getName();
	}

	/**
	 *
	 * @param string $formName
	 * @return Zend_Form
	 * @throws App_Form_Wizard_Exception 
	 */
	public function getSubForm($formName) {
		if (array_key_exists($formName, $this->_forms))
			return $this->_forms[$formName];

		throw new App_Form_Wizard_Exception("Form with name $formName doesn't exists");
	}

	/**
	 * Return main form on this page, which contains all subforms.
	 *
	 * @return App_Form_Wizard_Page_Form 
	 */
	public function getPageForm() {
		if (null === $this->_form) {
			$this->_form = new App_Form_Wizard_Page_Form();
			$this->_form->setAction(
					App_Form_Wizard_WizardAbstract::getFormActionUrl()
			);
			/**
			 * Bulk add those forms 
			 */
			$this->_form->addSubForms($this->getSubForms());
			/**
			 * Add some buttons: prev/next/finish/cancel 
			 */
			foreach ($this->getNavigation()->getElements() as $name => $element) {
				$this->_form->addToActionFieldset($element);
			}
		}

		return $this->_form;
	}

	/**
	 * Renders current page.
	 * 
	 * @return \App_Form_Wizard_Page_Form 
	 */
	public function render() {
		/**
		 * Create form to capture all page subforms 
		 */
		return $this->getPageForm();
	}

	/**
	 * Set page navigation
	 *
	 * @param array $navigation 
	 */
	public function setNavigation(array $navigation) {
		// @todo: implement, to set own navigation buttons/links per page.
		$this->_navigation = $navigation;
		return $this;
	}

	/**
	 * Returns page navigation
	 *
	 * @return App_Form_Wizard_Page_Navigation 
	 */
	public function getNavigation() {
		if (null === $this->_navigation) {
			$this->_navigation = new App_Form_Wizard_Page_Navigation(
					array()
			);
		}
		
		if(null === $this->_previousPage)
			$this->_navigation->disableElement (App_Form_Wizard_Page_Navigation::BACK);

		return $this->_navigation;
	}

	/**
	 * Proxy to render()
	 * 
	 * @return type 
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 *
	 * @param string $page
	 * @return \App_Form_Wizard_Page_PageAbstract 
	 */
	public function setPreviousPage($page) {
		$this->_previousPage = $page;
		return $this;
	}

	/**
	 *
	 * @param string $page
	 * @return \App_Form_Wizard_Page_PageAbstract 
	 */
	public function setNextPage($page) {
		$this->_nextPage = $page;
		return $this;
	}
}