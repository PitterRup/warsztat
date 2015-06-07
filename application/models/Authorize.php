<?php
class Authorize extends GeneralModelsController {
    private $logState = false;

	public function init($name) {
        $this->_name = $name;
	}

    public function logIn() {
        if($this->checkData()) {
            $this->fetchRow();
            $id = $this->data['id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $phpsessid = $_COOKIE['PHPSESSID'];
            // zapisanie zmiennych synchronizacji
            if($this->setQuery("UPDATE $this->_name SET ip='$ip',phpsessid='$phpsessid' WHERE id='$id'")) {
                $permissions = json_decode($this->data['permissions']);
                $_SESSION[$this->_name] = array("state"=>true, "login"=>"$this->login", "permissions"=>$permissions);
                return true;
            }
            else return false;
        }
        else return false;
    }

    public function logOut() {
        $login = $_SESSION[$this->_name]['login'];
        $this->setQuery("UPDATE $this->_name SET phpsessid='',ip='' WHERE login= BINARY '$login'");    
        unset($_SESSION[$this->_name]);
    }
				 
    private function checkData() { 
        $this->login = $this->_getPost('login');
        $pass = $this->_getPost('pass');

        $this->setQuery("SELECT * FROM $this->_name WHERE login= BINARY '$this->login' AND pass= BINARY '$pass'");
        return ($this->numRows()==1) ? true:false;
    }

    public function getData($login) {
        $this->setQuery("SELECT a.ip, a.phpsessid, f.permissions FROM $this->_name a, $this->_FunPrac f WHERE a.Fun_Prac_ID=f.id AND login= BINARY '$login'");
        $this->fetchRow();
        return $this->data;
    } 
}
?>