<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mechanik
 *
 * @author Piotrek
 */
class MechanikController extends AdminController {

    public function init() {
        
    }

    public function zadanialistAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');

        $Manage = new Managerepair();
        $this->view->repairs = $Manage->getrepairslist($this->sesField['id']);
    }

    public function showrepairAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $repairid = $this->_getParam('repairid');
        $Manage = new Managerepair();
        $this->view->repair = $Manage->getrepair($this->sesField['id'], $repairid);
    }

    public function editrepairAction() {
        $repairid = $this->view->repairid = $this->_getParam('repairid');
        $Manage = new Managerepair();
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
            $this->view->repair = $Manage->getrepair($this->sesField['id'], $repairid);
        } else {
            $postdata = $this->_getPost('dane');

            if (!$Manage->editrepair($postdata, $repairid))
                $this->msg(false, "Naprawa nie została zapisana.");
            else
                $this->msg(true, "Naprawa została zapisana");

            $this->_request->goToAddress($this->directoryUrl . '/mechanik/zadanialist/type/msg', 0);
        }
    }

    public function addpartsAction() {
        if ($this->_isPost()) {
            $repairid = $this->_getParam('repairid');
            $data = $this->_getPost('data');
            $Manage = new Managerepair();
            if ($Manage->orderparts($repairid, $data)) {
                $this->msg(true, "Części zostały zamówione.");
            } else {
                $this->msg(false, "Wystąpił błąd.");
            }
                  $this->_request->goToAddress($this->directoryUrl . '/mechanik/zadanialist/type/msg', 0);
        } else {
                  $this->view->repairid = $repairid = $this->_getParam('repairid');
        }
  
    }

    public function delparts($partid) {
        if ($this->_isPost()) {
            
        } else {
            $this->_request->goToAddress($this->directoryUrl . '/mechanik/zadanialist/type/msg', 0);
        }
    }

}
