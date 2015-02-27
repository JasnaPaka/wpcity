<?php

include "JPDb.php";

abstract class JPController {
	
	const URL_VIEW   = "view";
	const URL_CREATE = "create";
	const URL_EDIT   = "edit";
	const URL_DELETE = "delete";
	const URL_LIST   = "list";
	
	protected $messages = array();	
	private $pagging;
	
	public function getMessages() {
		return $this->messages;	
	}
	
	public function getErrorMessages() {
		$messages = array();
		foreach($this->messages as $message) {
			if ($message instanceof JPErrorMessage) {
				array_push($messages, $message);	
			}
		}
		
		return $messages;			
	}
	
	public function getInfoMessages() {
		$messages = array();
		foreach($this->messages as $message) {
			if ($message instanceof JPInfoMessage) {
				array_push($messages, $message);	
			}
		}
		
		return $messages;			
	}	
	
	public function getList() {
		if ($this->getShowAll()) {
			return $this->db->getAll($this->getCurrentOrder());
		} else {
			return $this->db->getPage($this->getPageCurrent(), $this->getCurrentOrder());	
		}	
	}
	
	public function getCount() {
		return $this->db->getCount();	
	}
	
	/**
	 * Vrátí ID objektu kategorie, s kterým se pracuje (v případě úpravy a mazání) nebo null, pokud nebyl nalezen.
	 * Null je vrácen i tehdy, když podle ID nebyla nalezena žádná kategorie.
	 */
	public function getObjectId() {
		
		if (isset ($_GET["id"])) {
			$id =  (int) filter_input (INPUT_GET, "id", FILTER_SANITIZE_STRING);
		}
		else if (isset ($_POST["id"])) {
			$id =  (int) filter_input (INPUT_POST, "id", FILTER_SANITIZE_STRING);
		}
		
		if ($id == null) {
			return null;	
		}
		
		$row = $this->db->getById($id);
		if ($row == null) {
			return null;
		}
		
		return $id;
	}
	
	public function getObjectById($id) {
		$row = $this->db->getById($id);
		if ($row == null) {
			return null;
		}
		
		return $row;
	}
	
	/**
	 * Vrátí objekt z db na základě ID v URL nebo null, pokud nebylo ID či záznam nalezen.
	 */
	public function getObjectFromUrl() {
		$id = (int) filter_input (INPUT_GET, "id", FILTER_SANITIZE_STRING);
		if ($id == null) {
			return null;	
		}
		
		return $this->db->getById($id);
	}
	
	public function getShowAll() {
		if ($this->getSearchValueValid()) {
			return true;
		}
		
		return false;
	}
	
	public function getShowNavigation() {
		if ($this->getShowAll()) {
			return false;	
		}
		
		$count = $this->db->getCount();
		
		if ($count <= JPDb::MAX_ITEMS_ON_PAGE) {
			return false;	
		}
		
		return true;
	}
	
	public function getPageCurrent() {
		if (!isset($_GET["paged"])) {
			return 1;	
		}
		
		$page = (int) filter_input (INPUT_GET, "paged", FILTER_SANITIZE_STRING);
		if ($page < 1) {
			return 1;
		}
		if ($page > $this->getPageLast()) {
			return 1;
		}
		
		return $page;
	}
	
	public function getPageLast() {
		$pages = $this->getCount() / JPDb::MAX_ITEMS_ON_PAGE;
		return ceil($pages);
	}
	
	public function getPagePrevious() {
		$page = $this->getPageCurrent();	
		if ($page === 1) {
			return $page;	
		}
		
		$page--;
		return $page;
	}
	
	public function getPageNext() {
		$page = $this->getPageCurrent();	
		if ($page >= $this->getPageLast()) {
			return $page;
		}
		
		return $page+1;
	}
	
	/**
	 * Pracuje se nad hledaným řetězcem? Pokud ano, vrátíme jej nebo null.
	 */
	public function getSearchValue() {
		if (!isset($_POST["s"])) {
			return null;
		}
		
		$str = filter_input (INPUT_POST, "s", FILTER_SANITIZE_STRING);
		if ($str != null) {
			$str = trim($str);	
		}
		
		return $str;
	}
	
	/**
	 * Zadaný řetězec ve vyhledávání by měl být aspoň tři znaky, jinak nemá vyhledávání smysl.
	 */
	public function getSearchValueValid() {
		if ($this->getSearchValue() == null) {
			return false;
		}
		
		return strlen($this->getSearchValue()) >= 3;
	}
	
	/**
	 * Pracujeme nad již uloženým objektem?
	 */
	public function getIsEdit() {
		return $this->getObjectFromUrl() != null;
	}
		
	public function getUrl($action, $idObject = null) {
		$page = filter_input (INPUT_GET, "page", FILTER_SANITIZE_STRING);
		
		$url = "admin.php?page=".$page."&amp;action=".$action;
		if ($idObject != null) {
			$url = $url."&amp;id=".$idObject;
		} else if ($this->getObjectId() != null) {
			$url = $url."&amp;id=".$this->getObjectId();
		}
		
		return $url;
	}
	
	public function getOrders() {
		return array();	
	}
	
	public function getCurrentOrder() {
		return filter_input (INPUT_GET, "order", FILTER_SANITIZE_STRING);	
	}
	
	public function initDefaults() {
		return new stdClass();
	}
	
	abstract public function getStringId();
	
}

?>
	