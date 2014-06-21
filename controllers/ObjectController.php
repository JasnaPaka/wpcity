<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";
include_once $ROOT."fw/GPSUtils.php";

include_once $ROOT."db/CategoryDb.php";
include_once $ROOT."db/ObjectDb.php"; 

class ObjectController extends JPController {
	
	protected $db;
	private $dbCategory;
	
	private $categories;
	
	function __construct() {
		$this->db = new ObjectDb();
		$this->dbCategory = new CategoryDb();
	}
	
	public function getCategoryNameForObject($id) {
		if ($categories == null) {
			$categories = $this->dbCategory->getAll();	
		}
		
		foreach ($categories as $category) { 
			if ($category->id === $id) {
				return $category->nazev;	
			}
		}
		
		return "Neznámá";
	}
	
	public function getAllCategories() {
		return $this->dbCategory->getAll();	
	}
	
	private function validate($row) {
		
		// název
		if (strlen($row->nazev) < 3 || strlen($row->nazev) > 250) {
			array_push($this->messages, new JPErrorMessage("Název objektu musí mít min. 3 a nejvíce 250 znaků."));
		}

		// latitude
		if (!GPSUtils::getIsValidLatitude($row->latitude)) {
			array_push($this->messages, new JPErrorMessage("Neplatná latitude u GPS souřadnice."));
		}
		
		// longitude
		if (!GPSUtils::getIsValidLongitude($row->longitude)) {
			array_push($this->messages, new JPErrorMessage("Neplatná longitude u GPS souřadnice."));
		}
		
		// kategorie
		if (!$this->dbCategory->getById($row->kategorie)) {
			array_push($this->messages, new JPErrorMessage("Nebyla zvolena kategorie."));
		}
		
		return count($this->messages) === 0; 
	}
	
	
	public function add() {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nový objekt."));
			} else {
				array_push($this->messages, new JPInfoMessage("Objekt byl úspěšně přidán."));
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
				array_push($this->messages, new JPErrorMessage("Objekt se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage("Objekt byl úspěšně aktualizován."));
			}
		}
		
		return $row;
	}
	
	public function delete() {
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}
		
		$id = $this->getObjectId();
		if ($id == null) {
			return null;
		}
		
		$result = $this->db->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Objekt se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage("Objekt byl úspěšně smazán."));
		}
	}
	
	private function getFormValues() {
		$row = new stdClass();
		$row->nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->latitude = (double) filter_input (INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		$row->longitude = (double) filter_input (INPUT_POST, "longitude", FILTER_SANITIZE_STRING);
		$row->kategorie = (int) filter_input (INPUT_POST, "kategorie", FILTER_SANITIZE_STRING);
		
		return $row;
	}
}

?>