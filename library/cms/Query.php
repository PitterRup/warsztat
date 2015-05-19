<?php
abstract class Query extends Library {
    protected $query;
    protected $result;
    protected $data;

    protected function setQuery($query) {
        $this->query = $query;
        // echo $query.'<br>';
        if($this->result = mysql_query($this->query)) return true;
        else return false; 
    }

    // mysql_fetch_array
    protected function fetchAll() {
        $this->data = array();
        $rows = array();
        while($row = mysql_fetch_array($this->result)) {
            foreach($row as $r => $value) $rows[$r] = stripSlashes($value);
            $this->data[] = $rows;
        }
    }
    protected function fetchRow() {
        $this->data = array();
        while($row = mysql_fetch_array($this->result))
            foreach($row as $r => $value) $this->data[$r] = stripSlashes($value);
    }
    // mysql_fetch_assoc
    protected function fetchAssocAll() {
        $this->data = array();
        $rows = array();
        while($row = mysql_fetch_assoc($this->result)) {
            foreach($row as $r => $value) $rows[$r] = stripSlashes($value);
            $this->data[] = $rows;
        }
    }
    protected function fetchAssocRow() {
        $this->data = array();
        while($row = mysql_fetch_assoc($this->result))
            foreach($row as $r => $value) $this->data[$r] = stripSlashes($value);
    }
    protected function numRows() {
        return mysql_num_rows($this->result);
    }
}
?>