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
        // jest wykonywana gdy w adresie nie podamy żadnej akcji, a metoda indexAction (akcja index) nie istnieje
        $this->defaultAction = 'customerlist';
    }
//metoda ospowiada za dodawanie do bazy nowego klienta
    public function newcustomerAction() {
        if (!$this->_isPost()) {
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        }
        // po wysłaniu formularza metodą POST
        else {
            $postdata = $this->_getPost('dane');
           // $permissions = '{"_page":1}';
           // $postdata[] = $permissions;
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
//metoda dodaje nowy samochód
    public function newcarAction() {
        if (!$this->_isPost()) {
            // dołączenie do szablonu strony plików stylu i js odpowiedzialnego za formularz
            $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
            $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
            // przesłanie do widoku zmiennej clientid która przyda się przy dodaniu samochodu do bazy danych
            $this->view->clientid = $this->_getParam('clientid');
        } else {
            // pobranie danych wysłanych metodą POST
            $postdata = $this->_getPost('dane');
            // pobranie z paska adresu parametru clientid (wcześniej podaliśmy go do widoku i dołączyliśmy do linku formularza)
            // w adresie wygląda to mniej więcej tak www.adresstrony.pl/panel/kontroler/akcja/parametr1/wartość1/parametr2/wartość2
            // czyli nasz adres wygląda warsztat.pitterrup.unixstorm.org/panel/zarzadzanieklientem/newcar/clientid/np.1
            $param = $this->_getParam("clientid");
            // dołączenie $clientid do danych POST
            $postdata['clientid'] = $param;

            $Manage = new Managecustomer();
            // dodanie samochodu (w argumencie przesyłamy dane z formularza czyli dane POST)
            if (!$Manage->addCar($postdata)) {
                $this->msg(false, "Samochód nie został dodany.");
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/newcar/clientid/$param/type/msg", 0);
            } else {
                $this->msg(true, "Samochód został zapisany.");
                $this->_request->goToAddress($this->directoryUrl . '/zarzadzanieklientem/customerlist/type/msg', 0);
            }
        }
    }
//metoda wyświetla listę klientów
    public function customerlistAction() {
        // dołączenie do szablonu pliku stylu odpowiedzialnego za tabele
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        // $this->baseUrl jest to zmienna frameworka która przechowuje podstawowy adres, czyli jeśli teraz jesteś w warsztat.pitterrup.unixstorm.org/panel/zarzadzanieklientem/customerlist
        // to $this->baseUrl przechowuje "warsztat.pitterrup.unixstorm.org" 
        // możesz gdzieś jeszcze spotkać $this->directoryUrl. ona przechowuje adres: "warsztat.pitterrup.unixstorm.org/panel"

        $Manage = new Managecustomer();
        // przesłanie do widoku klientów pobranych z bazy danych
        // te dane mają formę tablicy czyli coś takiego:
        // Array(0=>Array("clientID"=>1,"Nazw"=>tomasz kmiecik itd), 2=>Array(dane drugiego klienta), 3=>Array(dane trzeciego) itd);
        // w widoku odwołujesz się do nich w ten sposób 
        // echo $this->customers[0]['clientid']; <- to nam wyświetli "1".
        // celowo nie dałem $this->view->customers ponieważ jesteś w widoku. 
        // ten kontroler jest obok widoku więc musisz tablice danych po prostu przesłać do widoku
        $this->view->customers = $Manage->getcustomerlist();
    }
//usuwa klienta
    public function delcustomerAction() {
        // pobranie z paska adresu id klienta
        $id = $this->_getParam("clientid");
        if ($id) {
            $Manage = new Managecustomer();
            // usunięcie klienta po id
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
//metoda edytuje dane klienta
    public function editcustomerAction() {
        // dołączenie plików do formularza
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        // pobranie z paska adresu id klienta
        $id = $this->_getParam("clientid");
        $Manage = new Managecustomer();
        // sprawdzenie czy próbujemy dostać się do tej akcji po wysłaniu formularza metodą POST czy zwyczajnie w adresie wpisaliśmy
        // warsztat.pitterrup.unixstorm.org/panel/zarzadzaniekliente/editcustomer/clientid/np.2
        // $this->_isPost() zwraca "true" jeśli wysłaliśmy formularz metodą POST
        if ($this->_isPost() && $id) {
            // pobranie danych z formularza
            $data = $this->_getPost('dane');
            // edycja danych
            if ($Manage->updatecustomer($id, $data)) {
                $this->msg(true, "Zmiany zostały zapisane.");
            } else {
                $this->msg(false, "Wystąpił błąd! Zmiany nie zostały zapisane.");
            }
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist/type/msg", 0);
        } else if ($id) {
            // jeśli nie wysyłaliśmy formularza to wyświetlamy formularz więc pobieramy dane o kliencie z bazy danych
            // i przesyłamy je do widoku
            // tym razem dane są w postaci tablicy jednowymiarowej, czyli Array("clientid"=>1, "Nazw"=>"kmiszek piotr" itd);
            // więc odwołujemy się do nich w widoku poprzez $this->clientdata['clientid'];
            $this->view->clientdata = $Manage->getclient($id);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist/type/msg", 0);
        }
    }

    // metoda pokazująca dane o kliencie
    public function showcustomerAction() {
        // dołaczenie plików stylu dla tabeli i formularza
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/table&list.css');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        // przsłanie do widoku id klienta pobranego z adresu
        $this->view->clientid = $id = $this->_getParam("clientid");
        if ($id) {
            $Manage = new Managecustomer();
            // pobranie klienta z bazy danych
            $data = $Manage->getclient($id);
            // pobranie samochodów klienta po id klienta
            $cars = $Manage->getcars($id);
            if ($data) {
                // ???????? :)
                $this->view->data = $data;
                $this->view->cars = $cars;
            } else {
                $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
            }
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
        }
    }
//metoda pokazuje szczegółowe dane samochodu
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
//edycja danych samochodu
    public function editcarAction() {
        $this->_headScript($this->baseUrl . '/public/js/_admin/form.js');
        $this->_linkScript($this->baseUrl . '/public/template/styles/_admin/form.css');
        $id = $this->_getParam("carid");
        $this->view->clientid = $clientid = $this->_getParam("clientid");
        $Manage = new Managecustomer();
        if ($this->_isPost() && $id) {
            $data = $this->_getPost('dane');
            if ($Manage->updatecar($id, $data)) {
                $this->msg(true, "Zmiany zostały zapisane");
            } else {
                $this->msg(false, "Zmiany nie zostały zapisane");
            }
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/showcustomer/clientid/$clientid/type/msg", 0);
        } else if ($id) {
            $this->view->car = $Manage->getcar($id);
        } else {
            $this->_request->goToAddress($this->directoryUrl . "/zarzadzanieklientem/customerlist", 0);
        }
    }
//usuwanie samochodu z bazy danych
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
