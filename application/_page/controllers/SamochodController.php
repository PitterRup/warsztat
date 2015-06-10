<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SamochodController
 *
 * @author Kacper
 */
class SamochodController extends PageController {

    public function init() {
        
    }

    //wyswietla liste samochodów klienta
    public function indexAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        
        $Client = new Client();
        $this->view->cars = $Client->carlist($this->sesField['id']);
    }
    
    //wyświetla szczegółowe informacje o samochodzie
    public function getcarAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');

        $Client = new Client();
        $carid = $this->_getParam('carid');
        if ($carid) {
            $this->view->car = $Client->getcar($carid);
        }
    }

}
