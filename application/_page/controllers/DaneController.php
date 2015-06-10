<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DaneController
 *
 * @author Kacper
 */
class DaneController extends PageController {
    public function init(){
        
    }
    
    //wyświetla dane klienta
    public function indexAction() {
        $Client = new Client();
        $this->view->data = $Client->getclientdata($this->sesField['id']);
    }
}
