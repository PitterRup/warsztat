<?php
class LayoutLibrary extends Library {
	private $name;
	private $on = false;
	private $change = false;
	public $path;

	public function off() { $this->on = false; }
	public function on() { $this->on = true; }
	public function state() { return $this->on; }
	public function isChanged() { return $this->change; }
	public function set($name) {
        //zablokowanie wczytania domyślnego layoutu
        $this->change = true;
        $this->name = $name;
    }
    public function getName() { return $this->name; }
}
?>