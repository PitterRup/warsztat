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
            $data = $this->data;
            // zapisanie zmiennych synchronizacji
            if($this->setQuery("UPDATE $this->_name SET ip='$ip',phpsessid='$phpsessid' WHERE id='$id'")) {
                $permissions = json_decode($data['permissions']);
                $_SESSION[$this->_name] = array("state"=>true, "login"=>"$this->login", "id"=>$data['id'], "permissions"=>$permissions, "funcId"=>$data['funcId']);
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

        if($this->_name=='pracownik') $query = "SELECT a.*, f.rola, f.permissions, f.id as 'funcId' FROM $this->_name a, $this->_FunPrac f WHERE a.Fun_Prac_ID=f.id AND a.login= BINARY '$this->login' AND a.pass= BINARY '$pass'";
        else $query = "SELECT a.* FROM $this->_name a WHERE a.login= BINARY '$this->login' AND a.pass= BINARY '$pass'";;
        $this->setQuery($query);
        return ($this->numRows()==1) ? true:false;
    }

    public function getData($login) {
        if($this->_name=='pracownik') $query = "SELECT a.ip, a.phpsessid, f.permissions, f.rola FROM $this->_name a, $this->_FunPrac f WHERE a.Fun_Prac_ID=f.id AND login= BINARY '$login'";
        else $query = "SELECT a.ip, a.phpsessid, a.permissions FROM $this->_name a WHERE login= BINARY '$login'";
        $this->setQuery($query);
        $this->fetchRow();
        return $this->data;
    } 
}
?>