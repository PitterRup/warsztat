<?php
class IndexController extends AdminController {
	public function init() {
		
	}


	public function indexAction() {
		if($this->sesField['funcId']=='2') $link = 'mechanik/zadanialist';
		elseif($this->sesField['funcId']=='3') $link = 'zarzadzanieklientem/customerlist';
		else $link = 'zarzadzanieklientem/customerlist';
		
		$this->_request->goToAddress($this->directoryUrl.'/'.$link);
	}

	public function loginAction() { 
		$this->_layout->set("noAuthorized");
		$this->_linkScript($this->baseUrl.'/public/template/styles/_admin/form.css');
		if($this->_isPost()) {
			$authorize = new Authorize($this->_config->dbTableNames->Pracownik);
			if($authorize->logIn()) $this->_request->goToAddress($this->directoryUrl,0);
			else $this->msg(false,"Podane dane są nieprawidłowe.");
		}
	}
	public function logoutAction() {
		$authorize = new Authorize($this->_config->dbTableNames->Pracownik);
		$authorize->logOut();

		$this->_request->goToAddress($this->directoryUrl,0);
	}


	public function accessControlAction() {
		$this->view->text = 'Nie masz dostępu do tej części panelu.';
	}
}
?>