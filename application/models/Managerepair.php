<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Managerepair
 *
 * @author Kacper
 */
class Managerepair extends GeneralModelsController {

    public function init() {
        
    }

    public function getweekarray() {
        $numberofday = 7;
        $weekarray = array(date("Y-m-d", time()));
        for ($i = 1; $i <= $numberofday; $i++) {
            $weekarray[] = date("Y-m-d", strtotime("+$i day"));
        }
        return $weekarray;
    }

    public function countavailablemechanic() {
        $num = "SELECT COUNT(*) FROM pracownik WHERE Fun_Prac_ID=2";
        if ($this->setQuery($num)) {
            $this->fetchRow();
            $mechanic = $this->data;
        }
        else{
           return false;
        }
        $weekarray = $this->getweekarray();
        $availablemechanic = array();
        foreach ($weekarray as $time) {
           if( $this->setQuery("SELECT COUNT(*) FROM naprawa_pracownik WHERE Naprawa_ID=(SELECT id FROM naprawa WHERE Data= STR_TO_DATE('$time','%Y-%m-%d'))")){
            $this->fetchRow();
            $availablemechanic[] = (int)$mechanic - (int)$this->data['COUNT(*)'];
           }
        }
        return $availablemechanic;
    }

    public function countavailableplace() {
        $num = "SELECT COUNT(*) FROM stanowisko ";
        if ($this->setQuery($num)) {
            $this->fetchRow();
            $places = $this->data;
        }
        else{
           return false;
        }
        $weekarray = $this->getweekarray();
        $availableplaces = array();
        foreach ($weekarray as $time) {
           if( $this->setQuery("SELECT COUNT(*) FROM naprawa WHERE Data= STR_TO_DATE('$time','%Y-%m-%d')")){
            $this->fetchRow();
            $availableplaces[] = (int)$places - (int)$this->data['COUNT(*)'];
           }
        }
        return $availableplaces;
    }
    
    public function getavailablemechanics($date){
        
    }
    
    public function getavailableplaces($date){
        
    }

}
