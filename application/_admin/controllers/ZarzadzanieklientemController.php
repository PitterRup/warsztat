<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zarzadzanie_klientem
 *
 * @author Kacper
 */
class ZarzadzanieklientemController extends AdminController {

    public function init() {
        // zdefiniowanie akcji domyślnej
        // jest wykonywana gdy w adresie nie podany żadnej akcji, a akcja index nie istnieje
        $this->defaultAction = 'customerlist';
    }

    public function newcustomerAction() {
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        }
        // po wysłaniu formularza metodą POST
        else {
            $postdata = $this->_getPost('dane');
            $permissions = '{"_page":1}';
            $postdata[] = $permissions;
            $id = 0;
            $Manage = new Managecustomer();
            if (!$Manage->addCustomer($postdata, $id)) {
                $this->msg(false, "Klient nie został zapisany.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/newcustomer/type/msg', 0);
            } else {
                $this->msg(true, "Klient został zapisany");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/newcar/clientid/' . $id . '/type/msg', 0);
            }
        }
    }

    public function newcarAction() {
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
            $this->view->clientid = $this->_getParam('clientid');
        } else {
            $postdata = $this->_getPost('dane');
            $param = $this->_getParam("clientid");
            $postdata['clientid'] = $param;

            $Manage = new Managecustomer();
            if (!$Manage->addCar($postdata)) {
                $this->msg(false, "Samochód nie został dodany.");
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/newcar/clientid/$param/type/msg", 0);
            } else {
                $this->msg(true, "Klient został zapisany.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/customerlist/type/msg', 0);
            }
        }
    }

    public function customerlistAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');

        $Manage = new Managecustomer();
        $this->view->customers = $Manage->getcustomerlist();
    }

    public function delcustomerAction() {
        $id = $this->_getParam("clientid");
        if ($id) {
            $Manage = new Managecustomer();
            if ($Manage->delcustomer($id)) {
                $this->msg(true, "Klient został usunięty.");
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist/type/msg", 0);
            } else {
                $this->msg(false, 'Wystąpił błąd! Klient nie został usunięty');
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist/type/msg", 0);
            }
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
        }
    }

    public function editcustomerAction() {
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("clientid");
        $Manage = new Managecustomer();
        if ($this->_isPost() && $id) {
            $data = $this->_getPost('dane');
            if($Manage->updatecustomer($id, $data)) $this->msg(true, "Zmiany zostały zapisane.");
            else $this->msg(false, "Wystąpił błąd! Zmiany nie zostały zapisane.");

            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist/type/msg", 0);
        } else if ($id) {
            $this->view->clientdata = $Manage->getclient($id);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist/type/msg", 0);
        }
    }

    public function showcustomerAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $this->view->clientid = $id = $this->_getParam("clientid");
        if ($id) {
            $Manage = new Managecustomer();
            $data = $Manage->getclient($id);
            $cars = $Manage->getcars($id);
            if ($data) {
                $this->view->data = $data;
                $this->view->cars = $cars;
            } else {
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
            }
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
        }
    }

    public function showcarAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("carid");
        if ($id) {
            $Manage = new Managecustomer();
            $data = $Manage->getcar($id);
            if ($data) {
                $this->view->data = $data;
            } else {
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
            }
        }
    }

    public function editcarAction() {
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("carid");
        $this->view->clientid = $clientid = $this->_getParam("clientid");
        $Manage = new Managecustomer();
        if ($this->_isPost() && $id) {
            $data = $this->_getPost('dane');
            if($Manage->updatecar($id, $data)) $this->msg(true,"Zmiany zostały zapisane");
            else $this->msg(false,"Zmiany nie zostały zapisane");

            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/showcustomer/clientid/$clientid/type/msg", 0);
        } else if ($id) {
            $this->view->car = $Manage->getcar($id);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
        }
    }

    public function delcarAction() {
        $id = $this->_getParam("carid");
        $clientid = $this->_getParam("clientid");
        if ($id) {
            $Manage = new Managecustomer();
            if ($Manage->delcar($id)) {
                $this->msg(true, "Samochód został usunięty.");
            } else {
                $this->msg(false, 'Wystąpił błąd! Samochód nie został usunięty');
            }
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/showcustomer/clientid/$clientid/type/msg", 0);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
        }
    }
}
