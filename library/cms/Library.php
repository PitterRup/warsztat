<?php
abstract class Library {
	static private $_library=array();

	// przekierowania do obiektów
	public function __get($name) {
		return $this->get($name);
	}
	private function get($name) { 
		try {
			if($name=='baseUrl') return $this->_request->baseUrl;
			elseif($name=='directoryUrl') return $this->_request->directoryUrl;
			elseif($name=='currentUrl') return $this->_request->currentUrl;
			elseif($name=='serverPath') return $this->_request->serverPath;
			elseif($name=='_layoutPath') return $this->_config->layoutPath;
			elseif(isset(self::$_library[$name])) return self::$_library[$name];
			else throw new Exception("deep[1]:Nie odnaleziono właściwości <b>$name</b>");
		} catch(Exception $e) { Errors::add($e); }
	}
	public function __call($name,$args) {
		try {
			if($name=='_getParam') return $this->_request->getParam($args[0]);
			elseif($name=='_setParam') return $this->_request->setParam($args[0],$args[1]);
			elseif($name=='_getPost') return $this->_request->getPost($args[0],$args[1]);
			elseif($name=='_isPost') return $this->_request->isPost();
			elseif($name=='_headScript') return $this->_request->headScript($args[0]);
			elseif($name=='_getHeadScript') return $this->_request->getHeadScript();
			elseif($name=='_linkScript') return $this->_request->linkScript($args[0]);
			elseif($name=='_getLinkScript') return $this->_request->getLinkScript();
			else throw new Exception("Metoda <b>$name()</b> nie została odnaleziona");
		} catch(Exception $e) { Errors::add($e); }
	}
	protected function setLibrary($name,$obj) {
		self::$_library[$name] = $obj;
	}
}
?>