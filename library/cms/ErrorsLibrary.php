<?php
class ErrorsLibrary {
	private $errors = array();
	public $showLayout = true;

	public function add($error)	{
		$this->errors[] = $error;
	}

	public function getCount() { 
		return count($this->errors); 
	}

	public function getAll() {
		foreach($this->errors as $error) echo '<p>'.$error.'</p>';
	}	

	public function issetErrors() {
		return (count($this->errors)>0) ? true:false;
	}
}
?>