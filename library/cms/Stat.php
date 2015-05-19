<?php
class Stat 
{
	public $day;
	public $mouth;
	public $year;
	public $uniqueVisits;
	public $wywolania;
	private $baseUrl;
	private $serverPath;

	public function __construct()
	{
		$args = func_get_args();
		$this->baseUrl = $args[0];
		$this->serverPath = $args[1];
		
		$this->startObserv('');
	}

	public function startObserv($prefiks)      //prefiks niedopracowany
	{
		$date = explode("-",date("j-n-Y"));		
		$this->day = $date[0];
		$this->mouth = $date[1];
		$this->year = $date[2];	
		$filename = $prefiks.''.$this->mouth.'.'.$this->year.'.txt';
		$cookie = explode("_",$_COOKIE['guest']);
		
		$this->readFile($filename);
		//zliczanie wywołań strony
		$this->zLW();
		
		//zliczanie unikalnych wizyt
		if($cookie[0]!='yes') $this->zLUW();
		else if($this->day!=$cookie[1]) $this->zLUW();
	     
		//zapisanie zmian
		$this->saveFile($filename);
	}

	public function zLUW() //zwiększLicznikUnikalnychWizyt
	{
		setcookie("guest","yes_$this->day",time()+43200);			
				 
		$licznik = $this->uniqueVisits;
		$licznik++;
		$this->uniqueVisits = $licznik;
	}
	public function zLW() //zwiększLicznikWywołań
	{
		$licznik = $this->wywolania;
		$licznik = $licznik + 1;   //przy zapisie inkrementuje tą wartość o kolejne 0.5 lub jak ustawię 1 to kolejne jeden.
		$this->wywolania = $licznik;
	}

	public function readStat($month,$year) {return file_get_contents($this->baseUrl.'/application/stat/'.$month.$year.'.txt');}
	private function createNewFile($filename) {file_put_contents($this->serverPath.'/application/stat/'.$filename,"0-0");}
	private function setUniqueVisits($uniqueVisits) {$this->uniqueVisits = $uniqueVisits;}
	private function setWywolania($wywolania) {$this->wywolania = $wywolania;}
	private function readFile($filename)
	{
		if(!file_exists($this->baseUrl.'/application/stat/'.$filename)) $this->createNewFile($filename);
		$file = file_get_contents($this->baseUrl.'/application/stat/'.$filename);
		$content = explode("-",$file);
		settype($content[0],'integer');
		settype($content[1],'integer');
		$this->uniqueVisits = $content[0];
		$this->wywolania = $content[1];   
	}
	private function saveFile($filename)
	{       		   
		$content = $this->uniqueVisits.'-'.$this->wywolania;
		file_put_contents($this->serverPath.'/application/stat/'.$filename,$content,LOCK_EX);
	}
}
?>