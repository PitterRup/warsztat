<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manageservice
 *
 * @author Kacper
 */
class Manageservice extends Basemodel {
    
    public function addservice(&$data){
         $parm = $this->valuesl($data);
        $query = "INSERT INTO pracownik(Imie,Nazw,pesel,adr_zam,nr_tel,mail,specj,dosw_zaw,dyspoz,login,pass,Fun_Prac_ID) VALUES(" . $parm . ",'3')";
       return $this->add($query);
    }
    
    public function getservicelist(){
         return $this->getlist("pracownik", " WHERE Fun_Prac_ID ='3'");
    }
    
    public function getservice($id){
         $query = "SELECT * FROM pracownik WHERE Fun_Prac_ID ='3' AND id='".$id."'";
        if ($this->setQuery($query)) {
            $this->fetchRow();
            return $this->data;
        } else
            return false;
    }
    
    public function updateservice($id,&$param){
        $query = "UPDATE pracownik SET Imie='" . $param['imie'] . "',Nazw='".$param['nazw']."', pesel='" . $param['pesel'] . "',adr_zam='" . $param['adres'] . "', nr_tel='" . $param['tel'] . "', mail='" . $param['email'] . "',specj='" . $param['spe'] ."',dosw_zaw='".$param['dosw'] ."',dyspoz='".$param['dysp']."',obsl_stan='".$param['stan']."',login='" . $param['login'] . "' , pass='" . $param['haslo'] . "' WHERE id='$id'";
       return $this->update($query);
    }
    
    public function delservice($id){
        return $this->delete("pracownik", $id);
    }
}
