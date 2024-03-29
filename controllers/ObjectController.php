<?php

$ROOT = plugin_dir_path(__FILE__) . "../";

include_once $ROOT . "fw/JPMessages.php";
include_once $ROOT . "fw/GPSUtils.php";
include_once $ROOT . "fw/GoogleMapsBuilder.php";
include_once $ROOT . "fw/CityService.php";
include_once $ROOT . "fw/IdentifierAble.php";

include_once $ROOT . "db/CategoryDb.php";
include_once $ROOT . "db/ObjectDb.php";
include_once $ROOT . "db/PhotoDb.php";
include_once $ROOT . "db/AuthorDb.php";
include_once $ROOT . "db/SourceDb.php";
include_once $ROOT . "db/CollectionDb.php";
include_once $ROOT . "db/TagDb.php";
include_once $ROOT . "db/Object2AuthorDb.php";
include_once $ROOT . "db/Object2TagDb.php";
include_once $ROOT . "db/PoiDb.php";
include_once $ROOT . "db/Object2CollectionDb.php";
include_once $ROOT . "db/HistoryDb.php";
include_once $ROOT . "db/SettingDb.php";

include_once $ROOT . "utils/SourceType.php";
include_once $ROOT . "utils/SourceTypes.php";
include_once $ROOT . "utils/WikidataBuilder.php";
include_once $ROOT . "utils/WikidataIdentifier.php";

include_once $ROOT . "controllers/SettingController.php";
include_once $ROOT . "controllers/AbstractDefaultController.php";

class ObjectController extends AbstractDefaultController implements IdentifierAble
{

	protected $db;
	protected $dbCategory;
	protected $dbAuthor;
	protected $dbSource;
	protected $dbObject2Author;
	protected $dbTag;
	protected $dbObject2Tag;
	protected $dbObject2Collection;
	protected $dbCollection;
	protected $dbPoi;
	protected $dbHistory;
	protected $dbSetting;

	private $cacheTagSelected;
	private $poisForObjectObjCache = null;

	function __construct()
	{
		$this->db = new ObjectDb();
		$this->dbCategory = new CategoryDb();
		$this->dbPhoto = new PhotoDb();
		$this->dbAuthor = new AuthorDb();
		$this->dbSource = new SourceDb();
		$this->dbTag = new TagDb();
		$this->dbObject2Author = new Object2AuthorDb();
		$this->dbObject2Tag = new Object2TagDb();
		$this->dbCollection = new CollectionDb();
		$this->dbPoi = new PoiDb();
		$this->dbObject2Collection = new Object2CollectionDb();
		$this->dbHistory = new HistoryDb();
		$this->dbSetting = new SettingDb();
	}

	public function getList()
	{
		// výpis děl dle autora v administraci
		if ($this->getIsShowedCategory()) {
			return $this->db->getPageByCategory($this->getPageCurrent(), $this->getCurrentCategory()->id, $this->getCurrentOrder());
		}

		if (!$this->getSearchValueValid()) {
			if ($this->getSearchValue() != null) {
				array_push($this->messages, new JPErrorMessage("Hledaný výraz musí mít alespoň tři znaky."));
			}

			return parent::getList();
		}

		if ($this->getShowAll()) {
			return $this->db->getListByNazev($this->getSearchValue(), $this->getCurrentOrder(), true, true);
		} else {
			return $this->db->getPageByNazev($this->getPageCurrent(), $this->getSearchValue());
		}

		return $this->db->getListByNazev($this->getSearchValue());
	}

	public function getCount()
	{
		if ($this->getIsShowedCategory()) {
			return $this->db->getCountObjectsInCategory($this->getCurrentCategory()->id, false);
		}

		if (!$this->getSearchValueValid()) {
			return $this->db->getCountAgreed();
		}

		return $this->db->getCountByNazev($this->getSearchValue());
	}

	public function getCategoryNameForObject($id)
	{
		$categories = $this->dbCategory->getAll();

		foreach ($categories as $category) {
			if ($category->id === $id) {
				return $category->nazev;
			}
		}

		return "Neznámá";
	}

	public function getAllCategories()
	{
		return $this->dbCategory->getAll();
	}

	private function validate($row)
	{
		// název
		if (strlen(trim($row->nazev)) < 1 || strlen(trim($row->nazev)) > 250) {
			array_push($this->messages, new JPErrorMessage("Název objektu musí mít min. 1 a nejvíce 250 znaků."));
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

	private function validatePoi($poi)
	{
		// název
		if (strlen($poi->nazev) < 3 || strlen($poi->nazev) > 250) {
			array_push($this->messages, new JPErrorMessage("Název bodu musí mít min. 3 a nejvíce 250 znaků."));
		}

		// latitude
		if (!GPSUtils::getIsValidLatitude($poi->latitude)) {
			array_push($this->messages, new JPErrorMessage("Neplatná latitude u GPS souřadnice."));
		}

		// longitude
		if (!GPSUtils::getIsValidLongitude($poi->longitude)) {
			array_push($this->messages, new JPErrorMessage("Neplatná longitude u GPS souřadnice."));
		}

		return count($this->messages) === 0;
	}

	private function getCurrentDateStr()
	{
		$dt = new DateTime();
		return $dt->format('Y-m-d H:i:s');
	}

	private function setAuthors($row)
	{
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

	public function addPublic()
	{
		// Získání dat z formuláře
		$nazev = filter_input(INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		if (strlen(trim($nazev)) == 0) {
			$nazev = "Bez názvu";
		}

		$info = filter_input(INPUT_POST, "info", FILTER_SANITIZE_STRING);
		if (!is_user_logged_in()) {
			$author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING);
		} else {
			$current_user = wp_get_current_user();
			$author = $current_user->display_name;
		}
		$author = trim($author);

		if (strlen($author) == 0) {
			$author = "Neuveden";
		}

		$latitude = (double)filter_input(INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		$longitude = (double)filter_input(INPUT_POST, "longitude", FILTER_SANITIZE_STRING);


		// Validace
		if (strlen($author) > 250) {
			array_push($this->messages, new JPErrorMessage("Jméno autora příspěvku může mít maximálně 250 znaků."));
		}

		if ($latitude == 0 || $longitude == 0) {
			array_push($this->messages, new JPErrorMessage('Nebylo zvoleno umístění bodu v mapě.'));

			$objekt = new stdClass();
			$objekt->info = $info;
			$objekt->latitude = $latitude;
			$objekt->longitude = $longitude;

			return $objekt;
		}

		$kategorie = $this->dbCategory->getCategoryByUrl('ostatni');
		if ($kategorie == null) {
			$kategorie = $this->dbCategory->getCategoryByUrl('sipky');
		}

		// Uložení
		$objekt = new stdClass();
		$objekt->nazev = $nazev;
		$objekt->latitude = $latitude;
		$objekt->longitude = $longitude;
		$objekt->kategorie = $kategorie->id;
		$objekt->interni = $info;
		$objekt->deleted = 0;
		$objekt->schvaleno = 0;
		$objekt->pridal_autor = $author;
		$objekt->pridal_datum = $this->getCurrentDateStr();

		$this->db->create($objekt);

		$this->addPhotos($this->db->getLastId(), null, null,false);

		array_push($this->messages, new JPInfoMessage("Dílo bylo úspěšně přidáno. Po schválení se objeví v katalogu. Děkujeme!"));

		return $this->getInitPublicForm();
	}

	public function getInitPublicForm()
	{
		$objekt = new stdClass();
		$objekt->info = "";
		$objekt->latitude = 0;
		$objekt->longitude = 0;

		return $objekt;
	}

	public function add()
	{
		return $this->addInternal(false);
	}

	private function addInternal($public)
	{
		$row = $this->getFormValues();

		$result = $this->validate($row);
		if ($result) {
			$row = $this->setAuthors($row);
			$row->potreba_foto = 0;
			$row->schvaleno = $public ? 0 : 1;
			$row->pamatkova_ochrana = '';
			$result = $this->db->create($row);

			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nový objekt."));
			} else {
				$idObject = $this->db->getLastId();

				if (!$public) {
					array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně přidán.
                                        <a href="' . $this->getUrl(JPController::URL_VIEW, $idObject) . '">Zobrazit detail</a>'));
				} else {
					array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně přidán a čeká na schválení administrátorem. Děkujeme za přidání!'));
				}

				// Záznam přidán, nyní přidáme fotky (když selže, tak to jen uživateli oznámíme)
				$this->addPhotos($idObject,  null, null,false);

				// Nastavíme autora objektu
				$this->addAuthor($idObject);

				// Nastavíme štítky
				$this->addTags($idObject);

				// Nastavíme umístění (jen pokud jej uživatel nenastavil sám).
				if (strlen($row->mestska_cast) == 0) {
					$this->updateLocation($idObject);
				}

				return new stdClass();
			}
		}

		return $row;
	}

	public function addPoi()
	{
		$poi = $this->getFormPoiValues();
		$result = $this->validatePoi($poi);
		if ($result) {
			$row = $this->getObjectFromUrl();
			if (!$row) {
				return;
			}

			$poi->objekt = $row->id;
			$result = $this->dbPoi->create($poi);

			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nový bod."));
			} else {
				array_push($this->messages, new JPInfoMessage('Bod byl úspěšně přidán.
                    <a href="' . $this->getUrl("poi-list", $poi->objekt) . '">Zobrazit seznam</a>'));

				return new stdClass();
			}
		}
	}

	private function addAuthor($idObject)
	{
		$author = $this->getAuthorFromAddForm();
		if ($author == null) {
			return null;
		}

		$row = new stdClass();
		$row->objekt = $idObject;
		$row->autor = $author->id;

		$result = $this->dbObject2Author->create($row);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Autora '" . $author->jmeno . "' se nepodařilo přidat k objektu."));
		}
	}

	private function addTags($idObject)
	{

		foreach ($this->getAllTags() as $tag) {
			$tagName = "tag" . $tag->id;
			$value = filter_input(INPUT_POST, $tagName, FILTER_SANITIZE_STRING);
			if ($value == "on") {

				$row = new stdClass();
				$row->objekt = $idObject;
				$row->stitek = $tag->id;

				$result = $this->dbObject2Tag->create($row);

				$this->cacheTagSelected = null;
			}
		}
	}

	public function manageAuthors()
	{
		$authors = $this->getFormAuthorsValues();
		$cooperations = $this->getFormCooperationsValues();

		// Validace
		foreach ($authors as $author) {
			if ($author > 0 && $this->dbAuthor->getById($author) == null) {
				array_push($this->messages, new JPErrorMessage("Jeden ze zvolených autorů nebyl nalezen. Patrně byl před chvílí smazán."));
				return;
			}
		}

		// Smazání starých vazeb a vytvoření nových
		$this->dbObject2Author->deleteOldRelationsForObject($this->getObjectId());

		$i = 0;
		foreach ($authors as $author) {
			if ($author > 0) {
				$row = new stdClass();
				$row->objekt = $this->getObjectId();
				$row->autor = $author;
				$row->spoluprace = $cooperations[$i];

				$result = $this->dbObject2Author->create($row);
			}

			$i++;
		}

		array_push($this->messages, new JPInfoMessage('Autoři byli úspěšně nastaveni.
                <a href="' . $this->getUrl(JPController::URL_VIEW) . '">Zobrazit detail</a>'));
	}

	public function manageCollections()
	{
		$collections = $this->getFormCollectionsValues();

		// Validace
		foreach ($collections as $collection) {
			if ($collection > 0 && $this->dbCollection->getById($collection) == null) {
				array_push($this->messages, new JPErrorMessage("Jeden ze svolených souborů děl nebyl nalezen. Patrně byl před chvílí smazán."));
				return;
			}
		}

		// Smazání starých vazeb a vytvoření nových
		$this->dbObject2Collection->deleteOldRelationsForObject($this->getObjectId());

		$i = 0;
		foreach ($collections as $collection) {
			if ($collection > 0) {
				$row = new stdClass();
				$row->objekt = $this->getObjectId();
				$row->soubor = $collection;

				$result = $this->dbObject2Collection->create($row);
			}

			$i++;
		}

		array_push($this->messages, new JPInfoMessage('Soubory děl byly úspěšně nastaveny.
                <a href="' . $this->getUrl(JPController::URL_VIEW) . '">Zobrazit detail</a>'));

	}


	public function update()
	{
		$row = $this->getFormValues();
		if ($row == null) {
			return null;
		}

		$result = $this->validate($row);
		if ($result) {
			$row = $this->setAuthors($row);

			//$this->historyRecord($this->getObjectFromUrl(), $row);

			$result = $this->db->update($row, $this->getObjectFromUrl()->id);

			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Objekt se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně aktualizován.
                    <a href="' . $this->getUrl(JPController::URL_VIEW) . '">Zobrazit detail</a>'));
			}

			// Nastavíme štítky
			$this->dbObject2Tag->deleteTagsForObject($this->getObjectFromUrl()->id);
			$this->addTags($this->getObjectFromUrl()->id);
		}

		return $row;
	}

	public function updatePoi()
	{
		$poi = $this->getFormPoiValues();
		if ($poi == null) {
			return null;
		}

		$result = $this->validatePoi($poi);
		if ($result) {
			$poi->objekt = $this->getObjectFromUrl()->id;

			$result = $this->dbPoi->update($poi, $this->getPoiFromUrl()->id);

			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se aktualizovat bod."));
			} else {
				array_push($this->messages, new JPInfoMessage('Bod byl úspěšně aktualizován.
                    <a href="' . $this->getUrl("poi-list", $poi->objekt) . '">Zobrazit seznam</a>'));
			}
		}

		return $poi;
	}

	public function updateLocation($id = null)
	{
		if ($id == null) {
			$object = $this->getObjectById($this->getObjectFromUrl()->id);
		} else {
			$object = $this->getObjectById($id);
		}

		if ($object == null) {
			array_push($this->messages, new JPErrorMessage("Objekt se nepodařilo dohledat."));
			return null;
		}

		$setting = $this->dbSetting->getSetting(SettingController::$SETTING_CITY_API_URL);
		if ($setting == null || strlen(trim($setting->hodnota)) == 0) {
			if ($id == null) {
				array_push($this->messages, new JPErrorMessage("Webová služba pro získávání informací o městských částech není nastavena."));
			}
			return null;
		}

		$service = new CityService($setting->hodnota);
		$output = $service->call($object->latitude, $object->longitude);

		if ($service->getStatusCode() == CityService::HTTP_200) {
			$object->mestska_cast = $output->umo->__toString();
			$object->oblast = $output->part->__toString();
			$this->db->update($object, $object->id);
		} else if ($service->getStatusCode() == CityService::HTTP_404 && $id == null) {
			array_push($this->messages, new JPErrorMessage("Objekt se nenachází na území města Plzně."));
		} else if ($service->getStatusCode() == CityService::HTTP_500 && $id == null) {
			array_push($this->messages, new JPErrorMessage("Nepodařilo se získat informaci o městském obvodu. Chyba: " + $output->msg));
		}
	}

	public function approve()
	{
		$object = $this->getObjectById($this->getObjectFromUrl()->id);
		if ($object == null) {
			array_push($this->messages, new JPErrorMessage("Objekt se nepodařilo dohledat."));
			return null;
		}

		$this->db->approveObject($this->getObjectFromUrl()->id);
		$object->schvaleno = 1;

		array_push($this->messages, new JPInfoMessage("Objekt byl úspěšně schválen."));

		return $object;
	}

	public function managePhotos()
	{
		$id = $this->getObjectFromUrl()->id;
		if ($id == null) {
			return null;
		}

		// Nejdříve zaktualizujeme existující fotografie
		$photos = $this->getPhotosForObject();
		$newPhotos = array();
		if (count($photos) > 0) {
			if ($this->validatePhotos($photos)) {
				foreach ($photos as $photo) {

					$idDelete = "delete" . $photo->id;
					if (isset($_POST[$idDelete])) {
						$this->dbPhoto->delete($photo->id);
					} else {
						$photo = $this->refreshPhoto($photo);
						array_push($newPhotos, $photo);
						$result = $this->dbPhoto->update($photo, $photo->id);
					}
				}
			} else {
				foreach ($photos as $photo) {
					$photo = $this->refreshPhoto($photo);
					array_push($newPhotos, $photo);
				}
			}
		}

		// Nahrajeme nové fotografie
		$photos = $this->addPhotos($id, null, null,count($photos) > 0);
		if ($photos != null && count($photos) > 0) {
			foreach ($photos as $photo) {
				array_push($newPhotos, $photo);
			}
		}

		if (count($this->messages) == 0) {
			array_push($this->messages, new JPInfoMessage('Úprava fotografií byla dokončena.
            <a href="' . $this->getUrl(JPController::URL_VIEW) . '">Zobrazit detail objektu</a>'));
		}

		return $newPhotos;
	}

	public function needPhoto()
	{
		$id = $this->getObjectFromUrl()->id;
		if ($id == null) {
			return null;
		}

		$this->db->updateNeedPhoto(1, $id);
		array_push($this->messages, new JPInfoMessage('Byla nastavena potřeba přefotit dílo.'));
	}

	public function noNeedPhoto()
	{
		$id = $this->getObjectFromUrl()->id;
		if ($id == null) {
			return null;
		}

		$this->db->updateNeedPhoto(0, $id);
		array_push($this->messages, new JPInfoMessage('Byla zrušena potřeba přefotit dílo.'));
	}

	public function delete()
	{
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
			array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně smazán.
                    <a href="' . $this->getUrl(JPController::URL_LIST) . '">Zpět na seznam</a>'));
			$this->dbPhoto->deletePhotosByObject($id);
		}
	}

	public function deletePoi()
	{
		$row = $this->getFormPoiValues();
		if ($row == null) {
			return null;
		}

		$id = $this->getPoiFromUrl()->id;
		if ($id == null) {
			return null;
		}

		$result = $this->dbPoi->delete($id);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Bod se nepodařilo smazat."));
		} else {
			array_push($this->messages, new JPInfoMessage('Bod byl úspěšně smazán.
                    <a href="' . $this->getUrl("poi-list") . '">Zpět na seznam bodů</a>'));
			$this->dbPhoto->deletePhotosByObject($id);
		}
	}

	public function getPhotosForObject()
	{
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->dbPhoto->getPhotosByObject($this->getObjectId());
	}

	public function getPhotosForObjectMain()
	{
		$photos = $this->getPhotosForObject();

		foreach ($photos as $photo) {
			if ($photo->primarni && !$photo->skryta) {
				return $photo;
			}
		}

		return null;
	}

	public function getPhotosForObjectNotMain()
	{
		$newPhotos = array();
		$photos = $this->getPhotosForObject();

		foreach ($photos as $photo) {
			if (!$photo->primarni && !$photo->skryta) {
				array_push($newPhotos, $photo);
			}
		}

		return $newPhotos;
	}

	public function getAllAuthors()
	{
		return $this->dbAuthor->getAll();
	}

	public function getAllCollections()
	{
		return $this->dbCollection->getAll();
	}

	public function getAuthorsForObject()
	{
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->db->getAuthorsForObject($this->getObjectId());
	}

	public function getCollectionsForObject()
	{
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->dbObject2Collection->getCollectionsForObject($this->getObjectId());
	}

	public function getAuthorsByObject($idObject)
	{
		return $this->db->getAuthorsForObject($idObject);
	}


	public function getCooperationsForObject()
	{
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->db->getCooperationsForObject($this->getObjectId());
	}

	public function getSourcesForObject()
	{
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->dbSource->getSourcesForObject($this->getObjectId());
	}

	public function getSystemSourcesForObject()
	{
		if ($this->getObjectId() == null) {
			return null;
		}

		return $this->dbSource->getSourcesForObject($this->getObjectId(), true);
	}

	public function getSelectedAuthors()
	{
		$authors = array();
		foreach ($this->getAuthorsForObject() as $author) {
			array_push($authors, $author->id);
		}

		// doplníme ty nevyplněné (prázdné)
		for ($i = count($authors); $i < 6; $i++) {
			array_push($authors, 0);
		}

		return $authors;
	}

	public function getSelectedCollections()
	{
		$collections = array();

		foreach ($this->getCollectionsForObject() as $collection) {
			array_push($collections, $collection->id);
		}

		// doplníme ty nevyplněné (prázdné)
		for ($i = count($collections); $i < 3; $i++) {
			array_push($collections, 0);
		}

		return $collections;
	}

	public function getCooperations()
	{
		$cooperations = array();
		foreach ($this->getCooperationsForObject() as $cooperation) {
			array_push($cooperations, $cooperation->spoluprace);
		}

		// doplníme ty nevyplněné (prázdné)
		for ($i = count($cooperations); $i < 6; $i++) {
			array_push($cooperations, "");
		}

		return $cooperations;
	}

	private function getAuthorFromAddForm()
	{
		$idAuthor = (int)filter_input(INPUT_POST, "autor", FILTER_SANITIZE_STRING);
		if ($idAuthor <= 0) {
			return null;
		}

		return $this->dbAuthor->getById($idAuthor);
	}

	private function getFormValues()
	{
		$row = new stdClass();
		$row->nazev = filter_input(INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->latitude = (double)filter_input(INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		$row->longitude = (double)filter_input(INPUT_POST, "longitude", FILTER_SANITIZE_STRING);
		$row->kategorie = (int)filter_input(INPUT_POST, "kategorie", FILTER_SANITIZE_STRING);
		$row->popis = filter_input(INPUT_POST, "popis", FILTER_SANITIZE_STRING);
		$row->obsah = $_POST["editor"]; // TODO: sanitize
		$row->interni = $_POST["interni"]; // TODO: sanitize
		$row->rok_realizace = filter_input(INPUT_POST, "rok_realizace", FILTER_SANITIZE_STRING);
		$row->rok_vzniku = filter_input(INPUT_POST, "rok_vzniku", FILTER_SANITIZE_STRING);
		$row->rok_zaniku = filter_input(INPUT_POST, "rok_zaniku", FILTER_SANITIZE_STRING);
		$row->prezdivka = filter_input(INPUT_POST, "prezdivka", FILTER_SANITIZE_STRING);
		$row->material = filter_input(INPUT_POST, "material", FILTER_SANITIZE_STRING);
		$row->pamatkova_ochrana = filter_input(INPUT_POST, "pamatkova_ochrana", FILTER_SANITIZE_STRING);
		$row->pristupnost = filter_input(INPUT_POST, "pristupnost", FILTER_SANITIZE_STRING);

		$row->mestska_cast = filter_input(INPUT_POST, "mestska_cast", FILTER_SANITIZE_STRING);
		$row->oblast = filter_input(INPUT_POST, "oblast", FILTER_SANITIZE_STRING);
		$row->adresa = filter_input(INPUT_POST, "adresa", FILTER_SANITIZE_STRING);

		$row->zruseno = filter_input(INPUT_POST, "zruseno", FILTER_SANITIZE_STRING);
		$row->zruseno = ($row->zruseno === "on" ? 1 : 0);

		$row->zpracovano = filter_input(INPUT_POST, "zpracovano", FILTER_SANITIZE_STRING);
		$row->zpracovano = ($row->zpracovano === "on" ? 1 : 0);

		$row->pridano_osm = filter_input(INPUT_POST, "pridano_osm", FILTER_SANITIZE_STRING);
		$row->pridano_osm = ($row->pridano_osm === "on" ? 1 : 0);

		$row->pridano_vv = filter_input(INPUT_POST, "pridano_vv", FILTER_SANITIZE_STRING);
		$row->pridano_vv = ($row->pridano_vv === "on" ? 1 : 0);

		return $row;
	}

	private function getFormPoiValues()
	{
		$poi = new stdClass();
		$poi->nazev = filter_input(INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$poi->latitude = (double)filter_input(INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		$poi->longitude = (double)filter_input(INPUT_POST, "longitude", FILTER_SANITIZE_STRING);
		$poi->popis = filter_input(INPUT_POST, "popis", FILTER_SANITIZE_STRING);

		return $poi;
	}

	private function getFormAuthorsValues()
	{
		$authors = array();

		array_push($authors, (int)filter_input(INPUT_POST, "autor1", FILTER_SANITIZE_STRING));
		array_push($authors, (int)filter_input(INPUT_POST, "autor2", FILTER_SANITIZE_STRING));
		array_push($authors, (int)filter_input(INPUT_POST, "autor3", FILTER_SANITIZE_STRING));
		array_push($authors, (int)filter_input(INPUT_POST, "autor4", FILTER_SANITIZE_STRING));
		array_push($authors, (int)filter_input(INPUT_POST, "autor5", FILTER_SANITIZE_STRING));
		array_push($authors, (int)filter_input(INPUT_POST, "autor6", FILTER_SANITIZE_STRING));

		return $authors;
	}

	private function getFormCooperationsValues()
	{
		$cooperations = array();

		array_push($cooperations, filter_input(INPUT_POST, "spoluprace1", FILTER_SANITIZE_STRING));
		array_push($cooperations, filter_input(INPUT_POST, "spoluprace2", FILTER_SANITIZE_STRING));
		array_push($cooperations, filter_input(INPUT_POST, "spoluprace3", FILTER_SANITIZE_STRING));
		array_push($cooperations, filter_input(INPUT_POST, "spoluprace4", FILTER_SANITIZE_STRING));
		array_push($cooperations, filter_input(INPUT_POST, "spoluprace5", FILTER_SANITIZE_STRING));
		array_push($cooperations, filter_input(INPUT_POST, "spoluprace6", FILTER_SANITIZE_STRING));


		return $cooperations;
	}

	private function getFormCollectionsValues()
	{
		$collections = array();

		array_push($collections, (int)filter_input(INPUT_POST, "collection1", FILTER_SANITIZE_STRING));
		array_push($collections, (int)filter_input(INPUT_POST, "collection2", FILTER_SANITIZE_STRING));
		array_push($collections, (int)filter_input(INPUT_POST, "collection3", FILTER_SANITIZE_STRING));

		return $collections;
	}


	public function getGoogleMapSettings()
	{
		$setting["gm_key"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_KEY)->hodnota;
		$setting["gm_lat"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_LAT)->hodnota;
		$setting["gm_lng"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_LON)->hodnota;
		$setting["gm_zoom"] = $this->dbSetting->getSetting(SettingController::$SETTING_GM_ZOOM)->hodnota;

		return $setting;
	}

	public function getGoogleMapPointContent($lat, $lng)
	{
		$map = new GoogleMapsBuilder($this->getGoogleMapSettings(), $lat, $lng);
		$map->addPois($this->getPoisForObject());
		return $map->getOutput();
	}

	public function getGoogleMapPointEditContent($lat, $lng)
	{
		$map = new GoogleMapsBuilder($this->getGoogleMapSettings(), $lat, $lng);
		return $map->getOutputEdit();
	}

	public function getGoogleMapPoisContent()
	{
		$map = new GoogleMapsBuilder($this->getGoogleMapSettings(), NULL, NULL);
		$map->addPois($this->getPoisForObject());

		return $map->getOutputPois();
	}

	public function getOrders()
	{
		$orders = array();

		// dle názvu
		$order = new stdClass();
		$order->nazev = "Název";
		$order->url = "nazev";
		array_push($orders, $order);

		// dle vytvoření
		$order = new stdClass();
		$order->nazev = "Data vytvoření";
		$order->url = "vytvoreni";
		array_push($orders, $order);

		// dle data poslední aktualizace
		$order = new stdClass();
		$order->nazev = "Data aktualizace";
		$order->url = "aktualizace";
		array_push($orders, $order);

		return $orders;
	}

	public function getStringId()
	{
		return "object";
	}

	public function getObjectId()
	{
		global $wp_query;

		if (isset($wp_query->query_vars['objekt'])) {
			$id = (int)$wp_query->query_vars['objekt'];
			if ($id == null) {
				return parent::getObjectId();
			}
		} else {
			return parent::getObjectId();
		}

		return $id;
	}

	public function getObjectCategory($id)
	{
		return $this->dbCategory->getById($id);
	}

	public function getRandomObjectWithPhoto()
	{
		$count = $this->db->getCountObjectsWithPhotos();
		$randNumber = rand(1, $count);

		return $this->db->getRandomObjectWithPhoto($randNumber);
	}

	public function getLastObjectWithPhoto()
	{
		return $this->db->getLastObjectWithPhoto();
	}

	public function getCatalogPage($page, $search)
	{

		if ($this->getIShowedBezAutora()) {
			$objects = $this->db->getCatalogPageWithoutAuthor($this->getCurrentCategory());
		} else {
			$objects = $this->db->getCatalogPage($page, $search, $this->getCurrentTag(), $this->getCurrentCategory());
		}

		foreach ($objects as $object) {
			if ($object->skryta == 1) {
				$object->img_512 = null;
			}
		}

		return $objects;
	}

	public function getAuthorFullname($obj)
	{
		return trim($obj->titul_pred . " " . $obj->jmeno . " " . $obj->prijmeni . " " . $obj->titul_za);
	}

	public function getCountKeSchvaleni()
	{
		return $this->db->getCountKeSchvaleni();
	}

	public function getAllTags()
	{
		return $this->dbTag->getAll();
	}

	public function getAllTagsForWeb()
	{
		return $this->dbTag->getAll("groups");
	}

	public function getIsTagSelected($idTag)
	{

		$idObject = $this->getObjectId();
		if ($idObject == null) {
			return false;
		}

		if ($this->cacheTagSelected == null) {
			$this->cacheTagSelected = $this->dbObject2Tag->getTagsForObject($idObject);
		}

		foreach ($this->cacheTagSelected as $selectedTag) {
			if ($selectedTag->stitek == $idTag) {
				return true;
			}
		}

		return false;
	}

	public function getIsShowedTag()
	{
		global $wp_query;

		$id = (int)$wp_query->query_vars['stitek'];
		return $this->dbTag->getById($id) != null;
	}

	public function getIsShowedCategory()
	{
		global $wp_query;

		$id = 0;

		// v administraci máme "category", na webu "kategorie" :)
		if (isset($_GET["category"])) {
			$id = (int)filter_input(INPUT_GET, "category", FILTER_SANITIZE_STRING);
		} else if (isset($wp_query->query_vars['kategorie'])) {
			$id = (int)$wp_query->query_vars['kategorie'];
		}

		if ($id == 0) {
			return null;
		}

		return $this->dbCategory->getById($id) != null;
	}

	public function getCurrentTag()
	{
		global $wp_query;

		$id = (int)$wp_query->query_vars['stitek'];
		return $this->dbTag->getById($id);
	}

	public function getCurrentCategory()
	{
		global $wp_query;

		// v administraci máme "category", na webu "kategorie" :)
		if (isset($_GET["category"])) {
			$id = (int)filter_input(INPUT_GET, "category", FILTER_SANITIZE_STRING);
		} else {
			$id = (int)$wp_query->query_vars['kategorie'];
		}

		if ($id == 0) {
			return null;
		}

		return $this->dbCategory->getById($id);
	}

	public function getIShowedBezAutora()
	{
		global $wp_query;

		$id = (int)$wp_query->query_vars['bezautora'];

		return $id > 0;
	}

	public function getTagsForObject($idObject)
	{
		return $this->dbObject2Tag->getTagsForObject($idObject);
	}

	public function getTagsForObjectStr($idObject)
	{
		$str = "";

		$tags = $this->dbObject2Tag->getTagsForObject($idObject);
		foreach ($tags as $tag) {

			if (strlen($str) > 0) {
				$str = $str . ", ";
			}

			$str = $str . $tag->nazev;
		}

		return $str;
	}

	public function getIsZobrazeniList()
	{
		if (isset($_GET["zobrazeni"]) && $_GET["zobrazeni"] == "list") {
			return true;
		}

		return false;
	}

	public function getZobrazeniStr($containsPageNumber)
	{
		if (!isset($_GET["zobrazeni"])) {
			return "";
		}

		$param = $_GET["zobrazeni"] == "list" ? "zobrazeni=list" : "zobrazeni=grid";
		if ($containsPageNumber) {
			$param = "&amp;" . $param;
		} else {
			$param = "?" . $param;
		}

		return $param;
	}

	public function getAdminUrlParams()
	{
		$action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_STRING);
		if (strlen($action) == 0) {
			$action = parent::URL_LIST;
		}

		if (($action == parent::URL_LIST) && isset($_GET["category"])) {
			$idCategory = (int)filter_input(INPUT_GET, "category", FILTER_SANITIZE_STRING);

			return "&amp;category=" . $idCategory;
		}

		return "";
	}

	public function getPoisForObject()
	{

		if ($this->poisForObjectObjCache == null) {
			$object = $this->getObjectFromUrl();
			if ($object == null) {
				$id = $this->getObjectId();
				if ($id == null) {
					return null;
				}

				$object = $this->getObjectById($id);
			}

			if ($object == null) {
				return null;
			}

			$this->poisForObjectObjCache = $object;
		}

		return $this->dbPoi->getPoisForObject($this->poisForObjectObjCache->id);
	}

	public function getPoiFromUrl()
	{
		$id = (int)filter_input(INPUT_GET, "poi", FILTER_SANITIZE_STRING);
		if ($id == null) {
			return null;
		}

		return $this->dbPoi->getById($id);
	}

	public function getHistoryForObject()
	{
		return $this->dbHistory->getHistoryForObject($this->getObjectFromUrl()->id);
	}

	private function historyRecord($puvodni, $novy)
	{

		// TODO:
		$popis = "";

		if ($puvodni->rok_vzniku !== $novy->rok_vzniku) {
			$this->createHistory($puvodni->rok_vzniku, $novy->rok_vzniku, $popis);
		}
	}

	private function createHistory($oldValue, $newValue, $popis)
	{
		global $current_user;
		get_currentuserinfo();

		$dt = new DateTime();
		$dtStr = $dt->format('Y-m-d H:i:s');
		$row = new stdClass();

		$row->objekt = $this->getObjectFromUrl()->id;
		$row->datum = $dtStr;
		$row->kdo = $current_user->display_name;
		$row->pred = $oldValue;
		$row->po = $newValue;
		$row->popis = $popis;

		$this->dbHistory->create($row);
	}

	public function getAllSettings()
	{
		$settings = array();

		foreach ($this->dbSetting->getAll() as $property) {
			$settings[$property->nazev] = $property->hodnota;
		}

		return $settings;
	}

	public function getSourceType($code) {
		return SourceTypes::getInstance()->getSourceType($code);
	}

	public function getIsKniha($code) {
		return SourceTypes::getInstance()->getIsKniha($code);
	}

	/**
	 * Pro aktuální objekt vrátí jeho identifikátor
	 *
	 * @return int číslo identifikátoru nebo -1, pokud jej nebylo možné získat
	 */
	public function getIdentifier(): int
	{
		$idObject = $this->getObjectId();
		if ($idObject == null) {
			return -1;
		}

		return WikidataIdentifier::getIdentifierForObject($idObject);
	}
}
