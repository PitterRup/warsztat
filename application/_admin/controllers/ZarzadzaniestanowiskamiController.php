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
            $stat = $this->_getPost('stat');
            $Manage = new Manageplace();
            if ($Manage->addplace($postdata, $stat)) {
                $this->msg(true, "Stanowisko zostało zapisane");
            } else {
                $this->msg(false, "Wystąpił błąd stanowisko nie zostało zapisane");
            }
            $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniestanowiskami/placelist/type/msg', 0);
        }
    }

    public function placelistAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');

        $Manage = new Manageplace();
        $this->view->places = $Manage->getplacelist();
    }

    public function showplaceAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("placeid");
        if ($id) {
            $Manage = new Manageplace();
            $data = $Manage->getplace($id);
            if ($data) {
                $this->view->place = $data;
            } else {
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniestnowiskami/placelist", 0);
            }
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniestnowiskami/placelist", 0);
        }
    }

    public function editplaceAction() {
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("placeid");
        $Manage = new Manageplace();
        if ($this->_isPost() && $id) {
            $data = $this->_getPost('dane');
            $stat = $this->_getPost('stat');
            if ($Manage->updateplace($id, $data, $stat)) {
                $this->msg(true, 'Stanowisko zapisane');
            } else {
                $this->msg(false, 'Wystąpił błąd, dane nie zostały zapisane');
            }
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniestanowiskami/placelist/type/msg", 0);
        } else if ($id) {
            $this->view->place = $Manage->getplace($id);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniemestanowiskami/placelist", 0);
        }
    }

    public function delplaceAction() {
        $id= $this->_getParam("placeid");
        $Manage = new Manageplace();
        if($Manage->deleteplace($id)){
            $this->msg(true,'Stanowisko zostało usunięte');
        }
        else{
            $this->msg(false,'Wystąpił błąd! Stanowisko nie zostało usunięte');
        }
         $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniestanowiskami/placelist/type/msg", 0);
    }

}
