<?php
class Stoper {
	private $defaultName;
	private $i;
	private $name;
	private $results;

	public function __construct() {
		$this->i = 0;
		$this->defaultName = 'czas ';
		$this->results = array();
	}

	public function start($name=null) {
		// nazwa pomiaru
		$this->name = $name ? $name:$this->setName();
		// start pomiaru
		$this->results[$this->name] = array("start"=>$this->getMicrotime());
	}
	public function stop($name=null) {
		// nazwa pomiaru
		$this->name = $name ? $name:$this->getName();
		// zatrzymanie pomiaru
		$this->results[$this->name]['stop'] = $this->getMicrotime();
		$this->results[$this->name]['result'] = $this->results[$this->name]['stop'] - $this->results[$this->name]['start'];
		$out = '<p>'.$this->name.': <b>'.number_format($this->results[$this->name]['result'],22).'</b></p>';
		$this->name = null;
		return $out;
	}
	public function getTime($name=null) {
		$this->name = $name ? $name:$this->getName();
		return number_format($this->results[$name]['result'],22);
	}

	private function setName() {
		$this->i++;
		return $this->defaultName.$this->i;
	}
	private function getName() {
		return $this->name ? $this->name:$this->defaultName.$this->i;
	}
	private function getMicrotime() {
		list($usec, $sec) = explode(' ', microtime()); 
    	return ((float)$usec + (float)$sec); 
	}
}
?>