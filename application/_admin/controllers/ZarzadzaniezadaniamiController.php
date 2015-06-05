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
        $Manage = new Managerepair();
        $this->view->date = $Manage->getweekarray();
        $this->view->mechanic = $Manage->countavailablemechanic();
        $this->view->places = $Manage->countavailableplace();
    }

    public function addrepairAction() {
        $Manage = new Managerepair();
        $date = $this->_getParam('date');
        if ($date) {
            $this->view->placestable = $Manage->getavailableplaces($date);
            $this->view->mechanicstable = $Manage->getavailablemechanics($date);
            $this->view->date=$date;
        }
    }

    public function includecarAction() {
        if ($this->_isPost()) {
            $Manage = new Managerepair();
            $this->view->date=  $this->_getParam('date');
            $this->view->mechanicid = $this->_getPost('mechanic');
            $this->view->placeid = $this->_getPost('place');
            $cardata = $this->_getPost('car');
            $clientdata=  $this->_getPost('client');
            
          $this->view->cars=$Manage->find($cardata,$clientdata);
        }
    }
    
    public function savedataAction(){
        if($this->_isPost()){
            $Manage = new Managerepair();
            $date=  $this->_getParam('date');
            $mechanicid=  $this->_getPost('mechanic');
            $placeid=  $this->_getPost('place');
            $carid=  $this->_getPost('carid');
            $info=  $this->_getPost('info');
            $status=  $this->_getPost('status');
            $price=  $this->_getPost('price');
            
            if($Manage->saverepair($carid,$mechanicid,$placeid,$date,$info,$status,$price)){
               echo "Informacje zapisane";
            }
            else{
               
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

}
