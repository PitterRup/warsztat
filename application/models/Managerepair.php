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

    public function getrepair() {
        $weekarray = $this->getweekarray();
        $repairarray = array();
        foreach ($weekarray as $day) {
            $query = "SELECT ID_Zadania,Diagnoza,Status FROM zadania WHERE Data=STR_TO_DATE('$day','%Y-%m-%d')";
            if ($this->setQuery($query)) {
                $this->fetchAll();
                $repairarray[] = $this->data;
            }
            
        }
        return $repairarray;
    }
    
    public function getavailablemechanic($date){
        
    }

}
