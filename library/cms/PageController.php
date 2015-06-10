<?php
Debug::checkPoint("Pobranie pliku GeneralController",2);
require_once PATH."library/cms/GeneralController.php";
// generalny kontroler kontrolerów strony
abstract class PageController extends GeneralController {
	public function commonElements() {
		// ustawienie layoutu
        $this->_layout->set("authorized");

        //////// BUTTONY ///////////
        $this->view->buttonDelete = '<img src="'.$this->baseUrl.'/public/template/img/admin/delete.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/delete_hover.png" class="hover"/>';
        $this->view->buttonMove = '<img src="'.$this->baseUrl.'/public/template/img/admin/move.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/move_hover.png" class="hover"/>';
        $this->view->buttonEdit = '<img src="'.$this->baseUrl.'/public/template/img/admin/edit.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/edit_hover.png" class="hover"/>';
        $this->view->buttonVisible = '<img src="'.$this->baseUrl.'/public/template/img/admin/visible.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/visible_hover.png" class="hover"/>';
        
        $this->view->buttonListDelete = '<img src="'.$this->baseUrl.'/public/template/img/admin/remove-blog-active-icon.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/remove-blog-hover-icon.png" class="hover"/>';
        $this->view->buttonListEdit = '<img src="'.$this->baseUrl.'/public/template/img/admin/edit-blog-active-icon.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/edit-blog-hover-icon.png" class="hover"/>';
        $this->view->buttonListVisible = '<img src="'.$this->baseUrl.'/public/template/img/admin/visible-blog-active-icon.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/visible-blog-hover-icon.png" class="hover"/>';
        $this->view->buttonListAdd = '<img src="'.$this->baseUrl.'/public/template/img/admin/list-add-icon.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/list-add-active-icon.png" class="hover"/>';
        $this->view->buttonSetMini = '<img src="'.$this->baseUrl.'/public/template/img/admin/setMini-icon.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/setMini-active-icon.png" class="hover"/>';
        $this->view->buttonListDownload = '<img src="'.$this->baseUrl.'/public/template/img/admin/downloadAlbum.png" class="active"/><img src="'.$this->baseUrl.'/public/template/img/admin/downloadAlbum_hover.png" class="hover"/>';
	
        $this->sesField = $this->view->sesField = $_SESSION[$this->_config->dbTableNames->Klient];
    }

	// metoda ustawiająca komunikat (do przeniesienia najlepiej zrobic z tego klase MSG z getterem i setterem)
	protected function msg($state,$text) {
        $state = $state ? 'true':'false';
        $_SESSION[$this->_config->dbTableNames->Klient]['msg'] = array("text"=>$text,"state"=>$state);
    }
}
?>