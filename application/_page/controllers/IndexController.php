<?php
class IndexController extends PageController {
	public function init() {
	}


	public function indexAction() {
		$this->_layout->off();
		$this->view->on = false;
		$this->_request->goToAddress($this->baseUrl.'/naprawa/index',0);
	}

	public function loginAction() { 
		$this->_layout->set("noAuthorized");
		$this->_linkScript($this->baseUrl.'/public/template/styles/_page/form.css');
		if($this->_isPost()) {
			$authorize = new Authorize($this->_config->dbTableNames->Klient);
			if($authorize->logIn()) {
				$this->_layout->off();
				$this->view->on = false;
				$this->_request->goToAddress($this->baseUrl.'/naprawa/index',0);
			}
			else $this->msg(false,"Podane dane są nieprawidłowe.");
		}
	}
	public function logoutAction() {
		$this->_layout->off();
		$this->view->on = false;
		$authorize = new Authorize($this->_config->dbTableNames->Klient);
		$authorize->logOut();

		$this->_request->goToAddress($this->directoryUrl,0);
	}


	public function accessControlAction() {
		$this->view->text = 'Nie masz dostępu do tej części panelu.';
	}
}
?>