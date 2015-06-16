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
class Managerepair extends Basemodel {

    public function getweekarray() {
        $numberofday = 30;
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
            $mechanic = $this->data['COUNT(*)'];
        } else {
            return false;
        }
        $weekarray = $this->getweekarray();
        $availablemechanic = array();
        foreach ($weekarray as $time) {
            if ($this->setQuery("SELECT COUNT(*) FROM naprawa_pracownik WHERE Naprawa_ID IN(SELECT id FROM naprawa WHERE Data= STR_TO_DATE('$time','%Y-%m-%d'))")) {
                $this->fetchRow();
                $availablemechanic[] = (int) $mechanic - (int) $this->data['COUNT(*)'];
            }
        }
        return $availablemechanic;
    }

    public function countavailableplace() {
        $num = "SELECT COUNT(*) FROM stanowisko ";
        if ($this->setQuery($num)) {
            $this->fetchRow();
            $places = $this->data['COUNT(*)'];
        } else {
            return false;
        }
        $weekarray = $this->getweekarray();
        $availableplaces = array();
        foreach ($weekarray as $time) {
            if ($this->setQuery("SELECT COUNT(*) FROM naprawa WHERE Data= STR_TO_DATE('$time','%Y-%m-%d')")) {
                $this->fetchRow();
                $availableplaces[] = (int) $places - (int) $this->data['COUNT(*)'];
            }
        }
        return $availableplaces;
    }

    public function getavailablemechanics($date) {
        $query = "SELECT id, Imie, Nazw,nr_tel,pesel FROM pracownik WHERE id NOT IN( SELECT Pracownik_ID FROM naprawa_pracownik WHERE Naprawa_ID IN (SELECT id FROM naprawa WHERE Data= STR_TO_DATE('$date','%Y-%m-%d'))) AND Fun_Prac_ID=2";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else
            return false;
    }

    public function getavailableplaces($date) {
        $query = "SELECT id, ladow, Nazw, przezn, wymiary, Posiad_tun FROM stanowisko WHERE id NOT IN (SELECT Stanowisko_ID FROM naprawa WHERE Data= STR_TO_DATE('$date','%Y-%m-%d'))";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else
            return false;
    }

    public function find(&$carinfo, &$clientinfo) {
        $query = "SELECT * FROM samochod WHERE Marka='" . $carinfo['Marka'] . "' OR Model='" . $carinfo['Model'] . "' OR Rok_pr='" . $carinfo['Rok_pr'] . "' OR Klient_ID=(SELECT id FROM klient WHERE nazw='" . $clientinfo . "')";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            return $this->data;
        } else {
            return false;
        }
    }

    public function saverepair($carid, $mechanicid, $placeid, $date, $info, $status, $price) {
        $repair = "INSERT INTO naprawa(Data,Diagnoza,Status,Stanowisko_ID,Samochod_ID,Cena) VALUES (STR_TO_DATE('$date','%Y-%m-%d'),'$info','$status','$placeid','$carid','$price')";
        if ($this->setQuery($repair)) {
            $id = mysql_insert_id();
            foreach ($mechanicid as $mechanic) {
                $repairid = "INSERT INTO naprawa_pracownik VALUES ('$id','$mechanic')";
                $this->setQuery($repairid);
            }
            return true;
        } else {
            return false;
        }
    }

    public function orderparts($repairid, &$data) {

        foreach ($data as $part) {
            $query = "INSERT INTO czesci(nazwa,naprawaID) VALUES ('$part','$repairid')";
            if (!$this->setQuery($query)) {
                return false;
            }
        }
        return true;
    }

    public function delpart($id) {
        $query = "DELETE FROM czesci WHERE id='$id'";
        if ($this->setQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function getdetails($date, &$datar, &$mechanicr) {
        $mechanicr = array();
        $query = "SELECT * FROM `naprawa` JOIN `stanowisko` ON naprawa.Stanowisko_ID= stanowisko.id JOIN `samochod` ON naprawa.Samochod_ID=samochod.id WHERE naprawa.Data=STR_TO_DATE('$date','%Y-%m-%d') ";
        if ($this->setQuery($query)) {
            $this->fetchAll();
            $datar = $this->data;
            foreach ($datar as $key => $num) {
                $mechanic = "SELECT * FROM `pracownik` WHERE id=(SELECT Pracownik_ID FROM Naprawa_Pracownik WHERE Naprawa_ID=" . $num['id'] . ")";
                $this->setQuery($mechanic);
                $this->fetchAll();
                $mechanicr[$key] = $this->data;
            }
            return true;
        } else {
            return false;
        }
    }

    public function getrepairslist($mechanikId = null, $date = null) {
        $mf = $mechanikId ? "naprawa_pracownik p," : '';
        $m = $mechanikId ? "p.Pracownik_ID='$mechanikId' AND p.Naprawa_ID=n.id AND" : '';
        $n = $date ? "AND n.Data=STR_TO_DATE('$date','%Y-%m-%d')" : '';
        $this->setQuery("SELECT n.*, s.Nazw, a.Model, a.Marka, a.Rok_pr
            FROM naprawa n, $mf stanowisko s, samochod a
            WHERE $m n.Stanowisko_ID=s.id AND n.Samochod_ID=a.id $n
            ORDER BY Data DESC");
        $this->fetchAssocAll();
        return $this->data;
    }

    public function getrepair($mechanikId = null, $repairid) {
        $mf = $mechanikId ? "naprawa_pracownik p," : '';
        $m = $mechanikId ? "p.Pracownik_ID='$mechanikId' AND p.Naprawa_ID=n.id AND" : '';
        $this->setQuery("SELECT n.*, s.Nazw, a.*
            FROM naprawa n, $mf stanowisko s, samochod a
            WHERE $m n.Stanowisko_ID=s.id AND n.Samochod_ID=a.id AND n.id='$repairid'");
        $this->fetchAssocRow();
        return $this->data;
    }

    public function editrepair($postData, $repairid) {
        $status = $postData['status'];
        $diagnoza = $postData['info'];
        $price = $postData['price'];
        $stanowisko_id = $postData['place'];
        $stanowisko = $stanowisko_id ? ", Stanowisko_ID='$stanowisko_id'" : '';

        return $this->setQuery("UPDATE naprawa SET Status='$status', Diagnoza='$diagnoza', Cena='$price' $stanowisko WHERE id='$repairid'");
    }

    public function countrepair() {
        $weekarray = $this->getweekarray();
        $repair = array();
        foreach ($weekarray as $time) {
            $query = "SELECT Count(*) FROM naprawa WHERE Data=STR_TO_DATE('$time','%Y-%m-%d')";
            if ($this->setQuery($query)) {
                $this->fetchRow();
                $repair[] = $this->data['Count(*)'];
            } else {
                return false;
            }
        }
        return $repair;
    }

}
