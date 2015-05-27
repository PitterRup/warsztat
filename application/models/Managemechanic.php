<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Menagemechanic
 *
 * @author Kacper
 */
class Managemechanic extends Basemodel {
 
   
   public function addMechanic(&$data){
       $parm = $this->valuesl($data);
        $query = "INSERT INTO pracownik(Imie,Nazw,pesel,adr_zam,nr_tel,mail,specj,dosw_zaw,dyspoz,obsl_stan,login,pass,Fun_Prac_ID) VALUES(" . $parm . ",'2')";
       return $this->add($query);
   }
   
     public function getmechaniclist() {
       return $this->getlist("pracownik", " WHERE Fun_Prac_ID ='2'");
    }
    
    public function updatemechanic($id, &$param){
    $query = "UPDATE pracownik SET Imie='" . $param['imie'] . "',Nazw='".$param['nazw']."', pesel='" . $param['pesel'] . "',adr_zam='" . $param['adres'] . "', nr_tel='" . $param['tel'] . "', mail='" . $param['email'] . "',specj='" . $param['spe'] ."',dosw_zaw='".$param['dosw'] ."',dyspoz='".$param['dysp']."',obsl_stan='".$param['stan']."',login='" . $param['login'] . "' , pass='" . $param['haslo'] . "' WHERE id='$id'";
       return $this->update($query);
    }
    
    public function getmechanic($id){
        $query = "SELECT * FROM pracownik WHERE Fun_Prac_ID ='2' AND id='".$id."'";
        if ($this->setQuery($query)) {
            $this->fetchRow();
            return $this->data;
        } else
            return false;
    }
    public function delmechanic($id){
        return $this->delete("pracownik", $id);
    }
}
