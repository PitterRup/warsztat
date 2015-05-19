<?php
class ConfigLibrary extends Library {
	private $_settings;
	private $file;
	private $json;
	public $errors = array();

	// przekierowanie nieznanych zmiennych do pobranych ustawień
	// lub do get() rodzica
	public function __get($name) { return $this->get($name); }
	private function get($name) {
		return isset($this->_settings->{$name}) ? $this->_settings->{$name} : parent::__get($name);
	}
	public function __set($name,$value) { 
		$this->_settings->{$name} = $value;
	}

	// pobranie konfiguracji z pliku
	public function load() {	
		$this->file = file_get_contents($this->_request->serverPath.'/application/config.json');
		$this->_settings = $this->_filter->json_clean_decode($this->file);
	}

	// połączenie z baza danych
	public function dbConnect() {
		// echo 'host: '.$this->host.'<br>';
		if(!mysql_connect($this->host,$this->userName,$this->password)) $this->errors[] = 'Błąd połączenia z bazą danych.<br>'.mysql_error();
		if(!mysql_select_db($this->datebaseName)) $this->errors[] = 'Błąd wybierania  bazy danych.<br>'.mysql_error();     
		mysql_query("SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	}

	public function getDirName($config) {
		$width = ($config[0]===null) ? 'oryg':$config[0];
		$height = ($config[1]===null) ? 'oryg':$config[1];
		return $width.'x'.$height;
	}


	// public function setConfig() {
	// 	$serverPath = $this->setServerPath();
	// 	$json = $this->getJson($this->_settings);die;
	// 	$plik = file_put_contents($serverPath.'/test.json', $json);
	// }


	// private function getJson($object,$show=true) {
	// 	$this->json .= '{<br>';
	// 	foreach($object as $key=>$value) {
	// 		if(!is_object($value) && !is_array($value)) $this->json .= $key.':'.$value.'<Br>';
	// 		elseif(is_object($value)) {
	// 			$this->json .= $key.': ';
	// 			$this->getJson($value,false);
	// 		}
	// 		// elseif(is_array($value)) $this->json .= $key.':'.json_encode($value).'<br>';
	// 	}
	// 	$this->json .= '}<br>';
	// 	if($show) echo $this->json;

	// 	// $json = json_encode($object);
	// 	// $old = array("{","}",",");
	// 	// $new = array("{\n\t","\n}",",\n\t");
	// 	// return str_replace($old,$new,$json);
	// }
}

?>