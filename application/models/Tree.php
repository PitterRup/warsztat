<?php
class Tree extends GeneralModelsController {
	public function init() {
	}
	

	public function addNew() {
		$this->parentId = $this->addData['parentId'];
        $position = $this->addData['position'];
		$this->objId = $this->addData['objId'];
		$name = $this->addData['name'];
		$lp = $this->getLastPosition() + 1;
        $this->lang = $this->addData['lang'];
        $link = strtolower($this->_filter->toLink($name));
		$newLink = $this->findLink($link);
		$this->link = $link = $newLink['id'] ? $link.'_'.$this->objId : $link; 

		if($this->setQuery("INSERT INTO $this->_name VALUES('','$this->type','$position','$this->parentId','$this->objId','$link','$this->lang','1','$lp','1')")) {
            $this->setMsg('Obiekt został dodany do drzewa');
            return true;
        }
        else {
            $this->setError('Obiekt nie został dodany do drzewa');
            return false;
        }
	}
    public function delete() {
        if($this->setQuery("DELETE FROM $this->_name WHERE type='$this->type' AND objId='$this->objId'")) return true;
        else return false;
    }
    public function edit() {
        $link = strtolower($this->_filter->toLink($this->name));
        $newLink = $this->findLink($link);
        $this->link = $link = $newLink['id'] ? $link.'_'.$this->objId : $link; 
        $editPos = isset($this->position) ? ", position='$this->position'":'';
        if($this->setQuery("UPDATE $this->_name SET link='$link' $editPos WHERE type='$this->type' AND lang='$this->lang' AND objId='$this->objId'")) {
            $this->setMsg('Edycja w drzewie przebiegła pomyślnie.');
            return true;     
        }
        else {
            $this->setError('Wystąpił problem w bazie danych podczas edycji Drzewa.');
            return false;
        } 
    }
    public function findLink($link,$col=null) {
        $objId = $this->objId ? "AND objId!='$this->objId'":'';
        $this->setQuery("SELECT * FROM $this->_name WHERE type='$this->type' AND link='$link' AND lang='$this->lang' $objId ORDER BY lp DESC");       
        if($this->numRows()>0) {
            $this->fetchRow();
            if($col) return $this->data[$col];
            else return $this->data;
        }
        else return false;
    }

	public function getTreeId($objId) {
		$data = $this->getAll("type='$this->type' AND objId='$objId'");
		return $data[0]['id'];
	}
    public function getParentId($objId,$col=null) {
        $curObj = $this->getRow("type='$this->type' AND objId='$objId'");
        $parentId = $curObj['parentId'];
        $parentObj = $this->getRow("type='$this->type' AND id='$parentId'");

        if($this->numRows()>0) {
            if($col) return $this->data[$col];
            else return $this->data;
        }
        else return false;
    }

    public function getObj() {
        $visible = !$this->showHidden ? 'AND visible="1"' : '';
        $limit = $this->limit ? "LIMIT $this->limitStartPos,$this->limit" : "";
        
        $this->setQuery("SELECT * FROM $this->_name WHERE type='$this->type' AND lang='$this->lang' $visible ORDER BY lp DESC $limit");
        $this->fetchAll();
        return $this->data;
    }

	protected function getLastPosition() {
        $parentId = $this->parentId ? $this->parentId:0;
        $this->getAll('visible="1" AND type="'.$this->type.'" AND parentId="'.$this->parentId.'"');
        return $this->numRows();
    }

    public function hide($id) {
        return $this->setQuery("UPDATE $this->_name SET visible='0' WHERE type='$this->type' AND objId='$id'");  
    }
    public function show($id) {
        return $this->setQuery("UPDATE $this->_name SET visible='1' WHERE type='$this->type' AND objId='$id'"); 
    }
}
?>