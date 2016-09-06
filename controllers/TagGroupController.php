<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/TagDb.php";
include_once $ROOT."db/TagGroupDb.php";


class TagGroupController extends JPController
{
	protected $db;
	protected $dbTag;

	function __construct() {
		$this->db = new TagGroupDb();
		$this->dbTag = new TagDb();
	}

	public function getStringId() {
		return "tagGroup";
	}

	private function getFormValues() {
		$row = new stdClass();
		$row->nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->popis = filter_input (INPUT_POST, "popis", FILTER_SANITIZE_STRING);
		$row->barva = filter_input (INPUT_POST, "barva", FILTER_SANITIZE_STRING);
		$row->poradi = (int)filter_input(INPUT_POST, "poradi", FILTER_SANITIZE_STRING);
		if ($row->poradi == 0) {
			$row->poradi = null;
		}

		return $row;
	}

	private function validate($row) {

		// název
		if (strlen($row->nazev) < 3 || strlen($row->nazev) > 250) {
			array_push($this->messages, new JPErrorMessage("Název skupiny štítků musí mít min. 3 a nejvíce 250 znaků."));
		}

		return count($this->messages) === 0;
	}

	public function add() {
		$row = $this->getFormValues();

		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Novou skupinu štítků se nepodařilo uložit."));
			} else {
				array_push($this->messages, new JPInfoMessage('Skupina štítků byla úspěšně přidána. 
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
				array_push($this->messages, new JPErrorMessage("Skupinu štítků se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage('Skupina štítků byla úspěšně aktualizována. 
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
			array_push($this->messages,
				new JPErrorMessage("Skupina štítků nelze smazat. Pravděpodobně je přiřazena k některému štítku."));
			return $row;
		}

		$id = $this->getObjectId();
		if ($id == null) {
			return null;
		}

		$result = $this->db->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Skupinu štítků se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage("Skupina štítků byla úspěšně smazána."));
		}
	}

	public function getCanDelete() {
		$id = $this->getObjectId();
		if ($id == null) {
			return false;
		}

		return sizeof($this->dbTag->getTagWithTagGroup($id)) == 0;
	}

	public function getShowAll() {
		return true;
	}
}