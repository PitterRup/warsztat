<?php
abstract class Login {
	private static $adminState = false;
	private static $userState = false;
	private static $data;

	public function setState() {
		// ustalenie statusu dla admina i usera
		if(isset($_SESSION[$this->_config->dbTableNames->Pracownik]) && $_SESSION[$this->_config->dbTableNames->Pracownik]['state']==1) self::$adminState = true;
		if(isset($_SESSION[$this->_config->dbTableNames->Klient]) && $_SESSION[$this->_config->dbTableNames->Klient]['state']==1) self::$userState = true;
	}
	public function getState($type) {
		if($type==$this->_config->dbTableNames->Pracownik) return self::$adminState;
		elseif($type==$this->_config->dbTableNames->Klient) return self::$userState;
	}

	public function checkLog() {
		// sprawdza czy user lub admin jest zalogowany
		if(!self::$userState && !self::$adminState) return false;

		// sprawdza dla kogo pozwolenie
		if(Access::getPermission()==1) {
			$authorize = new Authorize($this->_config->dbTableNames->Klient);
			self::$data = $authorize->getData($_SESSION[$this->_config->dbTableNames->Klient]['login']);
		}
		elseif(Access::getPermission()==2) {
			$authorize = new Authorize($this->_config->dbTableNames->Pracownik);
			self::$data = $authorize->getData($_SESSION[$this->_config->dbTableNames->Pracownik]['login']);
		}

		// porównanie ip i phpsessid
		if((self::$data['phpsessid']==$_COOKIE['PHPSESSID']) && (self::$data['ip']==$_SERVER['REMOTE_ADDR'])) return true;
		else return false;
	}

	public function hasPermission($type) {
		if($type=='dir') {
			if(self::$adminState && Access::getPermission()==2) return true;
			elseif(self::$userState && Access::getPermission()==1) return true;
			else return false;
		}
		elseif($type=='controller') {
			$permissions = json_decode(self::$data['permissions']);

			$a = $this->_request->directoryPath();
			$b = $this->_request->controllerName();
			return (array_key_exists($a, $permissions)  
				&& ((gettype($permissions->$a)=='integer' && $permissions->$a==1)
					|| (array_key_exists($b, $permissions->$a)
						&& gettype($permissions->$a->$b)=='integer'
						&& $permissions->$a->$b==1))) ? true:false;
		}
		elseif($type=='action') {			
			$permissions = json_decode(self::$data['permissions']);
			$a = $this->_request->directoryPath();
			$b = $this->_request->controllerName();
			$c = $this->_request->actionName();
			return (array_key_exists($a, $permissions)  
				&& ((gettype($permissions->$a)=='integer' && $permissions->$a==1)
					|| (array_key_exists($b, $permissions->$a)
						&& gettype($permissions->$a->$b)=='object'
						&& array_key_exists($c, $permissions->$a->$b)
						&& $permissions->$a->$b->$c==1))) ? true:false; 
		}
	}
}
?>