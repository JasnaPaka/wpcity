<?php

class JPController {
	
	protected $messages = array();	
	
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
		return $this->db->getAll();	
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
	
}

?>
	