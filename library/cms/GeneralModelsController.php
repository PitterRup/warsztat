<?php
require_once PATH."library/cms/Query.php";
// generalny kontroler modeli
abstract class GeneralModelsController extends Query {
    protected $_name; // nazwa dla tabeli w db
    protected $errors = array();
    protected $msg = array();
    public $limit = false;
    public $limitStartPos = 0;
    public $showHidden = false;
    public $postData;
    public $categoryId; // usuwać powoli
    public $parentId; 
    public $id; 
    public $objId;
    public $name;

    public function __construct($args=null) {
        $this->_name = strtolower(get_class($this));

        // init klasy potomnej
        $this->init($args);
    }
    public function __get($name) {
        $preparedName = substr($name,1);
        if(isset($this->_config->dbTableNames->$preparedName)) return $this->_config->dbTableNames->$preparedName;
        elseif(parent::__get($name)) return parent::__get($name);
    }  


    // błędy modeli
    public function getErrors($id=null) { 
        if(gettype($id)=='integer') return count($this->errors)>0 ? $this->errors[$id]:false;
        elseif($id=='last') return count($this->errors)>0 ? $this->errors[count($this->errors)-1]:false;
        else return count($this->errors)>0 ? $this->errors:false; 
    }
    protected function setError($error) { 
        $this->errors[] = $error; 
        $this->view->setLog($error,'red');
    }
    public function getMsg($id=null) { 
        if(gettype($id)=='integer') return count($this->msg)>0 ? $this->msg[$id]:false;
        elseif($id=='last') return count($this->msg)>0 ? $this->msg[count($this->msg)-1]:false;
        else return count($this->msg)>0 ? $this->msg:false; 
    }
    protected function setMsg($msg) { 
        $this->msg[] = $msg; 
        $this->view->setLog($msg);
    }

    //uniwersalna funkcja dla wszystkich modeli wydobywająca jeden rekord o podanych warunkach
    public function getRow($where=false) {
        $where = $where ? 'WHERE '.$where:'';

        $this->setQuery("SELECT * FROM $this->_name $where");
        $this->fetchRow();
        
        return $this->data;
    }
    //uniwersalna funkcja dla wszystkich modeli wydobywająca jeden rekord o podanych warunkach
    public function getAll($where=null,$order=null) {
        $where = $where ? 'WHERE '.$where : '';
        $order = !$order ? '' : 'ORDER BY '.$order;

        $this->setQuery("SELECT * FROM $this->_name $where $order");
        $this->fetchAll();
        return $this->data;
    }
    //funkcja zwraca tylko podaną kolumnę lub wszystko (getAll)
    public function getColumn($columns='*',$where=null,$order=null) {
        $where = $where ? 'WHERE '.$where : '';
        $order = !$order ? '' : 'ORDER BY '.$order;

        $this->setQuery("SELECT $columns FROM $this->_name $where $order");
        $this->fetchAll();

        $data = array();
        if($columns!='*') foreach($this->data as $r) $data[] = $r[0];
        else $data = $this->data;

        return $data;        
    }
    //funkcja zwracająca tablice o kluczach i wartościach zgodnych z columnami podanymi w argumentach
    public function getColumns($column='id',$column2='name',$where=null,$order='ORDER BY lp DESC') {
        $where = $where ? 'WHERE '.$where : '';
        if($order!='ORDER BY lp DESC') $order = 'ORDER BY '.$order;

        $this->setQuery("SELECT * FROM $this->_name $where $order");
        $this->fetchAll();

        $data = array();
        foreach($this->data as $r) $data[$r[$column]] = $r[$column2];

        return $data;         
    }
    // metoda zwraca wszystkie obiekty dla podanej ścieżki
    // pomija obiekty o rozszerzeniu podanym w wyjątkach
    public function getObjectsDirectory($dir,$type='all',$exception=array()) {
        $data = array();

        //wybranie do tablicy obiektów folderu na serwerze
        $objects = scandir($this->serverPath.'/'.$dir);

        foreach ($objects as $object) {
            if($object != "." && $object != "..") {
                $ext = pathinfo($object);
                $ext = $ext['extension'];
                if(in_array($ext,$exception)) continue;
                if($type=='all') $data[] = $object;          
                elseif(filetype($this->serverPath.'/'.$dir.$object)==$type) $data[] = $object;  
            }
        }
        return $data;
    }
    //funkcja zwracająca obiekty o odpowiedniej kategorii i opcjach
    public function getImages() {
        if(!$this->showHidden) $visible = 'AND visible="1"';
        if($this->limit) $limit = "LIMIT $this->limitStartPos,$this->limit";
        
        $this->setQuery("SELECT * FROM $this->_name WHERE categoryId='$this->categoryId' $visible ORDER BY lp DESC $limit");
        $this->fetchAll();
        return $this->data;
    }
    //funkcja zwraca kategorie zdjęć ?????
    public function getImgsCat() {
        $this->setQuery("SELECT * FROM $this->_name WHERE visible='1' AND categoryId='$this->categoryId' ORDER BY lp DESC");
        $this->fetchAll();
        return $this->data;
    }
    // zwrócenie ilości wyników
    public function getNumRows() {
        return $this->numRows();
    }

    //uniwersalna funkcja dla wszystkich modeli usuwająca wiersz z bazy danych
    public function deleteRow($where=null) {
        $where = $where ? 'WHERE '.$where : "WHERE id='$this->id'";
        return $this->setQuery("DELETE FROM $this->_name $where") ? true:false;
    }

    //uniwersalna funkcja dla wszystkich modeli do zmiany pozycji rekordów w stosunku do siebie
    public function changePosition($array,$opWhere=false) {
        $lp = $this->getLastPosition();
        foreach($array as $id) {    
            if(!$opWhere) $where = "id='$id'";
            else $where = "type='$this->type' AND parentId='$this->parentId' AND objId='$id'";       
            
            if(!$this->setQuery("UPDATE $this->_name SET lp='$lp' WHERE $where")) $this->setError('Nie udało się zmienić pozycji obiektu o id: '.$id);        
            $lp--;
        } 
    }

    //uniwersalne funkcje dla wszystkich modeli zmieniające ich widoczność
    public function hide($id) {
        return $this->setQuery("UPDATE $this->_name SET visible='0' WHERE id='$id'");  
    }
    public function show($id) {
        return $this->setQuery("UPDATE $this->_name SET visible='1' WHERE id='$id'"); 
    }

    //uniwersalna funkcja sprawdzająca ile jest rekordów w tabeli spełniających warunki
    public function count($where=null) {
        $where = $where ? 'WHERE '.$where : '';
        $this->setQuery("SELECT * FROM $this->_name $where");
        return $this->numRows();
    }

    //funkcja przygotowująca nowy rekord 
    public function prepareReady() {
        //ustalenie id dodawanego albumu (pobiera z bazy ostatni nie dodany[added=0] lub tworzy nowy)
        $this->getReady();
        if($this->numRows()==1) $this->id = $this->data['id'];
        else $this->addNew();
    }
    //funkcja wyciągająca z bazy przygotowany rekord do dodania 
    public function getReady() {
        $this->setQuery("SELECT * FROM $this->_name WHERE added='0'");
        $this->fetchRow();
        return $this->data;       
    }

    //funkcja edytująca tabele przyjmująca argumenty z tablicy
    public function edit($updateAddDate=false) {
        if($updateAddDate) $addDate = ",addDate=now()";
        $data = $this->_filter->setArrayToDB($this->postData);
        if($this->setQuery("UPDATE $this->_name SET $data $addDate WHERE id='$this->id'")) return true;
        else return false; 
    }

    // metoda tworzy katalogi na serwerze (pod zdjecia o różnych rozmiarach) z pliku konfiguracyjnego
    protected function addDirectories() { 
        $albumsDir = $this->config->albumsDir;
        if(mkdir($this->serverPath.'/'.$albumsDir.$this->id)){ 
            chmod($this->serverPath.'/'.$albumsDir.$this->id, 0777);
            for($i=1; $i<=count((array) $this->config->images); $i++) {
                $type = "img$i";
                $config = $this->config->images->$type;
                $dir = $this->_config->getDirName($config).'/';
                if(mkdir($this->serverPath.'/'.$albumsDir.$this->id.'/'.$dir)) chmod($this->serverPath.'/'.$albumsDir.$this->id.'/'.$dir, 0777);
            }
        }
        else $this->setError('Nie można utworzyć katalogu na serwerze.');
    }
    protected function deleteDirectory($dir) {
        if(is_dir($dir)) {
            $objects = scandir($dir);
            foreach($objects as $object) {
                if($object != "." && $object != "..") {
                    if(filetype($dir.$object) == "dir") $this->deleteDirectory($dir.$object.'/'); 
                    else unlink($dir.$object);    
                }          
            }

            if(rmdir($dir)) return true;
            else return false;
        }
        else return true;
    }
}
?>