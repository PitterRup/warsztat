<?php
class HelperLibrary extends Library {

    // metoda tworzy nazwę kontrolera
	public function createControllerName($controllerName) {
        $firstPart = $this->_filter->firstCharToUpper($controllerName);
        $secondPart = 'Controller';
        return $firstPart.$secondPart;
    }

    // metoda zwraca obiekt kontrolera
    public function getController() {
        // nazwa kontrolera
        Debug::checkPoint("Utworzenie nazwy kontrolera",1);
        $cn = $this->createControllerName($this->_request->controllerName());

        // nazwa kontrolera głównego kontrolera :)
        Debug::checkPoint("Wyznaczenie nazwy kontrolera głównego",1);
        if($this->_request->directoryPath()=='_page') $gc = 'PageController';
        elseif($this->_request->directoryPath()=='_admin') $gc = 'AdminController'; 
        elseif($this->_request->directoryPath()=='_user') $gc = 'UserController';
        
        Debug::checkPoint("Pobranie pliku kontrolera głównego",1);
        require_once PATH.'library/cms/'.$gc.'.php';
        Debug::checkPoint("Pobranie pliku kontrolera",1);
        require_once PATH.'application/'.$this->_request->directoryPath().'/controllers/'.$cn.'.php';
        Debug::checkPoint("Zwrócenie obiektu kontrolera",1);
        return new $cn;
    }
}
?>