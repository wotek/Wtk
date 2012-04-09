<?php

/*
 * Session.php created 2012-03-04 20:51:17
 *
 * @category   App_Wizard
 * @package    App_Wizard
 * @version    $Id$
 * @author     Wojciech Zalewski
 */

class App_Form_Wizard_Session {

	const NAMESPACE_CURRENT_PAGE = 'current_page';
	const NAMESPACE_STORED_FORMS = 'stored_forms';

	/**
	 *
	 * @var Zend_Session_Namespace 
	 */
	protected static $_session;

	/**
	 * Session namespace
	 *
	 * @var string 
	 */
	public static $sessionNamespace;

	/**
	 * Data stored from processed forms
	 *
	 * @var array 
	 */
	protected $_storedForms = array();

	/**
	 *
	 * @var string 
	 */
	protected $_currentPage;

	/**
	 *
	 * @param string $sessionNamespace 
	 */
	public function __construct($sessionNamespace) {
		/**
		 * Set session namespace
		 */
		self::$sessionNamespace = $sessionNamespace;
		/**
		 * Load session data if any
		 */
		$this->_loadSession();
	}

	/**
	 * Returns session obj
	 *
	 * @return Zend_Session_Namespace 
	 */
	protected static function _getSessionNamespace() {
		if (null === self::$_session) {
			self::$_session = new Zend_Session_Namespace(
							self::$sessionNamespace
			);
		}
		return self::$_session;
	}

	/**
	 *
	 * @param string $pageName
	 * @return array
	 */
	public function getStoredFormData($pageName) {
		if (!array_key_exists($pageName, $this->_storedForms))
			return array();

		if (is_array($this->_storedForms[$pageName]))
			return $this->_storedForms[$pageName];
	}

	/**
	 * Returns stored forms data array
	 *
	 * @return array 
	 */
	public function getStoredForms() {
		return $this->_storedForms;
	}

	/**
	 *
	 * @param string $pageName
	 * @param App_FormAbstract $form
	 * @return \App_Form_Wizard_Session 
	 */
	public function storeForm($pageName, App_FormAbstract $form) {
		$this->_storedForms[$pageName] = $form->getValues();
		$this->persist();
		return $this;
	}

	/**
	 * Clear any stored forms data
	 *
	 * @return \App_Form_Wizard_Session 
	 */
	public function clearStoredForms() {
		$this->_storedForms = array();
		$this->persist();
		return $this;
	}

	/**
	 * Sets current page
	 *
	 * @param string $page
	 * @return \App_Form_Wizard_Session 
	 */
	public function setCurrentPage($page) {
		$this->_currentPage = $page;
		$this->persist();
		return $this;
	}

	/**
	 * Returns current page
	 * 
	 * @return string 
	 */
	public function getCurrentPage() {
		return $this->_currentPage;
	}

	/**
	 *
	 * @return \App_Form_Wizard_Session 
	 */
	public function resetCurrentPage() {
		$this->_currentPage = null;
		$this->persist();
		return $this;
	}

	/**
	 * Loads vars from session into object instance
	 * 
	 * @return void
	 */
	protected function _loadSession() {
		if (isset(self::_getSessionNamespace()->{self::NAMESPACE_CURRENT_PAGE})) {
			$this->_currentPage = self::_getSessionNamespace()->{self::NAMESPACE_CURRENT_PAGE};
		}

		if (isset(self::_getSessionNamespace()->{self::NAMESPACE_STORED_FORMS})) {
			$this->_storedForms = self::_getSessionNamespace()->{self::NAMESPACE_STORED_FORMS};
		}
	}

	/**
	 * Persis session
	 * 
	 * @return \App_Form_Wizard_Session 
	 */
	public function persist() {
		self::_getSessionNamespace()
				->{self::NAMESPACE_CURRENT_PAGE} = $this->getCurrentPage()
		;
		self::_getSessionNamespace()
				->{self::NAMESPACE_STORED_FORMS} = $this->getStoredForms()
		;
		return $this;
	}

	/**
	 * Clears all data from session
	 *
	 * @return \App_Form_Wizard_Session 
	 */
	public function clearAll() {
		self::_getSessionNamespace()->unsetAll();
		return $this;
	}

}