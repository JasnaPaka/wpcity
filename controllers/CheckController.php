<?php
$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/CategoryDb.php";
include_once $ROOT."db/ObjectDb.php";

include_once $ROOT . "utils/WikidataBuilder.php";

/**
 * Akce na kontrolu údajů u děl
 */
class CheckController extends JPController {
    
    protected $db;
    protected $dbObjects;
    protected $dbSource;

    function __construct() {
        $this->db = new CategoryDb();
        $this->dbObjects = new ObjectDb();
        $this->dbSource = new SourceDb();
    }
    
    public function getStringId() {
        return "check";
    }
    
    private function getCategoryId() {
        return (int) filter_input (INPUT_GET, "category", FILTER_SANITIZE_STRING);
    }
    
    public function getCategory() {
        return $this->db->getById($this->getCategoryId());
    }
    
    public function getCountObjectsNoAccessibility() {
        return $this->dbObjects->getCountObjectsInCategoryNoAccessibility($this->getCategoryId());
    }
    
    public function getObjectsNoAccessibility() {
        return $this->dbObjects->getObjectsInCategoryNoAccessibility($this->getCategoryId());
    }

    public function getCountObjectsNoMaterial() {
        return $this->dbObjects->getCountObjectsInCategoryNoMaterial($this->getCategoryId());
    }
    
    public function getObjectsNoMaterial() {
        return $this->dbObjects->getObjectsInCategoryNoMaterial($this->getCategoryId());
    }

    public function getMonuments() {
    	return $this->dbObjects->getListByPamatkovaOchrana();
	}

	public function findMonument($monumentId) {
		return WikidataSource::getWikidataIdentifier($monumentId);
	}

	public function processMonument($object, $wikiId) {

    	// 1) Nejprve uložíme k objektu informaci o Wikidatech
		$zdroj = new stdClass();
		$zdroj->typ = SourceTypes::CODE_WIKIDATA;
		$zdroj->identifikator = $wikiId;
		$zdroj->cerpano = 0;
		$zdroj->deleted = 0;
		$zdroj->objekt = $object->id;
		$this->dbSource->create($zdroj);
		//$this->dbSource–>create($zdroj);

    	$wb = new WikidataBuilder($this->dbSource, $this->dbSource->getSourcesForObject($object->id));
		return $wb->process();
	}
}

