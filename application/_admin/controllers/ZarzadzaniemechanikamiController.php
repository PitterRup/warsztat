<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zarzadzaniemechanikami
 *
 * @author Kacper
 */
class ZarzadzaniemechanikamiController extends AdminController {

    public function init() {
        
    }

    public function newmechanicAction() {
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        }
        // po wysłaniu formularza metodą POST
        else {
            $postdata = $this->_getPost('dane');
            $Manage = new Managemechanic();
            if ($Manage->addMechanic($postdata)) {
                $this->msg(true, "Mechanik został zapisany");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniemechanikami/mechaniclist/type/msg', 0);
            } else {
                $this->msg(false, "Mechanikt nie został zapisany.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniemechanikami/newmechanic/type/msg', 0);
            }
        }
    }

    public function mechaniclistAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');

        $Manage = new Managemechanic();
        $this->view->mechanics = $Manage->getmechaniclist();
    }

    public function showmechanicAction() {
        $id = $this->_getParam("mechanicid");
        if ($id) {
            $Manage = new Managemechanic();
            $data = $Manage->getmechanic($id);
            if ($data) {
                $this->view->mechanic = $data;
            } else {
                // $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniemechanikami/mechaniclist", 0);
            }
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniemechanikami/mechaniclist", 0);
        }
    }

    public function editmechanicAction() {
        $id = $this->_getParam("mechanicid");
        $Manage = new Managemechanic();
        if ($this->_isPost() && $id) {
            $data = $this->_getPost('dane');
            if($Manage->updatemechanic($id, $data)){
                 $this->msg(false, 'Wystąpił błąd dane mechanika nie zostały zapisane');
            }
            else {
                 $this->msg(true, 'Mechanik zapisany');
            }
           // $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniemechanikami/mechaniclist/type/msg", 0);
        } else if ($id) {
            $this->view->mechanic = $Manage->getmechanic($id);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniemechanikami/mechaniclist", 0);
        }
    }

    public function delmechanicAction() {
        
        $id = $this->_getParam("mechanicid");
        $Manage= new Managemechanic();
        if($Manage->delmechanic($id)){
             $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniemechanikami/mechaniclist", 0);
        }
        else{
            $this->msg(false, 'Wystąpił błąd mechanik nie został usunięty');
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzaniemechanikami/mechaniclist/type/msg", 0);
        }
    }

}
