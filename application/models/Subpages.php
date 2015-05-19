<?php
class Subpages extends GeneralModelsController {
	public $limitStartPos;
	public $limit;
	public $showHidden;
    public $position;

	public function init() {
	}

	public function getPage($link) {
		$this->setQuery("SELECT p.* FROM $this->_name p, $this->_Tree t WHERE t.link='$link' AND t.objId=p.id AND t.lang='$this->lang'");
		$this->fetchRow();
		return $this->data;
	}	

	public function getObj() {
        $limit = $this->limit ? "LIMIT $this->limitStartPos,$this->limit" : '';
        $visible = !$this->showHidden ? 'AND t.visible="1"' : '';
        $lang = $this->lang=='all' ? '':"AND t.lang='$this->lang'";
        $position = $this->position ? "AND t.position='$this->position'":'';

        $this->setQuery("SELECT s.id,s.title,t.* FROM $this->_Tree t, $this->_name s WHERE t.type='subpages' AND t.objId=s.id AND s.added='1' $position $lang $visible ORDER BY t.lp DESC $limit");
        $this->fetchAll();
        return $this->data;
    }
    public function getObject() {
        $this->setQuery("SELECT s.id,s.title,s.text,t.position FROM $this->_Tree t, $this->_name s WHERE t.type='subpages' AND t.objId=s.id AND s.id='$this->id'");
        $this->fetchRow();
        return $this->data;
    }

    public function editPrepare() {    
        $this->postData['added'] = '1';
        if($this->edit()) {
            $this->msg[] = 'Podstrona została dodana';    
            return true;
        }
        else {
            $this->errors[] = 'Wystąpił błąd. Podstrona nie została dodana. Proszę spróbować ponownie.'; 
            return false;
        } 
    }

    public function addNew() {
        $this->setQuery("INSERT INTO $this->_name VALUES('','','','0')");
        $this->id = mysql_insert_id();
    }
}
?>