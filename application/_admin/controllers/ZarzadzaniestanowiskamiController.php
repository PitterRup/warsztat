<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ZarzadzaniestanowiskamiController
 *
 * @author Kacper
 */
class ZarzadzaniestanowiskamiController extends AdminController {

    public function init() {
        
    }

    public function newplaceAction() {
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        } else {
            $postdata = $this->_getPost('dane');
            $Manage = new Manageplace();
            if ($Manage->addplace($postdata)) {
                $this->msg(true, "Stanowisko zostało zapisane");
            } else {
                $this->msg(false, "Wystąpił błąd stanowisko nie zostało zapisane");
            }
            $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniestanowiskami/placelist/type/msg', 0);
        }
    }
    
    public function placelistAction(){
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');

        $Manage = new Manageplace();
        $this->view->places = $Manage->getplacelist();
    }

}
