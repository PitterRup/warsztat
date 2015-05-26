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
class Manageplace extends Basecontroller {

    public function addplace(&$data, $stat) {
        $parm = $this->valuesl($data);
        if (!$stat) {
            $stat = 'n';
        }
        $query = "INSERT INTO stanowisko(Nazw,ladow,wymiary,przezn,Posiad_tun) VALUES(" . $parm . ",'$stat')";
        return $this->add($query);
    }

    public function getplacelist() {
        return $this->getlist("stanowisko");
    }

    public function getplace($id) {
        return $this->getdata($id, 'stanowisko');
    }

    public function updateplace($id, &$param, $stat) {
        if (!$stat) {
            $stat = 'n';
        }
        $query = "UPDATE stanowisko SET Nazw='" . $param['Nazw'] . "',ladow='" . $param['ladow'] . "', wymiary='" . $param['wymiary'] . "',Posiad_tun='" . $stat . "', przezn='" . $param['przezn'] . "' WHERE id='$id'";
        return $this->update($query);
    }

    public function deleteplace($id){
        return $this->delete("stanowisko",$id);
    }
}
