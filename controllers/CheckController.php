<?php
$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/CategoryDb.php";
include_once $ROOT."db/ObjectDb.php";
include_once $ROOT."db/AuthorDb.php";
include_once $ROOT."db/CollectionDb.php";

include_once $ROOT . "utils/WikidataBuilder.php";
include_once $ROOT . "utils/WikidataDiffBuilder.php";


/**
 * Akce na kontrolu údajů u děl
 */
class CheckController extends JPController {
    
    protected $db;
    protected $dbObjects;
    protected $dbSource;
    protected $dbAuthor;
    protected $dbCollection;

    function __construct() {
        $this->db = new CategoryDb();
        $this->dbObjects = new ObjectDb();
        $this->dbSource = new SourceDb();
        $this->dbAuthor = new AuthorDb();
        $this->dbCollection = new CollectionDb();
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

	/**
	 * Z pole autorů získá pole identifikátorů do Wikidat
	 *
	 * @param array $authors
	 * @return array pole identifikátorů
	 */
	protected function getWikiDataIdentifiers(array $authors):array {
    	$ids = array();

    	foreach ($authors as $author) {
    		if ($author->identifikator == null
					|| !WikidataIdentifier::getIsValidWikiDataIdentifier($author->identifikator) ) {
				continue;
			}

			$ids[] = $author->identifikator;
    	}

    	return $ids;
	}

	/**
	 * Vrátí položky, kde jsou rozdíly mezi námi evidovanými údaji a těmi, co jsou evidovány ve Wikidatech.
	 * @return array
	 */
	public function getWDDiffAuthors():array {
		$authorsDb = $this->dbAuthor->getAuthorsWithWD();
		$ids = $this->getWikiDataIdentifiers($authorsDb);
		$authorsWD = WikidataSource::getInfoAuthors($ids);

		return WikidataDiffBuilder::getAuthorsDiff($authorsDb, $authorsWD);
	}

	/**
	 * Vrátí položky, kde chybí informace o provázání z jedné strany (u nás či ve Wikidatech)
	 *
	 * @return array
	 */
	public function getWDMissing():array {
		$result = $this->dbSource->getSourceWithWD();
		$dbItems = array();
		foreach ($result as $item) {
			$obj = new stdClass();
			$obj->wdIdentifikator = $item->identifikator;

			if ($item->objekt != null) {
				$object = $this->dbObjects->getById($item->objekt);
				$obj->dbNazev = $object->nazev;
				$obj->dbIdentifikator = WikidataIdentifier::getIdentifierForObject($item->objekt);
			}
			if ($item->autor != null) {
				$author = $this->dbAuthor->getById($item->autor);
				$obj->dbNazev = $author->prijmeni." ".$author->jmeno;
				$obj->dbIdentifikator = WikidataIdentifier::getIdentifierForAuthor($item->autor);
			}
			if ($item->soubor != null) {
				$source = $this->dbCollection->getById($item->soubor);
				$obj->dbNazev = $source->nazev;
				$obj->dbIdentifikator = WikidataIdentifier::getIdentifierForCollection($item->soubor);
			}

			$dbItems[] = $obj;
		}

		$wdItems = WikidataSource::getWDItems();
		return WikidataDiffBuilder::getWDIdentifiersDiff($dbItems, $wdItems);
	}
}

