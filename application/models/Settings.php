<?php
class Settings extends GeneralModelsController {
	
	public function init() {
	}

	public function get($type) {
		$this->setQuery("SELECT * FROM $this->_name WHERE type='$type'");
		$this->fetchRow();
		return $this->data;
	}
}
?>