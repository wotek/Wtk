<?php

/*
 * Pages.php created 2012-02-26 14:39:14
 *
 * @category   wypozycz.pl
 * @package    wypozycz.pl 
 * @version    $Id$
 * @author     Wojciech Zalewski
 */

class App_Form_Wizard_Pages {

	/**
	 *
	 * @var array 
	 */
	protected $_pages = array();

	/**
	 *
	 * @var string 
	 */
	protected $_start;
	
	/**
	 *
	 * @var string 
	 */
	protected $_end;

	/**
	 *
	 * @var int 
	 */
	protected $_count = 0;

	/**
	 *
	 * @var string 
	 */
	protected $_currentPage;

	/**
	 *
	 * @param array $pages
	 */
	public function __construct(array $pages = array()) {
		$this->setPages($pages);
	}

	/**
	 * Sets pages
	 *
	 * @param array $options 
	 */
	public function setPages(array $pages = array()) {
		foreach ($pages as $page => $forms) {
			$this->addPage($page, $forms);
		}
		return $this;
	}

	/**
	 * Adds page
	 *
	 * @param string $pageName
	 * @param array $forms 
	 */
	public function addPage($pageName, $forms) {
		/**
		 * Create page, push onto array
		 */
		$this->_pages[$pageName] = $this->_createPageObjectInstance($pageName, $forms);
		//if this is the first element we've added, we need to set the start
		//and end to this one element
		if (null === $this->_start) {
			$this->_start = $pageName;
			$this->_end = $pageName;
			return;
		}
		/**
		 * Last element from list, gets new neighbour yay! 
		 */
		$this->getPage($this->_end)->setNextPage($pageName);
		/**
		 * Current page added gets previous elem. 
		 */
		$this->_pages[$pageName]->setPreviousPage(
				$this->getPage($this->_end)
						->getName()
		);
		/**
		 * I'm the last one.
		 */
		$this->_end = $pageName;
	}

	/**
	 * Creates page obj.
	 *
	 * @param type $pageName
	 * @param type $forms
	 * @return \App_Form_Wizard_Page 
	 */
	protected function _createPageObjectInstance($pageName, $forms) {
		return new App_Form_Wizard_Page(
						array(
							App_Form_Wizard_Page::OPTION_NAME => $pageName,
							App_Form_Wizard_Page::OPTION_FORMS => $forms
						)
		);
	}

	/**
	 * Returns page by given page name
	 *
	 * @param string $pageName
	 * @return App_Form_Wizard_Page
	 * @throws App_Form_Wizard_Exception 
	 */
	public function getPage($pageName) {
		$this->_validatePage($pageName);

		$page = $this->_pages[$pageName];
		return $page;
	}

	/**
	 * Checks if page exists
	 * 
	 * @param mixed $pageName
	 * @throws App_Form_Wizard_Exception 
	 * @return void
	 */
	protected function _validatePage($pageName) {
		if (!array_key_exists($pageName, $this->_pages))
			throw new App_Form_Wizard_Exception("$pageName doesn't exists in wizard pages container.");
	}

	/**
	 * Returns first page
	 * 
	 * @return App_Form_Wizard_Page
	 * 
	 * @todo Add check if any page exists at all. 
	 */
	public function getFirstPage() {
		reset($this->_pages);
		return current($this->_pages);
	}

	/**
	 * Returns next page
	 * 
	 * @return App_Form_Wizard_Page
	 */
	public function getNextPage() {
		return next($this->_pages);
	}

	/**
	 * Returns previous page
	 * 
	 * @return App_Form_Wizard_Page
	 */
	public function getPreviousPage() {
		return prev($this->_pages);
	}

	/**
	 * Does we have any pages at all?
	 *
	 * @return bool 
	 */
	public function hasPages() {
		return (0 < count($this->_pages)) ? true : false;
	}

	/**
	 * 
	 * Sets current page position
	 *
	 * @param mixed $page
	 * @return \App_Form_Wizard_Pages 
	 */
	public function setCurrentPage($page) {
		$this->_validatePage($page);
		$this->_currentPage = $page;
		/**
		 * Reset array pointer 
		 */
		reset($this->_pages);
		/**
		 * Move internal php array pointer to current item 
		 */
		while (key($this->_pages) !== $this->_currentPage)
			next($this->_pages);

		return $this;
	}

}