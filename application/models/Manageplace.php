<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manageplace
 *
 * @author Kacper
 */
class Manageplace extends GeneralModelsController {
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
    
    public function addplace(&$data){
      $parm = $this->valuesl($data);
        $query = "INSERT INTO stanowisko(Nazw,ladow,wymiary,Posiad_tun,przezn) VALUES(" . $parm .")";
        if ($this->setQuery($query)) {
            return true;
        } else {
            return false;
        }  
    }
    
    public function getplacelist(){
        $query = "SELECT * FROM stanowisko";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else
            return false;
    }
}
