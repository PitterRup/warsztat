<?php
class ExceptionsLibrary extends Library {
	private $_exceptions;

	public function __construct() {
		$this->_exceptions = array();
	}

	public function check() {
		$fileName = $this->_filter->cleanREQUEST($_SERVER['REQUEST_URI']);
		$fileName = substr(str_replace($this->_request->path,"", $fileName),1);

		if(in_array($fileName,$this->_exceptions)) {
			// otworzenie strony
			die;
		}
	}
}
?>