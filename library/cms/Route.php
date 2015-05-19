<?php
abstract class Route {
	private static $active = false;

	public function issetLink() {
		$routes = new Routes;
		return $routes->issetLink() ? true:false;
	}

	public function issetPattern($searchBy=null) {
		$routes = new Routes;
		$controller = $this->_request->controllerName();
		if($searchBy=='controller') $where = "AND controller='$controller'";
		if($array = $routes->getAll($where)) {
			return self::matchPattern($array,$searchBy) ? true:false;
		}
		else return false;
	}

	public function matchPattern($array,$searchBy) {
		// aktualny adres strony
		$link = explode("/",$this->_request->paramsLinkWL);
		// id znalezionego wzoru
		$l = 0;

		// sprawdzenie wszystkich route'ów
		foreach($array as $row) {
			// link do dopasowania z bazy
			$rLink = explode("/",$row['link']);

			// porównanie części linku z linkiem z bazy
			$count = count($rLink);
			for($i=0; $i<$count; $i++) {
				// dopasowywanie route'a 
				if(preg_match('/^{.+}$/D',$rLink[$i],$arr)) {
					$pattern = str_replace(array("{","}"),array("",""),$rLink[$i]);
					if(!preg_match('/^'.$pattern.'$/D',$link[$i])) break;
				}
				elseif($rLink[$i]!=$link[$i]) break;

				// oznaczenie, że dopasowano route'a
				if(!isset($rLink[$i+1]) && !isset($link[$i+1])) self::$active = true;
			}

			// zakończenie jeśli dopasowano
			if(self::$active) break;
			$l++;
		} 

		// wczytanie znalezionego wzoru
		if(self::$active) { 
			$get = array();
			$p = 0;
			// przepisanie parametrów z linku
			$count = count($rLink);
			$sI = $searchBy=='controller' ? 1:0;
			for($i=$sI; $i<$count-1; $i++) {
				$x1 = $rLink[$i];
				$x2 = $rLink[$i+1];

				// wpisuje parametry podane jako np. "/image/img12"
				if(!preg_match('/^{.+}$/D',$x1) && preg_match('/^{.+}$/D',$x2)) {
					$get[$link[$i]] = $link[$i+1];
					$i++;
				}
				// wpisuje parametry podane jako np. "/parametr" i nadaje im wartości kolejne od zera
				else {
					$get[$p] = $link[$i];
					$p++;
				}

				// usunięcie wszystkich złych parametrów z _request->get i zastąpienie dobrymi
				$this->_request->removeParam($link[$i]);
			}

			// dołączenie tablicy get do _request->get
			$this->_request->extendParams($get);
			// ustawienie akcji i kontrolera z route
			$this->_request->controllerName($array[$l]['controller']);
			$this->_request->actionName($array[$l]['action']);
			$this->_request->directoryPath($array[$l]['dir']);

			return true;
		}
		else return false;
	}

	public function isActive() {
		return self::$active;
	}
}
?>