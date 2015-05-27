<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Basecontroller
 *
 * @author Kacper
 */
class Basemodel extends GeneralModelsController {
    public function init(){
        
    }
    
     protected function valuesl($data) {
        $list = array();
        foreach ($data as $val) {
            $list[] = "'" . $val . "'";
        }
        $parm = join(',', $list);
        return $parm;
    }
    
    protected function add(&$query){
        if ($this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }
    
    protected function getlist($table, $option=""){
        $query = "SELECT * FROM $table".$option;
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else
            return false;
    }
    
     protected function getdata($id, $table) {
        $query = "SELECT * FROM $table WHERE id=$id";
        if ($this->setQuery($query)) {
            $this->fetchRow();
            return $this->data;
        } else
            return false;
    }
    
    protected function update(&$query){
        if ($this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function delete($table, $id){
        $query = "DELETE FROM $table WHERE id=$id";
        if($this->setQuery($query)){
            return true;
        }
        else {
            return false;
        }
    }
}
