<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/TagDb.php"; 
include_once $ROOT."db/Object2TagDb.php";

class TagController extends JPController {
	
	protected $db;
	protected $dbObject2Tag;
	
	function __construct() {
		$this->db = new TagDb();
		$this->dbObject2Tag = new Object2TagDb();
	}

	public function getStringId() {
		return "tag";	
	}
	
	public function getCountObjectsWithTag($idTag) {
		return $this->dbObject2Tag->getCountObjectsWithTag($idTag);	
	}	
	
	private function getFormValues() {
		$row = new stdClass();
		$row->nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->popis = filter_input (INPUT_POST, "popis", FILTER_SANITIZE_STRING);
		
		return $row;
	}
	
	private function validate($row) {
		
		// název
		if (strlen($row->nazev) < 3 || strlen($row->nazev) > 250) {
			array_push($this->messages, new JPErrorMessage("Název štítku musí mít min. 3 a nejvíce 250 znaků."));
		}

		return count($this->messages) === 0; 
	}	
	
	
	public function add() {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nový štítek."));
			} else {
				$idObject = $this->db->getLastId();
				array_push($this->messages, new JPInfoMessage('Štítek byla úspěšně přidán. 
					<a href="'.$this->getUrl(JPController::URL_LIST).'">Zobrazit seznam</a>'));
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
				array_push($this->messages, new JPErrorMessage("Štítek se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage('Štítek byl úspěšně aktualizován. 
					<a href="'.$this->getUrl(JPController::URL_LIST).'">Zobrazit seznam</a>'));
			}
		}
		
		return $row;
	}	
	
	
	public function delete() {
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}
		
		if (!$this->getCanDelete()) {
			array_push($this->messages, new JPErrorMessage("Štítek nelze smazat. Pravděpodobně je přiřazen k některému objektu."));
			return $row;
		}
		
		$id = $this->getObjectId();
		if ($id == null) {
			return null;
		}
		
		$result = $this->db->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Štítek se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage("Štítek byl úspěšně smazán."));
		}
	}
	
	
	public function getCanDelete() {
		$id = $this->getObjectId();
		if ($id == null) {
			return false;
		}
		
		return $this->dbObject2Tag->getCountObjectsWithTag($id) == 0;
	}

	public function getShowAll() {
		return true;
	}

}
