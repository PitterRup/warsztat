<?php
class Upload {
	public $uploaded;
	public $processed;
	public $error;

	public function __construct($file,$buforPath) {
		$this->file = $file;

		//ustawienia
		$this->file_tmp_name = $this->file['tmp_name'];
		$this->file_name = $this->file['name'];
		$this->file_bufor_path = $buforPath;

		$this->file_auto_rename = false; //$this->file_new_name = null !koniecznie
        $this->file_safe_name = false;
        $this->file_new_name = null;
        $this->file_old_name = $this->file_name; 
        $this->file_dst_name = null;
        $this->file_dst_path = null;

        $this->image_resize = false;
        $this->image_y = 0;
        $this->image_x = 0;
        $this->image_ratio = false;
        $this->image_ratio_x = false;
        $this->image_ratio_y = false;
        $this->image_quality = 100;
	}

	public function __destruct() {
		//usuniecie pliku z bufora
		if(file_exists($this->serverPath.'/'.$this->file_bufor_path.$this->file_name) && filetype($this->serverPath.'/'.$this->file_bufor_path.$this->file_name)!='dir') unlink($this->serverPath.'/'.$this->file_bufor_path.$this->file_name);
	}

	public function upload() {
		$this->uploaded = false;

		if(is_uploaded_file($this->file_tmp_name)) {
			if(move_uploaded_file($this->file_tmp_name, $this->serverPath.'/'.$this->file_bufor_path.$this->file_name)) $this->uploaded = true;
			else $this->error = 'Plik nie został przeniesiony.';
		}
		else $this->error = 'Plik nie został przesłany.';		
	}

	public function process($dir) {
		$this->processed = false;
		$this->file_dst_path = $dir;

		//nadanie nowej nazwy plikowi (ustalam $this->file_new_name)
		if($this->file_new_name==null && $this->file_auto_rename) $this->newFileName(); 

		//ustalenie ostatecznej nazwy pliku
		if($this->file_new_name!=null) $this->file_dst_name = $this->file_new_name;
		else $this->file_dst_name = $this->file_name;

		//sprawdzenie czy istnieje plik o takiej nazwie w tym albumie 
		if($this->file_safe_name) $this->file_dst_name = $this->checkFileName($this->file_dst_path,$this->file_dst_name); 

		//ustalenie nowego adresu dla pliku
		$this->newAddress = $this->serverPath.'/'.$this->file_dst_path.$this->file_dst_name;

		//przekopiowanie pliku do docelowego adresu i ewentualna zmiana rozmiaru
		if($this->resize()) $this->processed = true;	
		else $this->error = 'Wystąpił problem przy zmianie rozmiarów obrazka';

		$this->clean();
	}

	public function resize() {
		// pobieramy rozszerzenie pliku, na tej podstawie
        // korzystamy potem z odpowiednich funkcji GD
        $i = explode('.', $this->file_name);
        
        // rozszerzeniem pliku jest ostatni element tablicy $i
        $rozszerzenie = end($i);
        $rozszerzenie = strtolower($rozszerzenie);
        
        // pobieramy rozmiary obrazka
        list($this->image_real_x, $this->image_real_y) = getimagesize($this->serverPath.'/'.$this->file_bufor_path.$this->file_name);

        //wyliczanie nowych rozmiarów obrazka w zależności od wybranych opcji
        if($this->image_resize) {
	        if($this->image_ratio_x) { if($this->image_real_y > $this->image_y) $this->calculateImageSize('ratio_x');}
	        elseif($this->image_ratio_y) { if($this->image_real_x > $this->image_x) $this->calculateImageSize('ratio_y'); }
	        elseif($this->image_ratio) { 
	        	//sprawdza czy zdjęcie jest poziome (true: ustawia wysokość i dopasowuje szerokość; false: odwrotnie)
	        	if($this->image_real_x >= $this->image_real_y) $this->calculateImageSize('ratio_x');
	        	else $this->calculateImageSize('ratio_y');	
	        }
	        else {
	        	$this->image_new_x = $this->image_x;
        		$this->image_new_y = $this->image_y;
	        }
	    }
        else {
        	$this->image_new_x = $this->image_real_x;
        	$this->image_new_y = $this->image_real_y;
        }  

        if(!$this->image_new_x && !$this->image_new_y) //sprawdzić
        {
        	$this->image_new_y = $this->image_real_y;
        	$this->image_new_x = $this->image_real_x;
        }

        // tworzymy nowy obrazek o zadanym rozmiarze
        // korzystamy tu z funkcji biblioteki GD
        // która musi być dołączona do twojej instalacji PHP,
        // najpierw tworzymy canvas.
        ini_set('memory_limit','1000M');
        ini_set("gd.jpeg_ignore_warning", 1);
        set_time_limit(600); //przy dużych plikach duży czas oczekiwania (trzeba zmienić też max_execution_time w php.ini)

        $canvas = imagecreatetruecolor($this->image_new_x, $this->image_new_y);
        switch($rozszerzenie) {
            case 'jpeg':
            $org = imagecreatefromjpeg($this->serverPath.'/'.$this->file_bufor_path.$this->file_name);
            break;
            case 'jpg':
            $org = imagecreatefromjpeg($this->serverPath.'/'.$this->file_bufor_path.$this->file_name);
            break;
            case 'gif':
            $org = imagecreatefromgif($this->serverPath.'/'.$this->file_bufor_path.$this->file_name);
            break;
            case 'png':
            $org = imagecreatefrompng($this->serverPath.'/'.$this->file_bufor_path.$this->file_name);
            break;
        }

        // kopiujemy obraz na nowy canvas
        imagecopyresampled($canvas, $org, 0, 0, 0, 0,$this->image_new_x, $this->image_new_y, $this->image_real_x, $this->image_real_y);

        // zapisujemy jako jpeg 
        if(imagejpeg($canvas, $this->newAddress, $this->image_quality)) return true;
        else return false;
	}
	public function checkFileName($dir,$name) {
		$new_name = $name;
		for($i=2;$i<=100;$i++) {
			if(file_exists($this->serverPath.'/'.$dir.$new_name)) {
				$k = explode(".", $name);
				$new_name = $k[count($k)-2].'_'.$i.'.'.$k[count($k)-1];
			}
			else break;
		}
		return $new_name;
	}

	private function newFileName() {
		$odczyt = pathinfo($this->file_name);
		$rozszerzenie = $odczyt['extension'];
		$nr = rand(10000,99999);
		$new_name = 'image-'.$nr;
		$this->file_new_name = $this->checkFileName($this->file_dst_path,$new_name);
	}

	private function calculateImageSize($op) {
		if($op=='ratio_x') {
			$a = $this->image_real_x / $this->image_real_y;		   
        	if($this->image_y==0) $this->image_y = $this->image_real_y;	        	
        	$this->image_new_x = $this->image_y * $a;
        	$this->image_new_y = $this->image_y;
		}
		elseif($op=='ratio_y') {
			$a = $this->image_real_y / $this->image_real_x;
        	if($this->image_x==0) $this->image_x = $this->image_real_x;        	
        	$this->image_new_y = $this->image_x * $a;
        	$this->image_new_x = $this->image_x;
		}
	}

	private function clean() {
		$this->image_resize = false;
        $this->image_y = 0;
        $this->image_x = 0;
        $this->image_ratio = false;
        $this->image_ratio_x = false;
        $this->image_ratio_y = false;

        //stworzone w resize()
		$this->image_new_x = 0;
        $this->image_new_y = 0;	
        $this->image_real_x = 0;
        $this->image_real_y = 0;	
	}
}
?>