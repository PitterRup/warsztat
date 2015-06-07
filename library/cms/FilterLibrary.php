<?php
class FilterLibrary {
	public function getDayName($date) {
		$x = date("w",strtotime($date));
		$numbers = array(0,1,2,3,4,5,6);
		$days = array("Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota");
		$name = str_replace($numbers, $days, $x);
		return $name;
	}
	public function convertDate($date)
	{
	 	$date = explode("-", $date);
		$date = $date[2].'.'.$date[1].'.'.$date[0];
		return $date;
	}
	public function convertDatetime($datetime) {
		$datetime = explode(" ",$datetime);

		$date = explode("-",$datetime[0]);
		$date = $date[2].'.'.$date[1].'.'.$date[0];
	
		$time = explode(":",$datetime[1]);
		$time = $time[0].':'.$time[1];

		$datetime = $time.'&nbsp;&nbsp;&nbsp;'.$date;
	
		return $datetime;
	}
    public function convertDatetimeToDate($datetime,$op='string')
	{
		$datetime = explode(" ",$datetime);

		$date = explode("-",$datetime[0]);
		if($op=='string') $newDate = $date[2].'.'.$date[1].'.'.$date[0];
		if($op=='array') $newDate = $date;

		return $newDate;
	}
	public function convertToTextMonth($month)
	{
		$months = array('Sty','Lut','Mar','Kwi','Maj','Cze','Lip','Sie','Wrz','Paź','Lis','Gru');
		if($month[0]==0) $month = substr($month,1);
		return $months[$month];
	}

	public function getState($state) {
		$states = array("preparing","sent", "confirmed","realizing","finalised");
		$toChange = array("przygotowywana","niepotwierdzona", "nowa","realizowana","wysłana");
		$state = str_replace($states, $toChange, $state);
		return $state;
	}


	public function changeName($img_nazwa,$img_url)
	{
		$odczyt = pathinfo($img_nazwa);
		$rozszerzenie = $odczyt['extension'];
		$nr = rand(10000,99999);
		$nazwa = 'zdjecie-'.$nr.'.'.$rozszerzenie;
		$img_url = $this->createNewUrl($nazwa,$img_url);
		if(file_exists($img_url)) $nazwa = $this->changeName($nazwa,$img_url);
		return $nazwa;	
	}


	public function cutText($text,$liczba)
	{
		if($liczba<strlen($text)) $text = substr($text,0,$liczba).'...';
		return $text;
	}
	public function convertCommentText($text,$op)
	{
		if($op=='>')
		{
			$text = stripslashes($text);
			$text = nl2br($text);
			$text = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $text);
			$text = htmlspecialchars($text);
		}
		if($op=='<')
		{
			$text = $this->br2nl($text);
			$text = htmlspecialchars_decode($text);
		}
		return $text;	
	}
	public function convertAddress($address)
	{
		$address = explode(',',$address);
		if($address[5]!='') $address[5]='/'.$address[5];
		$address = $address[0].' '.$address[1].'-'.$address[2].','.$address[3].' '.$address[4].$address[5];
	
		return $address;
	}
	public function br2nl($string)
	{
	    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}
	public function convertMail($text)
	{
		$text = stripslashes($text);
		$text = strtr($text, "\xA5\x8C\x8F\xB9\x9C\x9F", "\xA1\xA6\xAC\xB1\xB6\xBC");	
		return $text;
	}
	public function convertText($text,$op)
	{
		if(is_array($text)) foreach($text as $k=>$v) $text[$k] = $this->convertText($v,$op);
		else
		{
			if($op=='doform')
			{
				$text = stripslashes($text);
				$text = str_replace("<br />"," ",$text);
				$text = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;","\t", $text);
				$text = htmlspecialchars($text);
			}
			if($op=='zform')
			{
				$text = addslashes($text);
				$text = nl2br($text);
				$text = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $text);
				$text = htmlspecialchars_decode($text);	
			}	
		}
		return $text;
	}

	public function cleanText($text) {
		$text = htmlspecialchars($text);
		$text = addslashes($text);

		return $text;
	}
	public function cleanINT($text) {
		$text = htmlspecialchars($text);
		$text = addslashes($text);
		if(!preg_match("/^[0-9]+$/D",$text)) $text=0;

		return $text;
	}
	public function cleanREQUEST($string) {
		if(is_array($string)) foreach($string as $k=>$v) $string[$k] = $this->cleanREQUEST($v); 
		else
		{
			$string = urldecode($string);
			$string = addslashes($string);
			$string = htmlspecialchars($string);
		}
		return $string;
	}
    public function cleanUrlToOpen($text) {
        $niedozwolone = array('');
        $zastapione = '';
        $text = str_replace($niedozwolone,$zastapione,$text);

        return $text;
    }
    public function cleanArray($array) {
    	if(is_array($array)) {
    		$cleanArray = array();
    		foreach($array as $key=>$value) $cleanArray[$key] = $this->cleanArray($value);
    		return $cleanArray;
    	}
    	else return $this->cleanText($array);
    }

    public function firstCharToUpper($string)
	{
		$firstChar = substr($string,0,1);
		$otherChars = substr($string,1);

		//powiększenie pierwszego znaku
		$firstChar = strtoUpper($firstChar);

		return $firstChar.$otherChars;
	}
	
	public function imagesStringToArray($imagesString)
	{
		$images = array();

		if(!empty($imagesString)) 
		{
			$imagesString = explode(";",$imagesString);

	        for($i=0;$i<count($imagesString);$i++)
	        {
	            $images[] = explode("#",$imagesString[$i]);
	        }
	    }
        return $images;
	}
	public function setArrayToDB($array)
	{
		$helpArray = array();
		foreach($array as $key=>$value) 
		{
			$helpArray[] = "$key='$value'";
		}
		$string = join($helpArray,",");

		return $string;
	}
	public function toArray($string)
	{
		$array = explode(";",$string);
		$newArray = array();
		foreach($array as $col) 
		{
			$col = explode(":",$col);
			$newArray[$col[0]] = $col[1];
		}

		return $newArray;
	}
	public function toLink($string)
	{
		$polishLetter = array("ą","Ą","ć","Ć","ę","Ę","ł","Ł","ó","Ó","ś","Ś","ź","Ź","ż","Ż","ń","Ń"," ",'"',"'");
		$replaceLetter = array("a","A","c","C","e","E","l","L","o","O","s","S","z","Z","z","Z","n","N","_","","");
		$string = str_replace($polishLetter,$replaceLetter, $string);
		return $string;
	}
	public function removeLetter($string,$exception=array())
	{
		$removeLetter = array("ą","Ą","ć","Ć","ę","Ę","ł","Ł","ó","Ó","ś","Ś","ź","Ź","ż","Ż");
		$replaceLetter = array("a","A","c","C","e","E","l","L","o","O","s","S","z","Z","z","Z");
		$string = str_replace($removeLetter,$replaceLetter,$string);
		return $string;
	}


	public function getNewFileName($dir,$name)
	{
		$new_name = $name;
		for($i=2;$i<=100;$i++)
		{
			if(file_exists($dir.$new_name)) 
			{
				$k = explode(".", $name);
				$new_name = $k[count($k)-2].'_'.$i.'.'.$k[count($k)-1];
			}
			else break;
		}

		return $new_name;
	}


	public function getRouteLink($string)
	{
		$array = explode("/",$string);
		$countA = count($array);
		if($countA==2) return $array[0].'/{TEXT}';
		elseif($countA==3) return $array[0].'/{TEXT}/{TEXT}';
		elseif($countA==4) return $array[0].'/{TEXT}/'.$array[2].'/{TEXT}';
		elseif($countA==5) return $array[0].'/{TEXT}/{TEXT}/'.$array[3].'/{TEXT}';
		else return false;
	}
	public function getRouteParams($string)
	{
		$array = explode("/",$string);
		$newArray = array();
		if(count($array)%2 != 0) {
			$a = 3;
			$newArray['album'] = $array[$a-1];
		}
		for($i=$a;$i<count($array);$i++) 
		{
			$newArray[$array[$i]] = $array[$i+1];
			$i++;
		}
		return $newArray;
	}

	// v.1.1
	public function checkFileName($dir,$name) {
		$new_name = $name;
		for($i=2; $i<=100; $i++){
			if(file_exists($dir.$new_name)) {
				$a = explode(".", $name);
				$new_name = $a[count($a)-2].'_'.$i.'.'.$a[count($a)-1];
			}
			else break;
		}
		return $new_name;
	}
	private function newFileName($dir,$name) {
		$file = pathinfo($name);
		$rozszerzenie = $file['extension'];
		$lp = rand(10000,99999);
		$new_name = 'image-'.$lp;
		return $this->checkFileName($dir,$new_name);
	}


	private function createNewUrl($nazwa,$url)
	{
		$url = explode("/",$url);
		$url[count($url)-1] = $nazwa;
		$url = implode("/",$url);
		return $url;
	}


    public function getChildrenToSelect($array,$categoryId) {
    	$this->i = isset($this->i) ? $this->i:1;
    	$mlS = '10';
        foreach($array as $row) {
        	$ml = $this->i*20 + $mlS;
        	$selected = $categoryId==$row['id'] ? 'selected':'';
        	echo '<option value="'.$row['id'].'" style="padding-left: '.$ml.'px" '.$selected.'>'.$row['name'].'</option>';
        	if(is_array($row['children'])) {
        		$this->i++;
        		$this->getChildrenToSelect($row['children'],$categoryId); 
        		$this->i--;
        	}	
        }
    }


    // metoda usuwająca z pliku json komentarze i zwraca jako obiekt
    public function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
		// search and remove comments like /* */ and //
		$json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);

		if(version_compare(phpversion(), '5.4.0', '>=')) {
			$json = json_decode($json, $assoc, $depth, $options);
		}
		elseif(version_compare(phpversion(), '5.3.0', '>=')) {
			$json = json_decode($json, $assoc, $depth);
		}
		else {
			$json = json_decode($json, $assoc);
		}

		return $json;
	}


	// metoda grupuje tablice wielowymiarowe po kolumnie
	public function groupBy($column,$array,$func=null,$funcColumn=null) {
		$newArray = array();
		foreach($array as $row) {
			if(gettype($func)=='NULL') {
				$columnGroup = $row[$column];
				unset($row[$column]);
				$newArray[$columnGroup][] = $row;
			}
			// zsumowanie wartości kolumny $funcColumn
			elseif($func=='SUM') $newArray[$row[$column]] += $row[$funcColumn];
		}
		return $newArray;
	}


	// metoda sprawdza czy istnieje w tablicy dwuwymiarowej dla któregoś wiersza dana wartość
	public function issetNotNull($array,$column) {
		foreach($array as $row) {
			if(strlen($row[$column])>0) return true;
		}
		return false;
	}

	// metoda składa adres do jednego stringa
	public function implodeAddress($city,$zipcode,$street,$houseNumber,$flatNumber) {
		return implode(";;",array($city,$zipcode,$street,$houseNumber,$flatNumber));
	}
	// metoda rozkłada adres ze stringa
	public function explodeAddress($string) {
		return explode(";;",$string);
	}

	// metoda losuje losowy ciąg znaków z podango wzorca
	public function randomString($length, $pattern=null, $firstZero=false) {
		if(gettype($pattern)=='NULL') $pattern = 'abcdifghijklmnopqrstuvwxyz0123456789';

		$pattern_length = strlen($pattern)-1;
		for($i=1;$i<=$length;$i++) {
			$nextLetter = $pattern{mt_rand(0,$pattern_length)};
			if($i==1 && !$firstZero) while($nextLetter=='0') $nextLetter = $pattern{mt_rand(0,$pattern_length)};
			$random_string .= $nextLetter;
		}
		
		return $random_string;
	}
}
?>