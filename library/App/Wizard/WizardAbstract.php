<?php

/*
 * WizardAbstract.php created 2012-02-26 14:47:11
 *
 * @category   App_Wizard
 * @package    App_Wizard
 * @version    $Id$
 * @author     Wojciech Zalewski
 */

abstract class App_Form_Wizard_WizardAbstract {

	const FORM_NEXT_BUTTON = 'wizard_go_next';
	const FORM_PREV_BUTTON = 'wizard_go_back';
	const FORM_CANCEL_BUTTON = 'wizard_cancel';

	/**
	 *
	 * @var App_Form_Wizard_Pages 
	 */
	protected $_pages;

	/**
	 *
	 * @var App_Form_Wizard_Session 
	 */
	protected $_namespace;

	/**
	 *
	 * @var App_Form_Wizard_Result 
	 */
	protected $_result;

	/**
	 *
	 * @var string 
	 */
	protected $_defaultNamespaceName;

	/**
	 * Is wizard completed
	 *
	 * @var bool 
	 */
	protected $_completed = false;

	/**
	 *
	 * @var string 
	 */
	public static $formAction;

	/**
	 *
	 * @var string 
	 */
	public static $formCancelAction;

	/**
	 * 
	 * @return App_Form_Wizard_Result
	 */
	public function getResult() {
		if (null === $this->_result)
			throw new App_Form_Wizard_Exception('Results not prepared yet');

		return $this->_result;
	}

	/**
	 * Process request response
	 *
	 * @param Zend_Controller_Request_Abstract|array $data 
	 */
	public function process($data) {
		if ($data instanceof Zend_Controller_Request_Abstract)
			$data = $data->getPost();

		/**
		 * Handle cancel post. 
		 */
		if ($this->_isCancelled($data))
			return $this->_cancelWizardAndRedirect();

		/**
		 * Get current page now, to set proper internal pointer in Pages container 
		 */
		$currentPage = $this->getCurrentPage();

		/**
		 * Handle go back post 
		 */
		if ($this->_isGoToPrevious($data)) {
			$previousPage = $this->getPages()->getPreviousPage();
			if (false !== $previousPage) {
				/**
				 * Populate page form with data found in session (if found) 
				 */
				$this->_populate($previousPage);
				/**
				 * Set current page 
				 */
				$this->getNamespace()
						->setCurrentPage(
								$previousPage->getName()
						)
				;
				return;
			}
			// just in case
			$this->getPages()->setCurrentPage($currentPage->getName());
		}

		/**
		 * Note:
		 * Assign to var, calling it second time 
		 * will couse pointer to move to next page. 
		 */
		if (false === $currentPage->getPageForm()->isValid($data))
			return;

		// if was cliecked next.
		// current page form is valid, set pointer to next page, save VALID posted data.
		$this->_store($currentPage);

		/**
		 * Move to next page if was cliecked next.
		 */
		$nextPage = $this->getPages()->getNextPage();
		if (false === $nextPage)
			return $this->_completed = true;

		/**
		 * Populate with data from session if exists 
		 */
		$this->_populate($nextPage);
		/**
		 * Set current page name in session 
		 */
		$this->getNamespace()->setCurrentPage(
				$nextPage->getName()
		);
	}

	/**
	 *
	 * @param App_Form_Wizard_Page_PageAbstract $page 
	 */
	protected function _setPageNavigation(App_Form_Wizard_Page_PageAbstract $page) {
		
	}

	/**
	 *
	 * @param App_Form_Wizard_Page_PageAbstract $page 
	 * @return void
	 */
	protected function _populate(App_Form_Wizard_Page_PageAbstract $page) {
		$storedValues = $this->getNamespace()
				->getStoredFormData($page->getName())
		;
		if (0 === count($storedValues))
			return;

		$page->getPageForm()->populate($storedValues);
	}

	/**
	 * Stores valid page form in namespace for later to retrieve valid data
	 * 
	 * @param App_Form_Wizard_Page_PageAbstract $page 
	 * @return void
	 */
	protected function _store(App_Form_Wizard_Page_PageAbstract $page) {
		$this->getNamespace()->storeForm($page->getName(), $page->getPageForm());
	}

	/**
	 * Was wizard cancelled.
	 * 
	 * @param array $data
	 * @return bool 
	 */
	protected function _isCancelled(array $data) {
		return array_key_exists(self::FORM_CANCEL_BUTTON, $data);
	}

	/**
	 * User clicked on go back button?
	 *
	 * @param array $data
	 * @return bool 
	 */
	protected function _isGoToPrevious(array $data) {
		return array_key_exists(self::FORM_PREV_BUTTON, $data);
	}

	/**
	 * Clears wizard session and redirects if cancel action was set.
	 *
	 * @return void 
	 */
	protected function _cancelWizardAndRedirect() {
		$this->getNamespace()->clearAll();

		if (null !== self::getFormCancelActionUrl())
			return Zend_Controller_Front::getInstance()
							->getResponse()
							->setRedirect(self::getFormCancelActionUrl(), 302)
			;
	}

	/**
	 * Does wizard completed?
	 * 
	 * @return boolean 
	 */
	public function completed() {
		return $this->_completed;
	}

	/**
	 * Renders current wizard page
	 *
	 * @return string 
	 */
	public function render() {
		if (false === $this->_completed)
			return $this->getCurrentPage()->render();

		return ''; // nothing to render, form complted, 
		//could use view script to render 
		//which should be configurable by factory method.
	}

	/**
	 *
	 * @return App_Form_Wizard_Page 
	 */
	public function getCurrentPage() {
		if (null === $this->getNamespace()->getCurrentPage())
			$this->getNamespace()->setCurrentPage(
					$this->getPages()->getFirstPage()->getName()
			);


		$this->getPages()->setCurrentPage(
				$this->getNamespace()->getCurrentPage()
		);
		
		$page = $this->getPages()->getPage(
						$this->getNamespace()->getCurrentPage()
		);
		/**
		 * Prepare page navigation 
		 */
		return $page;
	}

	/**
	 * Returns wizard session namespace
	 * 
	 * @return App_Form_Wizard_Session 
	 */
	public function getNamespace() {
		if (null === $this->_namespace) {
			$this->_namespace = new App_Form_Wizard_Session(
							$this->getNamespaceName()
			);
		}
		return $this->_namespace;
	}

	/**
	 * Returns namespace name
	 * 
	 * @return string 
	 */
	public function getNamespaceName() {
		if (null !== $this->_defaultNamespaceName)
			return $this->_defaultNamespaceName;

		return __CLASS__;
	}

	/**
	 * Sets default namespace name to use
	 * 
	 * @param string $name
	 * @return \App_Form_Wizard 
	 */
	public function setDefaultNamespaceName($name) {
		$this->_defaultNamespaceName = $name;
		return $this;
	}

	/**
	 * Sets pages
	 *
	 * @param array|App_Form_Wizard_Pages $pages
	 * @return \App_Form_Wizard
	 * @throws App_Form_Wizard_Exception 
	 */
	public function setPages($pages) {
		if ($pages instanceof App_Form_Wizard_Pages)
			$this->_pages = $pages;

		if (is_array($pages))
			$this->_pages = new App_Form_Wizard_Pages($pages);

		if (null === $pages)
			throw new App_Form_Wizard_Exception("Invalid options given for pages");

		return $this;
	}

	/**
	 *
	 * @return App_Form_Wizard_Pages 
	 */
	public function getPages() {
		return $this->_pages;
	}

	/**
	 * Sets form action url
	 *
	 * @param type $formAction
	 */
	public function setFormAction($formAction) {
		if ($formAction instanceof Zend_Controller_Request_Abstract)
			$formAction = self::getUrlHelper()->url($formAction->getParams(), null, true);

		self::$formAction = $formAction;
	}

	/**
	 * Returns form action url
	 *
	 * @return string 
	 */
	public static function getFormActionUrl() {
		return self::$formAction;
	}

	/**
	 *
	 * @param type $formCancelAction
	 */
	public function setFormCancelAction($formCancelAction) {
		if ($formCancelAction instanceof Zend_Controller_Request_Abstract)
			$formCancelAction = self::getUrlHelper()->url($formAction->getParams(), null, true);

		self::$formCancelAction = $formCancelAction;
	}

	/**
	 * Returns form cancel action url
	 *
	 * @return string 
	 */
	public static function getFormCancelActionUrl() {
		return self::$formCancelAction;
	}

	/**
	 *
	 * @return \Zend_View_Helper_Url 
	 */
	public static function getUrlHelper() {
		return new Zend_View_Helper_Url();
	}

	/**
	 *
	 * @return string 
	 */
	public function __toString() {
		return $this->render();
	}

}