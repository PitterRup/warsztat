<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Daneklienta
 *
 * @author Kacper
 */
class NaprawaController extends PageController {

    public function init() {
        
    }

//wyświetla informacje o naprawach
    public function indexAction() {
        $Client = new Client();
        $this->view->tabrepair = $Client->getrepair($this->sesField['id']);
    }

}
