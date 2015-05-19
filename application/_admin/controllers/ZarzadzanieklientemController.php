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
		if(!$this->_isPost()) {
			$this->_headScript($this->baseUrl.'/public/js/_admin/form.js');
			$this->_linkScript($this->baseUrl.'/public/template/styles/_admin/form.css');
		}
		// po wysłaniu formularza metodą POST
		else {
			$postdata = $this->_getPost('dane');
			$id=0;
			$Manage = new Managecustomer();
			if (!$Manage->addCustomer($postdata,$id)) {
				$this->msg(false,"Klient nie został dodany.");
				$this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/newcustomer/type/msg', 0);
			}
			else {
				$this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/newcar/clientid/'.$id.'/type/msg', 0);
			}
		}
	}

	public function newcarAction() {
		if(!$this->_isPost()) {
			$this->_headScript($this->baseUrl.'/public/js/_admin/form.js');
			$this->_linkScript($this->baseUrl.'/public/template/styles/_admin/form.css');
			$this->view->clientid = $this->_getParam('clientid');
		}
		else {
			$postdata = $this->_getPost('dane');
			$param = $this->_getParam("clientid");
			$postdata['clientid'] = $param;

			$Manage = new Managecustomer();
			if (!$Manage->addCar($postdata)) {
				$this->msg(false,"Samochód nie został dodany.");
				$this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/newcar/clientid/$param/msg', 0);
			}
			else {
				$this->msg(true,"Klient został zapisany.");
				$this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/customerlist/type/msg', 0);
			}
		}
	}

	public function customerlistAction() {
		$this->_linkScript($this->baseUrl.'/public/template/styles/_admin/table&list.css');

		$Manage = new Managecustomer();
		$this->view->customers = $Manage->getcustomerlist();
	}

	public function editcustomerAction() {
		
	}

	public function delcustomerAction() {
		if ($this->_getParam("clientid")) {
			$Manage = new Managecustomer();
		   if(!$Manage->delcustomer($this->_getParam("clientid"))){
			   echo 'error ';
		   }
		} else {
			echo 'error';
		}
	}

}
