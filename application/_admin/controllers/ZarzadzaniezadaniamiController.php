<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zarzadzanienaprawami
 *
 * @author Kacper
 */
class ZarzadzaniezadaniamiController extends AdminController {

    public function init() {
        
    }

    public function indexAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $Manage = new Managerepair();
        $this->view->date = $Manage->getweekarray();
        $this->view->mechanic = $Manage->countavailablemechanic();
        $this->view->places = $Manage->countavailableplace();
    }

    public function addrepairAction() {
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        // .searchBox
        $this->_headScript($this->baseUrl.'/public/js/searchBox/searchBox.js');
        $this->_linkScript($this->baseUrl.'/public/js/searchBox/searchBox.css');

        $Manage = new Managerepair();
        $date = $this->_getParam('date');
        if ($date) {
            $this->view->placestable = $Manage->getavailableplaces($date);
            $this->view->mechanicstable = $Manage->getavailablemechanics($date);
            $this->view->date=$date;
        }
    }
    
    public function savedataAction(){
        if($this->_isPost()){
            $Manage = new Managerepair();
            $date=  $this->_getParam('date');
            $mechanicid=  $this->_getPost('mechanic');
            $placeid=  $this->_getPost('place');
            $carid=  $this->_getPost('carId');
            $info=  $this->_getPost('info');
            $status=  $this->_getPost('status');
            $price=  $this->_getPost('price');
            
            if($Manage->saverepair($carid,$mechanicid,$placeid,$date,$info,$status,$price)){
                $this->msg(true, "Naprawa została zapisana.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniezadaniami/index/type/msg', 0);
            }
            else{
                $this->msg(false, "Naprawa nie została zapisana.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniezadaniami/index/type/msg', 0);
            }
        }
    }
    
    public function getdetailsAction(){
         $Manage = new Managerepair();
        $date = $this->_getParam('date');
        $datar;
        $mechanicr;
        if($Manage->getdetails($date,$datar,$mechanicr)){
            $this->view->data=$datar;
            $this->view->mechanic=$mechanicr; 
        }
    }

    public function findClientAction() {
        $string = $this->_getPost("searchString");
        $Manage = new Managecustomer;
        $rows = $Manage->find($string);
        $count = $Manage->getNumRows();
        echo $count>0 ? $rows : '<center class="noResults">Nie znaleziono klienta.</center>';
    }

    public function getClientCarsAction() {
        $clientId = $this->_getPost('clientId');
        $Manage = new Managecustomer;
        $data = $Manage->getcars($clientId);
        
        foreach($data as $row) {
            $rows .= '<li objId="'.$row['id'].'">'.$row['Marka'].' '.$row['Model'].' '.$row['poj_sil'].' '.$row['Rodz_sil'].' '.$row['Rok_pr'].'</li>';
        }

        $count = $Manage->getNumRows();
        echo $count>0 ? $rows : '<center class="noResults">Brak samochodów dla tego klienta.</center>';
    }

}
