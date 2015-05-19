<?php
class Routes extends GeneralModelsController {
	public function init(){
	}

	public function issetLink() {
		$this->lang = $this->_getParam('lang');
		$this->link = $this->_request->paramsLinkWL; //without lang
		if($row = $this->getLink()) {
			$this->_request->setParam($row['action'],$this->_request->controllerName());
			$this->_request->controllerName($row['controller']);
			$this->_request->actionName($row['action']);
			$this->_request->directoryPath($row['dir']);
			return true;
		}
		else return false;
	}

	private function getLink() {
		$this->setQuery("SELECT * FROM $this->_name WHERE type='link' AND lang='$this->lang' AND link='$this->link'");
		$this->fetchRow();
		return ($this->numRows()>0) ? $this->data:false;
	}
	public function getAll($userWhere) {
		$this->lang = $this->_getParam('lang');
		$this->setQuery("SELECT controller,action,link FROM $this->_name WHERE type='pattern' AND lang='$this->lang' $userWhere");
		$this->fetchAll();
		return ($this->numRows()>0) ? $this->data:false;
	}

	public function issetRouteName($name) {	
		$this->setQuery("SELECT * FROM $this->_name WHERE link='$name'");
		if($this->numRows()>0) return true;
		else return false;
	}


	public function add() {
		$lang = $this->addData['lang'];
		$type = $this->addData['type'];
		$link = $this->addData['link'];
		$dir = isset($this->addData['dir']) ? $this->addData['dir']:'_page';
		$controller = $this->addData['controller'];
		$action = $this->addData['action'];

		if($this->setQuery("INSERT INTO $this->_name VALUES('','$lang','$type','$link','$dir','$controller','$action')")) {
            $this->setMsg('Obiekt został dodany do route');
            return true;
        }
        else {
            $this->setError('Obiekt nie został dodany do route');
            return false;
        }
	}

	public function edit() {
        if($this->setQuery("UPDATE $this->_name SET link='$this->newLink' WHERE link='$this->link' AND lang='$this->lang'")) return true;
        else return false; 
    }
}
?>