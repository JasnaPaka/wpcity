<?php

include_once "category-db.php";
include_once "objects-db.php";
include_once "jp\JPMessages.php"; 

class CategoryController {

	private $db;
	private $dbObjects;
	private $messages = array();
	
	function __construct() {
		$this->db = new CategoryDb();
		$this->dbObjects = new ObjectsDb();
	}

	public function getList() {
		return $this->db->getAll();	
	}
	
	private function validate($row) {
		
		// název
		if (strlen($row->nazev) < 3 || strlen($row->nazev) > 250) {
			array_push($this->messages, new JPErrorMessage("Název kategorie musí mít min. 3 a nejvíce 250 znaků."));
		}
		
		// kategorie
		if (strlen($row->url) < 3 || strlen($row->url) > 250) {
			array_push($this->messages, new JPErrorMessage("URL kategorie musí mít min. 3 a nejvíce 250 znaků."));
		} else if ($this->db->getByUrl($row->url) != null && 
			($this->getObjectFromUrl() == null || $this->getObjectFromUrl()->id !== $this->db->getByUrl($row->url)->id)) {
			array_push($this->messages, new JPErrorMessage("URL kategorie již existuje."));
		} else if (!preg_match("/^[a-z\d-]+$/", $row->url)) {
			array_push($this->messages, new JPErrorMessage("URL smí obsahovat pouze znaky abecedy (malé), čísla a pomlčku."));
		}
		
		// ikona
		if (!preg_match("%^((http?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i", $row->ikona)) {
			array_push($this->messages, new JPErrorMessage("URL ikony není platnou adresou."));
		}
		
		return count($this->messages) === 0; 
	}
	
	public function add() {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit novou kategorii."));
			} else {
				array_push($this->messages, new JPInfoMessage("Kategorie byla úspěšně přidána."));
				return new stdClass();
			}
		}
		
		return $row;
	}

	public function update() {
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}
		
		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->update($row, $this->getObjectFromUrl()->id);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Kategorii se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage("Kategorie byla úspěšně aktualizována."));
			}
		}
		
		return $row;
	}
	
	public function getCanDelete() {
		$id = $this->getObjectId();
		if ($id == null) {
			return null;	
		}
		
		$row = $this->db->getById($id);
		if ($row == null) {
			return null;
		}
		
		return $this->dbObjects->getCountObjectsInCategory($id) == 0;
	}
	
	public function delete() {
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}
		
		if (!$this->getCanDelete()) {
			array_push($this->messages, new JPErrorMessage("Kategorii nelze smazat. Pravděpodobně není prázdná."));
			return $row;
		}
		
		$id = $this->getObjectId();
		if ($id == null) {
			return null;
		}
		
		$result = $this->db->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Kategorii se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage("Kategorie byla úspěšně smazána."));
		}
	}
	
	public function getObjectId() {
		if (isset ($_GET["id"])) {
			return (int) filter_input (INPUT_GET, "id", FILTER_SANITIZE_STRING);
		}
		if (isset ($_POST["id"])) {
			return (int) filter_input (INPUT_POST, "id", FILTER_SANITIZE_STRING);
		}
		
		return null;
	}
	
	private function getFormValues() {
		$row = new stdClass();
		$row->nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->url = filter_input (INPUT_POST, "url", FILTER_SANITIZE_STRING);
		$row->ikona = filter_input (INPUT_POST, "ikona", FILTER_SANITIZE_STRING);
		
		return $row;
	}

	public function getObjectFromUrl() {
		$id = (int) filter_input (INPUT_GET, "id", FILTER_SANITIZE_STRING);
		if ($id == null) {
			return null;	
		}
		
		return $this->db->getById($id);
	}
	
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
}

?>