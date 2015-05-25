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
    public function init(){
        
    }
    
    public function indexAction(){
        $Manage= new Managerepair();
        $this->view->date=$Manage->getweekarray();
        $this->view->repair=$Manage->getrepair();
    }
}
