<?php
class RequestLibrary extends Library {
	public $baseUrl; 
	public $directoryUrl;
	public $serverPath;
	public $currentUrl;

	private $directoryPath;
	private $controllerParamId;
	private $get; 
	private $parameters;

	public $params;
	public $paramsLink;
	public $paramsLinkWL;
	public $scriptName;

	// powiadamia, że zostały już zrobione wszystkie przekierowania i można włączyć layout
	private $ready = false;

	private $headScripts = array();
	private $linkScripts = array();


	public function __construct() {
		$this->setBaseUrl();	
		$this->setServerPath();
	}

	public function init() {
		$this->readParameters(); 
		$this->setControllerName();
        $this->setActionName();
        $this->setDirectoryUrl();
        $this->setCurrentUrl();
	}


	// pobranie danych z adresu
	protected function readParameters() {
		// pobranie i przygotowanie odpowiedniej części adresu do podzielenia
		$requestUrl = $this->_filter->cleanREQUEST($_SERVER['REQUEST_URI']);
		$requestUrl = str_replace(array(".php","//"),array("","/"),$requestUrl);
		$requestUrl = substr(str_replace($this->path,"",$requestUrl),1);

		// ustalenie paramsLink
		$this->paramsLink = $requestUrl;
		$this->paramsLinkWL = ($this->_config->lang->on) ? substr($requestUrl,3) : $this->paramsLink;

		// podzielenie adresu na tablice
		$this->parameters = explode("/",$requestUrl);

		// indeks położenia kontrolera w adresie 
		// (w zależności od włączenia wersji językowej)
		$this->controllerParamId = $this->_config->lang->on ? 1:0;

		// zapisanie parametrów do tablicy _GET
		$this->setGetArray();

		// gdy nie podano języka przeładowuje stronę
		if($this->_config->lang->on && !$this->langExists()) {
			if($this->_config->lang->autosetting) $this->goToAddress($this->baseUrl.'/'.$this->_config->lang->default.'/'.$this->paramsLink,0);
			else $this->goToAddress($this->baseUrl.'/set/index/chooseLanguage',0); // strona wyboru języka
		}
		else $this->ready = true;

		// ustala directoryPath przez directoryName
		$this->setDirectoryPath($this->get['controllerName']);

		// sprawdzenie czy katalog został podany w adresie
		// (jeśli tak to odczytuje parametry jeszcze raz z przesunięciem dla kontrolera)
		if($this->directoryPath()!=$this->_config->paths->default) {
			$this->controllerParamId++;
			$this->setGetArray();
		}

		// gdy wersje językowe są wyłączone ma przyjąć za lang domyslny parametr
		if(!$this->_config->lang->on) $this->get['lang'] = $this->_config->lang->default;
		
		// stworzenie linku parametrów z tablicy parametrów
		$this->params = join("/",$this->params);

		// zwolnienie roboczej tablicy
		unset($this->parameters);
	}

	private function setGetArray() {
		// tablica GET
		$this->get = array();
		// tablica na link parametrów
		$this->params = array();

		// gdy włączona wersja językowa zaczyna szukanie katalogu, kontrollera, akcji i parametrów 
		// od indeksu 1
		$startI = $this->_config->lang->on ? 1:0;

		// przeanalizowanie łańcucha
		for($i=0; $i<count($this->parameters); $i++) {
			// wyciągniecie języka
			if($this->_config->lang->on && $i==0) $this->get['lang'] = $this->parameters[$i]; 
			
			// wyciągnięcie katalogu
			if($i==$startI && $i!=$this->controllerParamId) $this->get['directoryName'] = $this->parameters[$i];
			
			// wyciągnięcie kontrollera
			elseif($i==$this->controllerParamId) $this->get['controllerName'] = $this->parameters[$this->controllerParamId];
			
			// wyciągnięcie akcji
			elseif($i==$this->controllerParamId+1) {
				$this->get['actionName'] = $this->parameters[$this->controllerParamId+1];
				$j = $this->controllerParamId+2;
			}
			
			// wybranie parametrów 
			elseif($i > $this->controllerParamId+1) {
				$this->get[$this->parameters[$j]] = $this->parameters[$j+1];
				if(strlen($this->parameters[$j])>0) $this->params[] = $this->parameters[$j];
				if(strlen($this->parameters[$j+1])>0) $this->params[] = $this->parameters[$j+1];
				$j += 2;
				// zakończenie gdy nie ma kolejnego parametru
				if(!isset($this->parameters[$j])) break;
			}
		}
	}

	// baseUrl
	private function setBaseUrl() {
		$scriptName = explode("/",$_SERVER['SCRIPT_NAME']);
		unset($scriptName[count($scriptName)-1]);
		unset($scriptName[count($scriptName)-1]);
		$this->path = join("/",$scriptName);
		$this->baseUrl = 'http://'.$_SERVER['SERVER_NAME'].$this->path;
	}
	// serverPath
	private function setServerPath() {
		$this->serverPath = $_SERVER['DOCUMENT_ROOT'].$this->path;
	}	
	// directoryUrl
	private function setDirectoryUrl() {
		$lang = strlen($this->get['lang'])>0 ? '/'.$this->get['lang'] : '';
		$directoryName = strlen($this->get["directoryName"])>0 ? '/'.$this->get["directoryName"] : '';
		$this->directoryUrl = $this->_config->lang->on ? $this->baseUrl.$lang.$directoryName : $this->baseUrl.$directoryName;
	}
	// pełny aktualny adres
	private function setCurrentUrl() { 
		$lang = $this->_config->lang->on ? $this->get['lang'].'/':'';
		$directoryName = strlen($this->get["directoryName"])>0 ? $this->get["directoryName"].'/':'';
		$this->currentUrl = $this->baseUrl.'/'.$lang.$directoryName.$this->controllerName().'/'.$this->actionName().'/'.$this->params;
	}
	

	// getter i setter kontrolera
	private function setControllerName() {
		if(strlen($this->get["controllerName"])==0) $this->setParam('controllerName','index');
	}
	public function controllerName($controllerName=null) {
		if(!$controllerName) return $this->get["controllerName"];
		$this->setParam("controllerName",$controllerName);
	}

	// getter i setter akcji
	private function setActionName() {
		if(strlen($this->get["actionName"])==0) $this->setParam('actionName','index');
	}
	public function actionName($actionName=null) {
		if(!$actionName) return $this->get["actionName"];
		$this->setParam("actionName",$actionName);
	}

	// getter i setter directoryPath
	private function setDirectoryPath($directoryName) {
		// sprawdzenie czy podano katalog (jeśli nie wybierany jest domyślny)
		if(strlen($directoryName)>0 && array_key_exists($directoryName, $this->_config->paths)) $this->directoryPath = $this->_config->paths->$directoryName;
		else $this->directoryPath = $this->_config->paths->default;
	}
	public function directoryPath($directoryPath=null) { 
		if(!$directoryPath) return $this->directoryPath; 
		if($directoryName = array_search($directoryPath,(array) $this->_config->paths)) {
			$this->directoryPath = $directoryPath;
			$this->setParam("directoryName",$directoryName);
		}
	}


	// przekierowanie 
	public function goToAddress($address,$time) {
		header("refresh: $time; url=$address");
    }
    public function redirect($controllerName=null,$actionName=null,$directoryPath=null) {
    	$directoryPath = $directoryPath ? $directoryPath : "";
    	$controllerName = $controllerName ? $controllerName : "index";
    	$actionName = $actionName ? $actionName : "index";

    	$this->controllerName($controllerName);
    	$this->actionName($actionName);
    	$this->directoryPath($directoryPath);
    }

    // getter i setter GET
    public function getParam($name) { 
    	if(strlen($name)==0) return $this->get;
    	return isset($this->get[$name]) ? $this->get[$name]:false; 
    }
    public function setParam($name,$value) { $this->get[$name] = $value; }
    // usunięcie parametru
    public function removeParam($name) { unset($this->get[$name]); }
    // dołączenie tablicy parametrów
    public function extendParams($array) { $this->get = array_merge($this->get,$array); }
    // pobranie wszystkich parametrów
    public function getParams() { return $this->get; }

    // getter POST
    public function getPost($name,$clean=true) { 
    	if(strlen($name)==0) $post = $_POST;
    	else $post = $_POST[$name];
    	return isset($post) ? ($clean ? $this->_filter->cleanArray($post) : (!is_array($post) ? stripslashes($post):$post)):false; 
    }
    public function isPost() { return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' ? true:false; }


    // getter i setter plików nagłówkowych 
    // script i link
    public function headScript($script) {
        $this->headScripts[] = htmlspecialchars('<script type="text/javascript" src="'.$script.'"></script>');
    }
    public function getHeadScript() {
    	$html = '';
        foreach($this->headScripts as $script) $html .= htmlspecialchars_decode($script);
        return $html;
    }
    public function linkScript($script) {
        $this->linkScripts[] = htmlspecialchars('<link rel="stylesheet" type="text/css" href="'.$script.'">');    
    }
    public function getLinkScript() {
    	$html = '';
        foreach($this->linkScripts as $script) $html .= htmlspecialchars_decode($script);
        return $html;    
    }


    // czytanie parametrów z metody GET 
    // private function readParamsFromGetMethod($params) {
    // 	$params = explode("&",$params[1]);

    // 	foreach($params as $param)
    // 	{
    // 		$param = str_replace("amp;","",$param);
    // 		$KaV = explode("=",$param);
    // 		$this->get[$KaV[0]] = $KaV[1];
    // 	}
    // }

    // sprawdzenie czy język z adresu istnieje w bazie
    private function langExists() {
    	return in_array($this->get['lang'],$this->_config->lang->langs) ? true:false;
    }
    
    // zwracanie stanu 
    public function getState() {
    	return $this->ready;
    }

    // metoda sprawdza czy próbujemy otworzyć controller ajaxowych zapytań
    public function isAjaxRequest() {
        return $this->controllerName()=='ajax' ? true:false;
    }
}
?>