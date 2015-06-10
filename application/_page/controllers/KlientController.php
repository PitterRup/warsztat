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
class KlientController extends PageController {

    public function init() {
        
    }
//wyświetla informacje o naprawach
    public function indexAction() {
        $Client = new Client();
        $this->view->tabrepair = $Client->getrepair($this->sesField['id']);
    }
//wyswietla liste samochodów klienta
    public function carlistAction() {
        $Client = new Client();
        $this->view->cars = $Client->carlist($this->sesField['id']);
    }
//wyświetla szczegółowe informacje o samochodzie
    public function getcarAction() {
        $Client = new Client();
        $carid = $this->_getParam('carid');
        if ($carid) {
            $this->view->car = $Client->getcar($carid);
        }
    }
//wyświetla dane klienta
    public function showinfoAction() {
        $Client = new Client();
        $this->view->client = $Client->getclientdata($this->sesField['id']);
    }

}
