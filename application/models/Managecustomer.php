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

    public function addCustomer($data, &$id) {
        $parm = $this->valuesl($data);
        $query = "INSERT INTO klient(nazw, nip_pesel, adr_zameld, nr_tel, mail, war_ubez, login, pass) VALUES(" . $parm . ")";
        if ($this->setQuery($query)) {
            $id = mysql_insert_id();
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

    public function delcustomer($id) {
        $updateguery = "UPDATE `samochod` SET `Klient_ID`=NULL WHERE Klient_ID='$id'";
        $query = "DELETE FROM klient WHERE id=$id ";
        if ($this->setQuery($updateguery) && $this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    private function getobjdata($id, $table) {
        $query = "SELECT * FROM $table WHERE id=$id";
        if ($this->setQuery($query)) {
            $this->fetchRow();
            return $this->data;
        } else
            return false;
    }

    public function getclient($id) {
        return $this->getobjdata($id, 'klient');
    }

    public function getcar($id) {
        return $this->getobjdata($id, 'samochod');
    }

    public function getcars($clientid) {
        $query = "SELECT * FROM samochod WHERE Klient_ID='$clientid'";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else
            return false;
    }

    public function updatecustomer($id, &$param) {
        $query = "UPDATE klient SET nazw='" . $param['nazw'] . "', nip_pesel='" . $param['nip'] . "',adr_zameld='" . $param['adres'] . "', nr_tel='" . $param['tel'] . "', mail='" . $param['email'] . "', war_ubez='" . $param['warub'] . "',login='" . $param['login'] . "' , pass='" . $param['haslo'] . "' WHERE id='$id'";
        if ($this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function updatecar($id, &$param) {
        $query ="UPDATE samochod SET Model='".$param['model']."',Marka='".$param['marka']."',Rok_pr='".$param['rok']."',Przeb='".$param['przebieg']."',Wer_wyp='".$param['wersja']."',Rodz_nadw='".$param['nadwozie']."',poj_sil='".$param['pojemnosc']."',Moc_sil='".$param['moc']."',Rodz_sil='".$param['rodzajsil']."',rodz_ol_sil='".$param['rodzajol']."',Naped='".$param['naped']. "' WHERE id='$id'";
         if ($this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

}
