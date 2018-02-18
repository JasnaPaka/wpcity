<?php
$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/CollectionDb.php";
include_once $ROOT."db/Object2CollectionDb.php";
include_once $ROOT."db/SettingDb.php";
include_once $ROOT."db/SourceDb.php";

include_once $ROOT."controllers/SettingController.php";

include_once $ROOT."utils/SourceType.php";
include_once $ROOT."utils/SourceTypes.php";

/**
 * Správa souborů děl
 */
class CollectionController extends JPController {

	protected $db;
	protected $dbObject2Collection;
 	protected $dbSetting;
 	protected $dbSource;

	
	function __construct() {
		$this->db = new CollectionDb();
		$this->dbObject2Collection = new Object2CollectionDb();
		$this->dbSetting = new SettingDb();
		$this->dbSource = new SourceDb();
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
        
    private function getGoogleMapSettings() {
         $setting["gm_key"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_KEY)->hodnota;
         $setting["gm_lat"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_LAT)->hodnota;
         $setting["gm_lng"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_LON)->hodnota;
         $setting["gm_zoom"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_ZOOM)->hodnota;
         
         return $setting;
    }
	
	public function getGoogleMapPointContent($lat, $lng) {
            $map = new GoogleMapsBuilder($this->getGoogleMapSettings(), $lat, $lng);
            return $map->getOutput();
	}	
	
	public function getGoogleMapPointEditContent($lat, $lng) {
            $map = new GoogleMapsBuilder($this->getGoogleMapSettings(), $lat, $lng);
            return $map->getOutputEdit();
	}
	
	
	private function getFormValues() {
		$row = new stdClass();
		
		$row->nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->latitude = (double) filter_input (INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		if ($row->latitude == 0) {
			$row->latitude = null;
		}

		$row->longitude = (double) filter_input (INPUT_POST, "longitude", FILTER_SANITIZE_STRING);
		if ($row->longitude == 0) {
			$row->longitude = null;
		}

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
		if (strlen($row->latitude) > 0 && !GPSUtils::getIsValidLatitude($row->latitude)) {
			array_push($this->messages, new JPErrorMessage("Neplatná latitude u GPS souřadnice."));
		}
		
		// longitude
		if (strlen($row->longitude) > 0 && !GPSUtils::getIsValidLongitude($row->longitude)) {
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

	public function getCoordinatesExists() {
		$obj = $this->getObjectFromUrl();
		return (strlen($obj->latitude) != null && strlen($obj->longitude) != null);
	}

	private function getFormSourcesValues() {
		$sources = array ();

		foreach($_POST as $key => $value) {
			$pos = strpos($key, "zdroj");

			if ($pos === 0) {

				$source = new stdClass();
				$id = (int) filter_input (INPUT_POST, $key, FILTER_SANITIZE_STRING);
				if ($id > 0) {
					$source->id = $id;
				}

				$source->typ = filter_input(INPUT_POST, "typ" . $value, FILTER_SANITIZE_STRING);
				$source->identifikator = filter_input(INPUT_POST, "identifikator" . $value, FILTER_SANITIZE_STRING);

				$source->nazev = filter_input (INPUT_POST, "nazev".$value, FILTER_SANITIZE_STRING);
				$source->url = filter_input (INPUT_POST, "url".$value, FILTER_SANITIZE_STRING);

				$source->cerpano = filter_input (INPUT_POST, "cerpano".$value, FILTER_SANITIZE_STRING);
				$source->cerpano = ($source->cerpano === "on" ? 1 : 0);

				$source->deleted = filter_input (INPUT_POST, "deleted".$value, FILTER_SANITIZE_STRING);
				$source->deleted = ($source->deleted === "on" ? 1 : 0);
				$source->autor = null;
				$source->objekt = null;
				$source->soubor = $this->getObjectId();

				array_push($sources, $source);
			}
		}

		return $sources;
	}

	private function validateSources($sources) {

		foreach ($sources as $source) {
			if (!isset($source->id) && strlen($source->nazev) == 0 && strlen($source->url) > 0) {
				array_push($this->messages, new JPErrorMessage("Každý zdroj, který má zadáno URL, musí mít i název."));
			}
		}

		return count($this->messages) === 0;
	}

	public function manageSources() {
		$sources = $this->getFormSourcesValues();

		if (count($sources) == 0) {
			return $this->getSelectedSources();
		}

		$result = $this->validateSources($sources);
		if ($result) {
			foreach ($sources as $source) {
				if (strlen($source->typ) == 0 && strlen($source->nazev) == 0) {
					continue;
				}

				if (isset($source->id)) {
					$result = $this->dbSource->updateWithObject($source, $source->id, false);
				} else {
					$result = $this->dbSource->createWithObject($source, false);
				}
			}

			$wb = new WikidataBuilder($this->dbSource, $sources);
			if (!$wb->process()) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se zaktualizovat zdroje z Wikidat."));
			}

			array_push($this->messages, new JPInfoMessage('Zdroje byly aktualizovány. 
				<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));

			return $this->getSelectedSources();
		}

		return $sources;

	}

	public function getSourcesForCollection() {
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->dbSource->getSourcesForCollection($this->getObjectId());
	}

	public function getSelectedSources() {
		$sources = array ();
		foreach($this->getSourcesForCollection() as $source) {
			array_push($sources, $source);
		}

		// doplníme pět dalších
		for ($i = 1; $i <= 5; $i++) {
			array_push($sources, 0);
		}

		return $sources;
	}

	public function getSystemSourcesForCollection() {
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->dbSource->getSourcesForCollection($this->getObjectId(), true);
	}

	public function getAllSourceTypes() {
		return SourceTypes::getInstance()->getNonSystemValues();
	}

	public function getObjectId()
	{
		global $wp_query;

		if (isset($wp_query->query_vars['soubor'])) {
			$id = (int)$wp_query->query_vars['soubor'];
			if ($id == null) {
				return parent::getObjectId();
			}
		} else {
			return parent::getObjectId();
		}

		return $id;
	}
}