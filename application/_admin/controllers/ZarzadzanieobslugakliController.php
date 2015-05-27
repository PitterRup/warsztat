<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zarzadzanieobslugakli
 *
 * @author Kacper
 */
class ZarzadzanieobslugakliController extends AdminController {

    public function init() {
        
    }

    public function newserviceAction() {
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        } else {
            $postdata = $this->_getPost('dane');
            $Manage = new Manageservice();
            if (!$Manage->addservice($postdata)) {
                $this->msg(false, "Pracownik nie został zapisany.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzanieobslugakli/newservice/type/msg', 0);
            } else {
                $this->msg(true, "Klient został zapisany");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzanieobslugakli/servicelist/type/msg', 0);
            }
        }
    }

    public function servicelistAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');

        $Manage = new Manageservice();
        $this->view->services = $Manage->getservicelist();
    }

    public function showserviceAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("serviceid");
        if ($id) {
            $Manage = new Manageservice();
            $data = $Manage->getservice($id);
            if ($data) {
                $this->view->service = $data;
            } else {
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieobslugakli/servicelist", 0);
            }
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieobslugakli/servicelist", 0);
        }
    }

    public function editserviceAction() {
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("serviceid");
        $Manage = new Manageservice();
        if ($this->_isPost() && $id) {
            $data = $this->_getPost('dane');
            if ($Manage->updateservice($id, $data)) {
                $this->msg(true, 'Pracownik zapisany');
            } else {
                $this->msg(false, 'Wystąpił błąd dane pracownika nie zostały zapisane');
            }
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieobslugakli/servicelist/type/msg", 0);
        } else if ($id) {
            $this->view->service = $Manage->getservice($id);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieobslugakli/servicelist", 0);
        }
    }

    public function delserviceAction() {
        $id = $this->_getParam("serviceid");
        $Manage = new Manageservice();
        if ($Manage->delservice($id)) {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieobslugakli/servicelist", 0);
        } else {
            $this->msg(false, 'Wystąpił błąd pracownik nie został usunięty');
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieobslugaklienta/servicelist/type/msg", 0);
        }
    }

}
