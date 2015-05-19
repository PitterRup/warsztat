<?php
// generalny kontroller kontrolerów
abstract class GeneralController extends Library {
	public $defaultAction;

	public function __construct() {
		Debug::checkPoint("GeneralController construct",2);
        $this->_controllerName = $this->view->_controllerName = $this->_request->controllerName();
        $this->_actionName = $this->view->_actionName = $this->_request->actionName();
        $this->_lang = $this->view->_lang = $this->_getParam('lang');
        $this->_op = $this->view->_op = $this->_getParam('op');
        $this->_cmd = $this->view->_cmd = $this->_getParam('cmd');
        $this->_id = $this->view->_id = $this->_getParam('id');
        $this->_type = $this->view->_type = $this->_getParam('type');

		$this->commonElements();
		$this->init();
	}

	public abstract function init();
	public abstract function commonElements();
}
?>