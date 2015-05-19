<?php
class Loader extends Library {
	private $_prefix;
	private $_directory;
	private $_valid = false;

	public function __construct($prefix, $directory) {
		$this->_prefix = (string)$prefix;
		
		if(!is_dir($directory)) ;//$this->_errors->set('<p>katalog nie istnieje!</p>');

		$this->_directory = $directory;
		$this->_valid = true;
	}

	public function autoload($className) {
		if(strlen($this->_prefix)>0 && strpos($className, $this->_prefix) === 0) return false;
		if(file_exists($this->_directory.$className.'.php')) {
			require_once($this->_directory.$className.'.php');
			return true;
		}
	}

	public function register() {
		if($this->_valid) {
			spl_autoload_register(array($this, 'autoload'));
			$this->_valid = false; 
		}
	}
}
?>