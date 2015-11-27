<?php
$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/CollectionDb.php";
include_once $ROOT."db/Object2CollectionDb.php";

/**
 * Správa souborů děl
 */
class CollectionController extends JPController {

	protected $db;
	protected $dbObject2Collection;
	
	function __construct() {
		$this->db = new CollectionDb();
		$this->dbObject2Collection = new Object2CollectionDb();
	}
	
	public function getStringId() {
		return "collection";	
	}
	
	public function getCountObjectsInCollection($idCollection) {
		return $this->dbObject2Collection->getCountObjectsInCollection($idCollection);	
	}	
		
	public function getObjectsInCollection($idCollection) {
		return $this->dbObject2Collection->getObjectsInCollection($idCollection);
	}
	
	public function getGoogleMapPointContent($lat, $lng) {
            global $KV_SETTINGS;
            
            $map = new GoogleMapsBuilder($KV_SETTINGS, $lat, $lng);
            return $map->getOutput();
	}	
	
	public function getGoogleMapPointEditContent($lat, $lng) {
            global $KV_SETTINGS;
            
            $map = new GoogleMapsBuilder($KV_SETTINGS, $lat, $lng);
            return $map->getOutputEdit();
	}
	
	
	private function getFormValues() {
		$row = new stdClass();
		
		$row->nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->latitude = (double) filter_input (INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		$row->longitude = (double) filter_input (INPUT_POST, "longitude", FILTER_SANITIZE_STRING);
		$row->popis = filter_input (INPUT_POST, "popis", FILTER_SANITIZE_STRING);
		$row->obsah = $_POST["editor"]; // TODO: sanitize 
		$row->interni = $_POST["interni"]; // TODO: sanitize

		$row->zruseno = filter_input (INPUT_POST, "zruseno", FILTER_SANITIZE_STRING);
		$row->zruseno = ($row->zruseno === "on" ? 1 : 0);

		$row->zpracovano = filter_input (INPUT_POST, "zpracovano", FILTER_SANITIZE_STRING);
		$row->zpracovano = ($row->zpracovano === "on" ? 1 : 0);
		
		return $row;
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
		
		return count($this->messages) === 0; 
	}	
	
	private function setAuthors($row) {
		global $current_user;
		
		$dt = new DateTime();
		$dtStr = $dt->format('Y-m-d H:i:s'); 
		get_currentuserinfo();
		
		if (!$this->getIsEdit()) {
			$row->pridal_autor = $current_user->display_name;
			$row->pridal_datum = $dtStr;
		}
		
		$row->upravil_autor = $current_user->display_name;
		$row->upravil_datum = $dtStr;
		
		return $row;
	}	
	
	
	public function add() {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$row = $this->setAuthors($row);
			
			$result = $this->db->create($row);

			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nový soubor děl."));
			} else {
				$idObject = $this->db->getLastId();
				
				array_push($this->messages, new JPInfoMessage('Soubor děl byl úspěšně přidán. 
					<a href="'.$this->getUrl(JPController::URL_VIEW, $idObject).'">Zobrazit detail</a>'));
				
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
			$row = $this->setAuthors($row);
			$result = $this->db->update($row, $this->getObjectFromUrl()->id);
			
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Soubor děl se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage('Soubor děl byl úspěšně aktualizován. 
				<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));
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
			array_push($this->messages, new JPErrorMessage("Soubor děl nelze smazat. Pravděpodobně jsou k němu přiřazeny některé objekty."));
			return $row;
		}
		
		$id = $this->getObjectId();
		if ($id == null) {
			return null;
		}
		
		$result = $this->db->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Soubor děl se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage("Soubor děl byl úspěšně smazán."));
		}
	}	
	
	
	public function getCanDelete() {
		$id = $this->getObjectId();
		if ($id == null) {
			return false;
		}
		
		return $this->dbObject2Collection->getCountObjectsInCollection($id) == 0;
	}	
	
	public function getList() {
		return $this->db->getAll();	
	}
	
	
	public function getCollectionId() {
		global $wp_query;
		
		$id = (int) $wp_query->query_vars['soubor'];
		if ($id == null) {
			return parent::getObjectId();
		}
		
		return $id;
	}
	
	
	public function getImgForCollection($idCollection) {
		return $this->dbObject2Collection->getImgForCollection($idCollection);
	}
		
}

?>
	