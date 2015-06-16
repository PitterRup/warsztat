<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zarzadzanienaprawami
 *
 * @author Kacper
 */
class ZarzadzaniezadaniamiController extends AdminController {

    public function init() {
        
    }

    public function indexAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $Manage = new Managerepair();
        $this->view->date = $Manage->getweekarray();
        $this->view->mechanic = $Manage->countavailablemechanic();
        $this->view->places = $Manage->countavailableplace();
    }

    public function zadanialistAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $date = $this->_getParam('date');
        $Manage = new Managerepair();
        $this->view->repairs = $Manage->getrepairslist(null, $date);
    }

    public function addrepairAction() {
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        // .searchBox
        $this->_headScript($this->baseUrl . '/public/js/searchBox/searchBox.js');
        $this->_linkScript($this->baseUrl . '/public/js/searchBox/searchBox.css');

        $Manage = new Managerepair();
        $date = $this->_getParam('date');
        if ($date) {
            $this->view->placestable = $Manage->getavailableplaces($date);
            $this->view->mechanicstable = $Manage->getavailablemechanics($date);
            $this->view->date = $date;
        }
    }

    public function savedataAction() {
        if ($this->_isPost()) {
            $Manage = new Managerepair();
            $date = $this->_getParam('date');
            $mechanicid = $this->_getPost('mechanic');
            $placeid = $this->_getPost('place');
            $carid = $this->_getPost('carId');
            $info = $this->_getPost('info');
            $status = $this->_getPost('status');
            $price = $this->_getPost('price');

            if ($Manage->saverepair($carid, $mechanicid, $placeid, $date, $info, $status, $price)) {
                $this->msg(true, "Naprawa została zapisana.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniezadaniami/index/type/msg', 0);
            } else {
                $this->msg(false, "Naprawa nie została zapisana.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniezadaniami/index/type/msg', 0);
            }
        }
    }

    public function getdetailsAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $repairid = $this->_getParam('repairid');
        $Manage = new Managerepair();
        $this->view->repair = $Manage->getrepair(null, $repairid);

        $Managem = new Managemechanic;
        $this->view->mechanics = $Managem->getrepairmechanics($repairid);
    }

    public function findClientAction() {
        $string = $this->_getPost("searchString");
        $Manage = new Managecustomer;
        $rows = $Manage->find($string);
        $count = $Manage->getNumRows();
        echo $count > 0 ? $rows : '<center class="noResults">Nie znaleziono klienta.</center>';
    }

    public function getClientCarsAction() {
        $clientId = $this->_getPost('clientId');
        $Manage = new Managecustomer;
        $data = $Manage->getcars($clientId);

        foreach ($data as $row) {
            $rows .= '<li objId="' . $row['id'] . '">' . $row['Marka'] . ' ' . $row['Model'] . ' ' . $row['poj_sil'] . ' ' . $row['Rodz_sil'] . ' ' . $row['Rok_pr'] . '</li>';
        }

        $count = $Manage->getNumRows();
        echo $count > 0 ? $rows : '<center class="noResults">Brak samochodów dla tego klienta.</center>';
    }

    public function showrepairAction() {
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $Manage = new Managerepair();
        $this->view->date = $Manage->getweekarray();
        $this->view->repair = $Manage->countrepair();
    }

    public function editrepairAction() {
        $repairid = $this->view->repairid = $this->_getParam('repairid');
        $Manage = new Managerepair();
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
            $this->view->repair = $Manage->getrepair(null, $repairid);

            $date = $this->view->date = substr($this->view->repair['Data'], 0, 10);

            $availplaces = $Manage->getavailableplaces($date);
            $availmechanics = $Manage->getavailablemechanics($date);

            // aktualne stanowisko
            $Managep = new Manageplace;
            $curplace = array($Managep->getplace($this->view->repair['Stanowisko_ID']));
            // print_r($this->view->repair);die;
            // aktualni mechanicy
            $Managem = new Managemechanic;
            $curmechanics = $Managem->getrepairmechanics($repairid);
            $this->view->curmechanics = array();
            foreach ($curmechanics as $row)
                $this->view->curmechanics[] = $row['id'];

            $this->view->placestable = array_merge($availplaces, $curplace);
            $this->view->mechanicstable = array_merge($availmechanics, $curmechanics);
        } else {
            $postdata = $this->_getPost('dane');
            $date = $this->_getPost('date');
            $mechanic = is_array($this->_getPost('mechanic')) ? $this->_getPost('mechanic') : array();
            $oldmechanics = explode(",", $this->_getPost('oldmechanics'));
            $doUsuniecia = array_diff($oldmechanics, $mechanic);
            $doDodania = array_diff($mechanic, $oldmechanics);

            $Managem = new Managemechanic;
            // usuniecie mechaników z naprawy
            if (count($doUsuniecia) > 0)
                $Managem->deletefromrepair($repairid, $doUsuniecia);
            // dodanie mechaników do naprawy
            if (count($doDodania))
                $Managem->addtorepair($repairid, $doDodania);

            if (!$Manage->editrepair($postdata, $repairid))
                $this->msg(false, "Naprawa nie została zapisana.");
            else
                $this->msg(true, "Naprawa została zapisana");

            $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniezadaniami/zadanialist/date/' . $date . '/type/msg', 0);
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
                  $this->_request->goToAddress($this->directoryUrl . '/zarzadzaniezadaniami/index/type/msg', 0);
        } else {
                  $this->view->repairid = $repairid = $this->_getParam('repairid');
        }
  
    }

}
