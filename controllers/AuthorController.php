<?php
$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/AuthorDb.php"; 

/**
 * Správa autorů
 */
class AuthorController extends JPController {
		
	protected $db;
	
	function __construct() {
		$this->db = new AuthorDb();
	}
	
	private function validate($row) {
		
		// jméno
		if (strlen($row->jmeno) < 3 || strlen($row->jmeno) > 250) {
			array_push($this->messages, new JPErrorMessage("Jméno autora musí mít min. 3 a nejvíce 250 znaků."));
		}
	
		return count($this->messages) === 0; 	
	}
	
	public function add() {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nového autora."));
			} else {
				array_push($this->messages, new JPInfoMessage("Autor byl úspěšně přidán."));
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
				array_push($this->messages, new JPErrorMessage("Autora se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage("Autor byl úspěšně aktualizován."));
			}
		}
		
		return $row;
	}
	
	public function getCanDelete() {
		$id = $this->getObjectId();
		if ($id == null) {
			return false;	
		}
		
		return true;
	}
	
	public function delete() {
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}
		
		if (!$this->getCanDelete()) {
			array_push($this->messages, new JPErrorMessage("Autora nelze smazat. Pravděpodobně je přiřazen k některému objektu."));
			return $row;
		}
		
		$id = $this->getObjectId();
		if ($id == null) {
			return null;
		}
		
		$result = $this->db->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Autora se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage("Autor byl úspěšně smazán."));
		}
	}
	
	private function getFormValues() {
		$row = new stdClass();
		$row->jmeno = filter_input (INPUT_POST, "jmeno", FILTER_SANITIZE_STRING);
		
		return $row;
	}
	
	
}

?>