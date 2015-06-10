<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Client
 *
 * @author Kacper
 */
class Client extends GeneralModelsController {
    public function init() {
        
    }

    public function getrepair($clientid) {
        $query = "SELECT naprawa.Data, naprawa.Diagnoza,naprawa.Status, naprawa.Cena, samochod.Marka, samochod.Model, samochod.Rok_pr, samochod.ID FROM naprawa JOIN samochod ON naprawa.Samochod_ID=samochod.id "
                . "WHERE samochod.Klient_ID=$clientid ORDER BY naprawa.Data DESC";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else {
            return false;
        }
    }
    public function carlist($clientid){
      $query = "SELECT * FROM samochod WHERE Klient_ID='$clientid'";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else
            return false;
    }
    
    public function getcar($carid) {
        $query = "SELECT * FROM samochod WHERE id=$carid";
        if ($this->setQuery($query)) {
            $this->fetchRow();
            return $this->data;
          
        } else {
            return false;
        }
    }

    public function getclientdata($clientid) {
        $query = "SELECT * FROM klient WHERE id=$clientid";
        if ($this->setQuery($query)) {
            $this->fetchRow();
            return $this->data;
        } else {
            return false;
        }
    }

}
