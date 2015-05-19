<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zarzadzajklientem
 *
 * @author Kacper
 */
class Managecustomer extends GeneralModelsController {

    public function init() {
        
    }

    protected function valuesl($data) {
        $list = array();
        foreach ($data as $val) {
            $list[] = "'" . $val . "'";
        }
        $parm = join(',', $list);
        return $parm;
    }

    public function addCustomer($data,&$id) {
        $parm = $this->valuesl($data);
        $query = "INSERT INTO klient(nazw, nip_pesel, adr_zameld, nr_tel, mail, war_ubez, login, pass) VALUES(" . $parm . ")";
        if ($this->setQuery($query)) {
              $id=mysql_insert_id();
            return true;
        } else {
            return false;
        }
    }

    public function addCar($data) {
        $parm = $this->valuesl($data);
        $query = "INSERT INTO samochod(Model, Marka, Rok_pr, Przeb, Wer_wyp, Rodz_nadw,poj_sil,Moc_sil,Rodz_sil,rodz_ol_sil,Naped,Klient_ID) VALUES(" . $parm . ")";
        if ($this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function getcustomerlist() {
        $query = "SELECT * FROM klient";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else
            return false;
    }
    public function delcustomer($id){
        $updateguery="UPDATE `samochod` SET `Klient_ID`=NULL WHERE Klient_ID='$id'";
        $query ="DELETE FROM klient WHERE ID=$id ";
         if ($this->setQuery($updateguery)&&$this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

}
