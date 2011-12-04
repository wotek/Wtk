<?php

/*
 * Wkhtmltopdf.php
 * 
 * @url        http://code.google.com/p/wkhtmltopdf/wiki/Usage
 * @category   App
 * @package    App_Pdf
 * @version    $Id: $
 * @copyright  Copyright (c) 2011 Wojciech Zalewski (www.wotek.info)
 */

class App_Pdf_Adapter_Wkhtmltopdf extends App_Pdf_Adapter_Abstract {
	const PDF_EXTENSION = 'pdf';
	/**
	 * tmp folder
	 */
	const TMP_FOLDER = '/tmp';
	/**
	 * command name
	 */
	const COMMAND = 'wkhtmltopdf';
	/**
	 * Some command line options:
	 */
	const PDF_GRAYSCALE = '-g';
	const PDF_ORIENTATION = '-O';
	const PDF_ORIENTATION_LANDSCAPE = 'Landscape';
	const PDF_ORIENTATION_PORTRAIT = 'Portrait';
	const PDF_PAGE_SIZE = '-s';
	const PDF_PAGE_SIZE_A4 = 'A4';
	const PDF_PAGE_SIZE_LETTER = 'Letter';
	const PDF_LOWQUALITY = '-l';
	const PDF_DPI = '-d';
	const PDF_DEFAULT_DPI = 300;
	const PDF_HEADER_LEFT = '--header-left';
	const PDF_HEADER_RIGHT = '--header-right';
	const PDF_DEFAULT_HEADER = '--default-header';
	/**
	 *
	 * @var string 
	 */
	protected $_command = self::COMMAND;
	/**
	 *
	 * @var string 
	 */
	protected $_commandPath;
	/**
	 *
	 * @var string 
	 */
	protected $_tmp = self::TMP_FOLDER;
	/**
	 * Destination directory
	 * 
	 * @var string
	 */
	protected $_destination;
	/**
	 *
	 * @var type 
	 */
	public static $_logFile;
	/**
	 *
	 * @var bool 
	 */
	protected $_forceDownload = false;
	/**
	 *
	 * @var bool 
	 */
	protected $_cleanup = false;
	/**
	 *
	 * @var bool 
	 */
	protected $_grayscale = false;
	/**
	 *
	 * @var string 
	 */
	protected $_orientation = self::PDF_ORIENTATION_PORTRAIT;
	/**
	 *
	 * @var string 
	 */
	protected $_pageSize = self::PDF_PAGE_SIZE_A4;
	/**
	 *
	 * @var string 
	 */
	protected $_leftHeader;
	/**
	 *
	 * @var string 
	 */
	protected $_rightHeader;
	/**
	 *
	 * @var bool 
	 */
	protected $_isLowQuality = false;
	/**
	 *
	 * @var int 
	 */
	protected $_dpi = self::PDF_DEFAULT_DPI;
	/**
	 *
	 * @var string 
	 */
	protected $_filename;
	/**
	 *
	 * @var string 
	 */
	protected $_document;
	/**
	 *
	 * @var string 
	 */
	protected $_randomFilename;

	/**
	 *
	 * @param mixed $config 
	 */
	public function __construct($config) {
		parent::__construct($config);
		/**
		 * Run checks:
		 */
		$this->_checkRequiredOptions();
	}

	/**
	 * Run class checks
	 * 
	 * @throws App_Pdf_Exception
	 * @return void
	 */
	public function _checkRequiredOptions() {
		$this->_checkIfCommandExists();
		$this->_checkTemporaryDirectory();
	}

	/**
	 *
	 * @param boolean $value 
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setForceDownload($value = true) {
		$this->_forceDownload = (bool) $value;
		return $this;
	}

	/**
	 * Is forced to download after converting
	 *
	 * @return bool 
	 */
	public function getForceDownload() {
		return $this->_forceDownload;
	}

	/**
	 *
	 * @return bool 
	 */
	public function getCleanUp() {
		return $this->_cleanup;
	}

	/**
	 *
	 * @param bool $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setCleanUp($value) {
		$this->_cleanup = $value;
		return $this;
	}

	/**
	 *
	 * @param bool $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setGrayscale($value) {
		$this->_grayscale = (bool) $value;
		return $this;
	}

	/**
	 *
	 * @return bool 
	 */
	public function getIsGrayscale() {
		return $this->_grayscale;
	}

	/**
	 * Sets page orientation
	 *
	 * @param string $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setPageOrientation($value) {
		$this->_orientation = $value;
		return $this;
	}

	/**
	 *
	 * @return string 
	 */
	public function getPageOrientation() {
		return ucfirst(strtolower($this->_orientation));
	}

	/**
	 * Sets page size
	 *
	 * @param string $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setPageSize($value) {
		$this->_pageSize = $value;
		return $this;
	}

	/**
	 *
	 * @param string $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setLeftHeader($value) {
		$this->_leftHeader = $value;
		return $this;
	}

	/**
	 *
	 * @return string 
	 */
	public function getLeftHeader() {
		return $this->_leftHeader;
	}

	/**
	 *
	 * @param string $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setRightHeader($value) {
		$this->_rightHeader = $value;
		return $this;
	}

	/**
	 *
	 * @return string 
	 */
	public function getRightHeader() {
		return $this->_rightHeader;
	}

	/**
	 * Gets page size (default: A4)
	 *
	 * @return string 
	 */
	public function getPageSize() {
		return $this->_pageSize;
	}

	/**
	 * Set low quality
	 * 
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setIsLowQuality($value) {
		$this->_isLowQuality = (bool) $value;
		return $this;
	}

	/**
	 * Generate low quality?
	 *
	 * @return type 
	 */
	public function getIsLowQuality() {
		return $this->_isLowQuality;
	}

	/**
	 * Sets pdf dpi
	 *
	 * @param int $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setDpi($value) {
		$this->_dpi = $value;
		return $this;
	}

	/**
	 * Returns dpi setting
	 * 
	 * @return int 
	 */
	public function getDpi() {
		return $this->_dpi;
	}

	/**
	 * Set temporary files folder
	 * 
	 * @param string $value 
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setTmpFolder($value) {
		$this->_tmp = $this->_checkTemporaryDirectory($value);
		return $this;
	}

	/**
	 * Returns Temp folder settings
	 */
	public function getTmpFolder() {
		return $this->_tmp;
	}
	
	/**
	 *
	 * @param string $destination 
	 */
	public static function setLogfile($destination) {
		$file = new SplFileInfo($destination);
		
		if (false === realpath($file->getPathInfo()->__toString())
				|| false === $file->getPathInfo()->isWritable())
			throw new App_Pdf_Adapter_Exception('Given destination is invalid (not writeable, or does not exists) : '.$destination);

		if (false === $file->isFile())
			throw new App_Pdf_Adapter_Exception('Well, did you forgot about the log file filename? Given destination is a directory.');

		self::$_logFile = $file->getRealPath();
	}

	/**
	 *
	 * @return string 
	 */
	public static function getLogfile() {
		if (null === self::$_logFile)
			return null;

		return self::$_logFile;
	}

	/**
	 * Check if tmp folder is set and is writeable
	 * 
	 * @return string Tmp folder path
	 * @throws App_Pdf_Adapter_Exception 
	 */
	protected function _checkTemporaryDirectory($path = null) {
		if (null === $path)
			$path = $this->getTmpFolder();

		if (!is_writeable(realpath($path)))
			throw new App_Pdf_Adapter_Exception('Temporary directory not set or is not writeable');

		return realpath($path);
	}

	/**
	 * Set output filename
	 * 
	 * @param string $filename
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setFilename($filename) {
		$file = new SplFileInfo($filename);

		if (self::PDF_EXTENSION !== $extension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION)))
			throw new App_Pdf_Adapter_Exception("File extension not provided or is not valid. Discovered extension: $extension");

		$this->_filename = $file->getFilename();
		return $this;
	}

	/**
	 * Returns output filename
	 * 
	 * @return string Full path to output filename
	 */
	public function getFilename() {
		if (null === $this->_filename) {
			$this->_filename = $this->_getRandomFileName();
		}
		return $this->_filename;
	}

	/**
	 * Get full destination path
	 * 
	 * @return string
	 */
	public function getOutputFileDestination() {
		// if destination is set use it
		$path = $this->getDestination();
		if (!$path)
			$path = $this->getTmpFolder();

		$filename = $this->getFilename();
		return $path . DIRECTORY_SEPARATOR . $filename;
	}

	/**
	 *
	 * @param string $destination
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 * @throws App_Pdf_Adapter_Exception
	 */
	public function setDestination($destination) {
		$path = realpath($destination);
		if (!is_writeable($path))
			throw new App_Pdf_Adapter_Exception('Given destination path does not exists or it is not writeable ()'.$destination);

		$this->_destination = $path;
		return $this;
	}

	/**
	 *
	 * @return string 
	 */
	public function getDestination() {
		return $this->_destination;
	}

	/**
	 *
	 * @return string Random filename 
	 */
	protected function _getRandomFileName() {
		if (null === $this->_randomFilename) {
			$this->_randomFilename = mt_rand() . '.' . self::PDF_EXTENSION;
		}
		return $this->_randomFilename;
	}

	/**
	 * Set document.
	 * @todo: strategy depending on param -> move to Abstract
	 * 
	 * @param string $document
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setDocument($document) {
		$this->_document = new DOMDocument();
		// suppress any error for now
		libxml_clear_errors();
		@$this->_document->loadHTML($document);
		// handle errors if there were any
		if (false !== $error = libxml_get_last_error())
			throw new App_Pdf_Adapter_Exception("Document provided is not valid. Last error message: $error->message");

		return $this;
	}

	/**
	 * Get document content
	 *
	 * @return DOMDocument 
	 */
	public function getDocument() {
		return $this->_document;
	}

	/**
	 * Get pipes settings
	 *
	 * @return array
	 */
	private function getPipeSettings() {
		$settings = array(
			0 => array("pipe", "r"), // stdin is a pipe that the child will read from
			1 => array("pipe", "w"), // stdout is a pipe that the child will write to
			2 => array("pipe", "w"), // stdout is a pipe that the child will write to
		);

//		if (self::getLogfile())
//			$settings[] = array("file", self::getLogfile(), "a"); // stderr is a file to write to

		return $settings;
	}

	/**
	 * Execute wkhtmltopdf command to generate pdf file
	 * 
	 * @param array $args
	 * @return string Generated pdf filename 
	 */
	public function exec() {
		// open process reprint.pl and pass it an argument
		$process = proc_open(escapeshellcmd($this->_prepareCommand()), $this->getPipeSettings(), $pipes);

		stream_set_blocking($pipes[2], 0);

		if (is_resource($process)) {
			// if we set to get errors from STDERR and there is something, then throwing an expetion
			if (stream_get_contents($pipes[2])) {
				throw new App_Pdf_Adapter_Exception('An error occurred while creating a process.');
			}
			// Send the HTML on stdin
			fwrite($pipes[0], $this->getDocument()->saveHTML());
			fclose($pipes[0]);
			// Open file
			$file = fopen($this->getOutputFileDestination(), 'w');
			// Read the outputs
			fwrite($file, stream_get_contents($pipes[1]));
			// Close file
			fclose($file);
			// Close the process
			fclose($pipes[1]);
			// close process
			$result = proc_close($process);
			// Check file size
			if (0 === filesize($this->getOutputFileDestination()))
				throw new App_Pdf_Adapter_Wkhtmltopdf_Exception('An error occurred: It seems that generated file is empty.');

			return $this->getOutputFileDestination();
		}
		throw new App_Pdf_Adapter_Exception('Unexpected error, could not execute command line script.');
	}

	/**
	 * Convert document to PDF
	 * 
	 * @return mixed
	 */
	public function generate() {
		if (null === $this->getDocument())
			throw new App_Pdf_Exception('Set document body!');

		$filename = $this->exec();
		if ($filename)
			if ($this->getForceDownload())
				return $this->_outputFileToBrowser($filename);
			else
				return $filename;
	}

	protected function _outputFileToBrowser($filepath) {
		return $filepath;
	}

	/**
	 * Set command full path
	 *
	 * @param string $value
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	protected function setCommand($value) {
		$this->_command = trim($value);
		return $this;
	}

	/**
	 * Returns command full path
	 *
	 * @return string
	 */
	public function getCommand() {
		return $this->_command;
	}

	/**
	 * Set command directory path
	 *
	 * @param string $path
	 * @return App_Pdf_Adapter_Wkhtmltopdf 
	 */
	public function setCommandPath($path) {
		if (false === realpath($path))
			throw new App_Pdf_Adapter_Exception("Invalid path: $path");

		$this->_commandPath = realpath($path);
		return $this;
	}

	/**
	 * Returns command location path
	 *
	 * @return string
	 */
	public function getCommandPath() {
		if (null === $this->_commandPath) {
			$this->setCommandPath($this->getDefaultCommandPath());
		}

		return $this->_commandPath;
	}

	/**
	 * Returns default command path
	 *
	 * @return string 
	 */
	public function getDefaultCommandPath() {
		return APPLICATION_PATH . '/../scripts/';
	}

	/**
	 * Returns complete command string with options read to execute
	 * 
	 * @param type $args 
	 * @return string prepared command string
	 */
	private function _prepareCommand() {
		$args = array(
			self::PDF_DPI => $this->getDpi(),
			self::PDF_GRAYSCALE => $this->getIsGrayscale(),
			self::PDF_HEADER_LEFT => null,
			self::PDF_HEADER_RIGHT => null,
			self::PDF_LOWQUALITY => $this->getIsLowQuality(),
			self::PDF_ORIENTATION => $this->getPageOrientation(),
			self::PDF_PAGE_SIZE => $this->getPageSize()
		);
		$args = array_filter($args, create_function('$v', 'return !(false === $v);'));
		$args = array_filter($args, create_function('$v', 'return !is_null($v);'));

		$options = array_keys($args);

		array_walk($options, create_function('&$v,$k', '$v = $v . " %s";'));
		return vsprintf($this->_command . ' ' . implode(' ', $options) . ' - - ', array_values($args));
	}

	/**
	 * Sanitize args array
	 *
	 * @param type $args 
	 * @return array
	 */
//	private function _sanitizeInputArgs($args) {
//		// @todo: implement
//	}

	/**
	 * Check for command presence and executable property in given location
	 * 
	 * @return bool
	 */
	private function _checkIfCommandIsPresentInPathAndIsExecutable() {
		return ((is_executable($this->_getFullPathToCommand())) ? true : false);
	}

	/**
	 * Returns full path to command
	 * 
	 * @return string
	 */
	private function _getFullPathToCommand() {
		return realpath($this->getCommandPath() . DIRECTORY_SEPARATOR . $this->getCommand());
	}

	/**
	 * Check if wkhtmltopdf command exists in filesystem
	 * 
	 * If exists, returns path to command
	 * If not, returns null
	 *
	 * @return mixed 
	 * @throws App_Pdf_Adapter_Wkhtmltopdf_Exception
	 */
	public function _checkIfCommandExists() {
		/**
		 * If not found in system, look for script in provided path
		 * 
		 * @todo: change behaviour :
		 * 1. look locally
		 * 2. look in system
		 */
//		if (null === $command = shell_exec('command -v wkhtmltopdf &>/dev/null')) {
		$command = $this->_checkIfCommandIsPresentInPathAndIsExecutable();
		if (!$command) {
			throw new App_Pdf_Adapter_Wkhtmltopdf_Exception(
					sprintf('wkhtmltopdf script not present in system and 
						was not found in provided path'
					)
			);
		}
//		}
		$this->setCommand($this->_getFullPathToCommand());
		return $command;
	}

}