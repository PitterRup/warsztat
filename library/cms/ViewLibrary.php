<?php
class ViewLibrary extends Library {
    private $_on; 
    private $layout;
    public $content;
    public $logs;
    
    // tablica do której wrzucamy stałe obiekty kopiowane przez ajaxa
    private $constObjs = array();

    // pobranie Layout'u
    public function getLayout($name) {
        //sprawdzenie czy zostało włączone ładowanie innego layoutu
        if(!$this->_layout->isChanged()) $this->_layout->set($name);

        //sprawdzenie czy ładowanie szablonu jest włączone
        if($this->_layout->state()) {
            ob_start();
                //pobranie pliku layoutu
                if(file_exists(PATH.'application/'.$this->_request->directoryPath().'/view/'.$this->_layout->getName().'.phtml')) {
                    require_once PATH.'application/'.$this->_request->directoryPath().'/view/'.$this->_layout->getName().'.phtml';
                }
                // else throw new Exception("Plik (<i>PATH.'application/'.$this->_request->directoryPath().'/view/'.$this->_layout->getName().'.phtml'</i>) layout'u nie istnieje");
                $this->layout = htmlspecialchars(ob_get_contents());
            ob_end_clean();
        }
    }

    // metoda wyświetlająca widok
    public function show() {
        if($this->_layout->state()) {
            if( ( (!Errors::$showLayout && Errors::count()==0) || Errors::$showLayout) && !Debug::$on) echo htmlspecialchars_decode($this->layout);
        }
        else echo $this->getContent();
    }

    // pobranie widoku i treści z kontrolera
    public function getContent() { 
        ob_start();
            //wyświetlenie pobranej treści z kontrolera
            echo $this->content;

            //pobranie widoku dla akcji kontrolera
            if(file_exists(PATH.'application/'.$this->_request->directoryPath().'/view/'.$this->_request->controllerName().'/'.$this->_request->actionName().'.phtml')) { 
                if($this->_getParam("type")!='noContent') require_once PATH.'application/'.$this->_request->directoryPath().'/view/'.$this->_request->controllerName().'/'.$this->_request->actionName().'.phtml';
            }
            // else if($this->_layout->state()) throw new Exception("Nie znaleziono widoku do kontrolera: <b>$this->_request->controllerName()</b> i akcji: <b>$this->_request->actionName()</b>");

            $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    // pobranie części widoku
    public function renderContent($fileName) {
        ob_start();
            if(file_exists(PATH.'application/'.$this->_request->directoryPath().'/view/'.$fileName)) require PATH.'application/'.$this->_request->directoryPath().'/view/'.$fileName;
            // else throw new Exception("Plik widoku <b>PATH.'application/'.$this->_request->directoryPath().'/view/'.$fileName</b> nie istnieje");
            $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    

    // dodanie obiektu html który będzie wstawiony na dole strony 
    // (do kopiowania przez js)
    public function addConstObj($obj) {
        if(!in_array($obj,$this->constObjs)) $this->constObjs[] = $obj;
    }

    // dodanie logów z modeli
    public function setLog($log,$color='white') {
        $this->logs[] = htmlspecialchars('<span style="color:'.$color.'">'.$log.'</span>');
    }
}
?>