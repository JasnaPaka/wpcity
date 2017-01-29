<?php
$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/AuthorDb.php";
include_once $ROOT."db/ObjectDb.php";
include_once $ROOT."db/SourceDb.php";
include_once $ROOT."db/CategoryDb.php";

/**
 * Správa autorů
 */
class AuthorController extends JPController {
		
	protected $db;
	protected $dbObject;
        protected $dbCategory;
        private $dbObject2Tag;
	private $dbSource;

	function __construct()
	{
		$this->db = new AuthorDb();
		$this->dbObject = new ObjectDb();
		$this->dbSource = new SourceDb();
		$this->dbCategory = new CategoryDb();
		$this->dbObject2Tag = new Object2TagDb();
	}
	
	public function getList() {
		if (!$this->getSearchValueValid()) {
			if ($this->getSearchValue() != null) {
				array_push($this->messages, new JPErrorMessage("Hledaný výraz musí mít alespoň tři znaky."));
			}
			
			$rows = parent::getList();	
		} else {
			if ($this->getShowAll()) {
				$rows = $this->db->getListByNazev($this->getSearchValue());
			} else {
				$rows = $this->db->getPageByNazev($this->getPageCurrent(), $this->getSearchValue());	
			}
		
			$rows = $this->db->getListByNazev($this->getSearchValue(), $this->getCurrentOrder());	
		}
		
		foreach($rows as $row) {
			if ($row->datum_narozeni != null) {
				$dt = new DateTime($row->datum_narozeni);
				$row->datum_narozeni = $dt->format('d. m. Y');	
			}	
			
			if ($row->datum_umrti != null) {
				$dt = new DateTime($row->datum_umrti);
				$row->datum_umrti = $dt->format('d. m. Y');	
			}	
		}
		
		return $rows;
	}
	
	private function validate($row) {
		
		// jméno
		if (strlen($row->jmeno) < 2 || strlen($row->jmeno) > 250) {
			array_push($this->messages, new JPErrorMessage("Jméno autora musí mít min. 2 a nejvíce 250 znaků."));
		}

		// příjmení
		if (strlen($row->prijmeni) < 3 || strlen($row->prijmeni) > 250) {
			array_push($this->messages, new JPErrorMessage("Příjmení autora musí mít min. 3 a nejvíce 250 znaků."));
		}
		
		// Titul před
		if (strlen($row->titul_pred) > 250) {
			array_push($this->messages, new JPErrorMessage("Titul před jménem nesmí mít více než 250 znaků."));
		}
		
		// Titul za
		if (strlen($row->titul_za) > 250) {
			array_push($this->messages, new JPErrorMessage("Titul za jménem nesmí mít více než 250 znaků."));
		}
		
		// datum narození
		if ($row->datum_narozeni != null && DateTime::createFromFormat('d. m. Y', $row->datum_narozeni) == false 
			&& DateTime::createFromFormat('Y-m-d', $row->datum_narozeni) == false) {
			array_push($this->messages, new JPErrorMessage("Datum narození není platným datem."));
		}
		
		// datum úmrtí
		if ($row->datum_umrti != null && DateTime::createFromFormat('d. m. Y', $row->datum_umrti) == false 
			&& DateTime::createFromFormat('Y-m-d', $row->datum_umrti) == false) {
			array_push($this->messages, new JPErrorMessage("Datum umrtí není platným datem."));
		}

		// místo narození
		if (strlen($row->misto_narozeni) > 250) {
			array_push($this->messages, new JPErrorMessage("Místo narození nesmí mít více než 250 znaků."));
		}

		// místo umrtí
		if (strlen($row->misto_umrti) > 250) {
			array_push($this->messages, new JPErrorMessage("Místo úmrtí nesmí mít více než 250 znaků."));
		}
		
		if (strlen($row->web) > 250) {
			array_push($this->messages, new JPErrorMessage("Webová stránka nesmí mít více než 250 znaků."));
		}
	
		return count($this->messages) === 0; 	
	}
	
	public function preSave($row) {
		
		// datum narození
		if ($row->datum_narozeni != null) {
			if (DateTime::createFromFormat('d. m. Y', $row->datum_narozeni) == true) {
				$dt = DateTime::createFromFormat('d. m. Y', $row->datum_narozeni);	
			} else {
				$dt = DateTime::createFromFormat('Y-m-d', $row->datum_narozeni);
			}
			
			$row->datum_narozeni = $dt->format("Y-m-d");
		} else {
			unset($row->datum_narozeni);
		}
		
		// datum úmrtí
		if ($row->datum_umrti != null) {
			if (DateTime::createFromFormat('d. m. Y', $row->datum_umrti) == true) {
				$dt = DateTime::createFromFormat('d. m. Y', $row->datum_umrti);	
			} else {
				$dt = DateTime::createFromFormat('Y-m-d', $row->datum_umrti);
			}
			
			$row->datum_umrti = $dt->format("Y-m-d");
		} else {
			unset($row->datum_umrti);
		}
		
		return $row;
	}
	
	public function add() {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$row = $this->preSave($row);
			
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nového autora."));
			} else {
				$idObject = $this->db->getLastId();
				array_push($this->messages, new JPInfoMessage('Autor byl úspěšně přidán. 
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
			$row = $this->preSave($row);
			
			$result = $this->db->update($row, $this->getObjectFromUrl()->id);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Autora se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage('Autor byl úspěšně aktualizován. 
					<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));
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
	
	public function getListByAuthor() {
		$objects = $this->dbObject->getListByAuthor($this->getObjectId(), true);
		
		foreach($objects as $object) {
			if ($object->skryta == 1) {
				$object->img_512 = null;	
			}
		}
		
		return $objects;			
	}
	
	private function getFormValues() {
		$row = new stdClass();
		$row->jmeno = filter_input (INPUT_POST, "jmeno", FILTER_SANITIZE_STRING);
		$row->prijmeni = filter_input (INPUT_POST, "prijmeni", FILTER_SANITIZE_STRING);
		$row->titul_pred = filter_input (INPUT_POST, "titul_pred", FILTER_SANITIZE_STRING);
		$row->titul_za = filter_input (INPUT_POST, "titul_za", FILTER_SANITIZE_STRING);
		$row->datum_narozeni = filter_input (INPUT_POST, "datum_narozeni", FILTER_SANITIZE_STRING);
		$row->datum_umrti = filter_input (INPUT_POST, "datum_umrti", FILTER_SANITIZE_STRING);
		$row->misto_narozeni = filter_input (INPUT_POST, "misto_narozeni", FILTER_SANITIZE_STRING);
		$row->misto_umrti = filter_input (INPUT_POST, "misto_umrti", FILTER_SANITIZE_STRING);
		$row->obsah = $_POST["editor"]; // TODO: sanitize
		$row->web = filter_input (INPUT_POST, "web", FILTER_SANITIZE_STRING);
		
		$row->zpracovano = filter_input (INPUT_POST, "zpracovano", FILTER_SANITIZE_STRING);
		$row->zpracovano = ($row->zpracovano === "on" ? 1 : 0);
		
		return $row;
	}
	
	public function getStringId() {
		return "author";	
	}

	public function getObjectId() {
		global $wp_query;
		
		$id = (int) $wp_query->query_vars['autor'];
		if ($id == null) {
			return parent::getObjectId();
		}
		
		return $id;
	}
	
	public function getCountObjectsForAuthor($idAuthor) {
		return $this->db->getCountObjectsForAuthor($idAuthor);	
	}
	
	public function getOrders() {
		$orders = array();
		
		// dle názvu
		$order = new stdClass();
		$order->nazev = "Název";
		$order->url = "nazev";
		array_push($orders, $order);
		
		// dle vytvoření
		$order = new stdClass();
		$order->nazev = "Počet objektů";
		$order->url = "pocet-objektu";
		array_push($orders, $order);
		

		return $orders;
	}
	
	public function getCount() {
		if (!$this->getSearchValueValid()) {
			return parent::getCount();	
		}
		
		return $this->db->getCountByNazev($this->getSearchValue()); 
	}
	
	public function getFullname() {
		$obj = $this->getObjectById($this->getObjectId());
		
		return trim($obj->titul_pred." ".$obj->jmeno." ".$obj->prijmeni." ".$obj->titul_za);	
	}
	
	public function getSelectedSources() {
		$sources = array ();		
		foreach($this->getSourcesForAuthor() as $source) {
			array_push($sources, $source);
		}
		
		// doplníme pět dalších
		for ($i = 1; $i <= 5; $i++) {
			array_push($sources, 0);
		}
		
		return $sources;
	}
	
	public function getSourcesForAuthor() {
		if ($this->getObjectId() == null) {
			return null;
		}
		
		return $this->dbSource->getSourcesForAuthor($this->getObjectId());	
	}
	
	public function getCatalogPage($page, $search) {
		return $this->db->getCatalogPage($page, $search);		
	}
	
	public function getImgForAuthor($authorId) {
		return $this->db->getImgForAuthor($authorId);	
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
				
				$source->nazev = filter_input (INPUT_POST, "nazev".$value, FILTER_SANITIZE_STRING);
				$source->url = filter_input (INPUT_POST, "url".$value, FILTER_SANITIZE_STRING);
				$source->isbn = filter_input (INPUT_POST, "isbn".$value, FILTER_SANITIZE_STRING);
				
				$source->cerpano = filter_input (INPUT_POST, "cerpano".$value, FILTER_SANITIZE_STRING);
				$source->cerpano = ($source->cerpano === "on" ? 1 : 0);

				$source->deleted = filter_input (INPUT_POST, "deleted".$value, FILTER_SANITIZE_STRING);
				$source->deleted = ($source->deleted === "on" ? 1 : 0);
				$source->autor = $this->getObjectId();
				$source->objekt = null;

				array_push($sources, $source);
			}
		}
		
		return $sources;
	}
	
	private function validateSources($sources) {
		
		foreach ($sources as $source) {
			if (isset($source->id) && strlen($source->nazev) == 0 && !$source->deleted) {
				array_push($this->messages, new JPErrorMessage("Každý zdroj, který byl dříve uložen, musí mít vyplněný název nebo být označen pro smazání."));
			}
			if (!isset($source->id) && strlen($source->nazev) == 0 && (strlen($source->url) > 0 || strlen ($source->isbn) > 0)) {
				array_push($this->messages, new JPErrorMessage("Každý zdroj, který má zadáno URL či ISBN, musí mít i název."));
			}
		}
		
		return count($this->messages) === 0;
	} 	
	
	public function manageSources() {
		$sources = $this->getFormSourcesValues();
		
		if (count($sources) == 0) {
			return $this.getSelectedSources();	
		}
		
		$result = $this->validateSources($sources);
		if ($result) {
			foreach ($sources as $source) {
				if (strlen($source->nazev) == 0) {
					continue;	
				}
				
				if (isset($source->id)) {
					$result = $this->dbSource->update($source, $source->id, false);
				} else {
					$result = $this->dbSource->create($source, false);
				}
			}
			
			array_push($this->messages, new JPInfoMessage('Zdroje byly aktualizovány. 
				<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));
				
			return $this->getSelectedSources();
		}
		
		return $sources;
	}
	
	public function getUniqueFirstCharSurname() {
		return $this->db->getUniqueFirstCharSurname();
	}
	
	public function getFirstCharFromURL() {
		$str = filter_input (INPUT_GET, "znak", FILTER_SANITIZE_STRING);
		if (strlen($str) > 0) {
			return urldecode($str);
		}
		
		return null;
	}
	
	function getCatalogByChar($ch) {
		return $this->db->getCatalogPageByChar($ch);
	}
	
	function getSearchFirstChar() {
		if (!isset($_GET["znak"])) {
			return null;	
		}	
		
		return filter_input (INPUT_GET, "znak", FILTER_SANITIZE_STRING);
	}
		
    public function getCategoryNameForObject($id) {
        $categories = $this->dbCategory->getAll();	

        foreach ($categories as $category) { 
            if ($category->id === $id) {
                return $category->nazev;	
            }
        }

        return "Neznámá";
    }
    
    public function getTagsForObjectStr($idObject) {
        $str = "";

        $tags = $this->dbObject2Tag->getTagsForObject($idObject);
        foreach ($tags as $tag) {

            if (strlen($str) > 0) {
                $str = $str.", ";	
            }	

            $str = $str.$tag->nazev;
        }

        return $str;
    }    
        
}
