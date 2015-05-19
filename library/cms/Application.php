<?php
class Application extends Library {
	private $_controller;
	private $checkingRoute = true;

	// Ładowanie plików klas
	public function setLoader() {
		// ładowanie plików klas obiektów biblioteki
		$libLoader = new Loader("Library",PATH."library/cms/");
		$libLoader->register();
		// ładowanie plików klas kontrolerów
		$contrLoader = new Loader("Controller",PATH."application/controllers/");
		$contrLoader->register();
		// ładowanie plików klas obiektów modeli
		$modelLoader = new Loader("",PATH."application/models/");
		$modelLoader->register();
	}

	public function sessionStart() {
		session_start(); 
	}
	public function errorReporting($op=0) {
		error_reporting($op);
	}
	public function loadLibrary() {
		$this->setLibrary('view',new ViewLibrary); // widok
		$this->setLibrary('_filter',new FilterLibrary);
		$this->setLibrary('_request',new RequestLibrary);
		$this->setLibrary('_config',new ConfigLibrary); // ustawienia
		$this->setLibrary('_layout',new LayoutLibrary);
		$this->setLibrary('_helper',new HelperLibrary); // pomocne funkcje by nie zaśmiecać Application
	}

	public function init() {
		// pobranie ustawień strony
		Debug::checkPoint("Load config");
		$this->_config->load();
		// połączenie z bazą danych
		$this->_config->dbConnect();

		// analiza adresu
		Debug::checkPoint("Reqeust init");
		$this->_request->init();  
		// opcja odblokowana dopiero gdy strona jest gotowa do wczytania 
		// po wszystkich przekierowaniach, ustawieniach adresu i języka
		// aby nie pokazywało treści gdy strona jest niegotowa 
		if($this->_request->getState()) $this->_layout->on();
		// sprawdzenie czy ma być wykonane zapytanie ajaxowe 
		// (wyłącza wczytywanie layout'a)
		if($this->_request->isAjaxRequest() || $this->_getParam("type")=='noContent') $this->_layout->off();
	}


	public function run() {
		// ustala status użytkownika
		Debug::checkPoint("Ustalenie statusu klienta");
		Login::setState();

		$i = 0; // bezpiecznik
		while($i<10) {
			// ograniczenie dostępu
			// sprawdzenie czy katalog chroniony
			Debug::checkPoint("Ograniczenie dostępu");
			if(Access::isProtectedDir()) {
				// czy ma dostęp do katalogu i sprawdza szczegółowo zalogowanie
				if(!Login::hasPermission('dir') || !Login::checkLog()) $this->_request->redirect("index","login");
				// czy ma dostęp do kontrolera i czy ma dostęp do akcji
				elseif(!Login::hasPermission('controller') && !Login::hasPermission('action')) $this->_request->redirect("index","noAccess");
			}
			// sprawdza czy kontroler chroniony
			elseif(Access::isProtectedController()) {
				// sprawdza szczegółowo zalogowanie
				if(!Login::checkLog()) $this->_request->redirect("index","login");
				// czy ma dostęp do kontrolera i czy ma dostęp do akcji
				elseif(!Login::hasPermission('controller') && !Login::hasPermission('action')) $this->_request->redirect("index","noAccess");
			}
			// sprawdza czy akcja chroniona
			elseif(Access::isProtectedAction()) {
				// sprawdza szczegółowo zalogowanie
				if(!Login::checkLog()) $this->_request->redirect("index","login");
				// czy ma dostęp do akcji
				elseif(!Login::hasPermission('action')) $this->_request->redirect("index","noAccess");
			}
			
			// sprawdzenie czy istnieje kontroler
			Debug::checkPoint("Sprawdzenie czy istnieje kontroler");
			if(!file_exists(PATH.'application/'.$this->_request->directoryPath().'/controllers/'.$this->_helper->createControllerName($this->_request->controllerName()).'.php')) {		
				if(in_array($this->_request->directoryPath(),$this->_config->routeDirs)) {
					// sprawdzenie route'a
					Debug::checkPoint("Sprawdzenie route'a");
					if(!Route::issetLink() && !Route::issetPattern()) Errors::page("404");
					else continue;
				}
				else Errors::page("404");
			}
		
			// utworzenie kontrolera
			Debug::checkPoint("Utworzenie kontrolera");
			$this->_controller = $this->_helper->getController();
			
			// sprawdzenie czy istnieje akcja
			Debug::checkPoint("Sprawdzenie czy istnieje akcja");
			$_actionName = $this->_request->actionName().'Action';
			if(!method_exists($this->_controller,$_actionName)) {
				if($this->checkingRoute && in_array($this->_request->directoryPath(),$this->_config->routeDirs)) {
					// sprawdzenie route'a
					Debug::checkPoint("Sprawdzenie route'a");
					if(!Route::issetLink() && !Route::issetPattern('controller')) $this->checkingRoute = false;
				}
				else {
					// sprawdzenie domyślnej akcji
					Debug::checkPoint("Sprawdzenie domyślnej akcji");
					$_actionName = $this->_controller->defaultAction.'Action';
					if(!method_exists($this->_controller,$_actionName)) Errors::page("404");
					else $this->_request->redirect(null,$this->_controller->defaultAction);
				}
			}
			else break;

			// zwiększenie licznika bezpiecznika
			$i++;
		}
		// sprawdzenie błędu znalezienia kontrolera
		if($i==10) Errors::page("404");


		// przechwycenie wywołanej akcji
		Debug::checkPoint("Przechwycenie wywołanej akcji");
		ob_start();
			$this->_controller->$_actionName();
			// przypisuje treść z akcji do widoku
			$this->view->content = ob_get_contents();
		ob_end_clean();

		// pobranie layout'u
		Debug::checkPoint("<b>Pobranie Layout'u</b>");
		$this->view->getLayout('layout');

		// wyświetlenie błędów
		Debug::checkPoint("Wyświetlenie błędów");
		if($this->_config->errorReporting && $this->_request->getState()) Errors::get();   
		
		// wyświetlenie widoku
		Debug::checkPoint("<b>Wyświetlenie widoku</b>");
		$this->view->show();
	}
}
?>