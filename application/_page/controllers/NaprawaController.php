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
    	$this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $Client = new Client();
        $this->view->repairs = $Client->getrepair($this->sesField['id']);
    }

}
