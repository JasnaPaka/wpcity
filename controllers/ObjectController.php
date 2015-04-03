<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";
include_once $ROOT."fw/GPSUtils.php";
include_once $ROOT."fw/ImgUtils.php";
include_once $ROOT."fw/GoogleMapsBuilder.php";

include_once $ROOT."db/CategoryDb.php";
include_once $ROOT."db/ObjectDb.php"; 
include_once $ROOT."db/PhotoDb.php";
include_once $ROOT."db/AuthorDb.php";
include_once $ROOT."db/SourceDb.php";
include_once $ROOT."db/CollectionDb.php";
include_once $ROOT."db/TagDb.php";
include_once $ROOT."db/Object2AuthorDb.php";
include_once $ROOT."db/Object2TagDb.php";
include_once $ROOT."db/Object2CollectionDb.php";

include_once $ROOT."config.php";

class ObjectController extends JPController {
	
	protected $db;
	private $dbCategory;
	private $dbPhoto;
	private $dbAuthor;
	private $dbSource;
	private $dbObject2Author;
	private $dbTag;
	private $dbObject2Tag;
	private $dbObject2Collection;
	private $dbCollection;
	
	private $categories;
	private $cacheTagSelected;
	
	function __construct() {
		$this->db = new ObjectDb();
		$this->dbCategory = new CategoryDb();
		$this->dbPhoto = new PhotoDb();
		$this->dbAuthor = new AuthorDb();
		$this->dbSource = new SourceDb();
		$this->dbTag = new TagDb();
		$this->dbObject2Author = new Object2AuthorDb();
		$this->dbObject2Tag = new Object2TagDb();
		$this->dbCollection = new CollectionDb();
		$this->dbObject2Collection = new Object2CollectionDb(); 
	}
	
	public function getList() {
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
	
	public function getCount() {
		if (!$this->getSearchValueValid()) {
			return parent::getCount();	
		}
		
		return $this->db->getCountByNazev($this->getSearchValue()); 
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
	
	private function getCurrentDateStr() {
		$dt = new DateTime();
		return $dt->format('Y-m-d H:i:s');
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
	
	public function addPublic() {
		// Získání dat z formuláře		
		$nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		if (strlen (trim($nazev)) == 0) {
			$nazev = "Bez názvu";
		}			
			
		$info = filter_input (INPUT_POST, "info", FILTER_SANITIZE_STRING);
		if (!is_user_logged_in()) {
			$author = filter_input (INPUT_POST, "author", FILTER_SANITIZE_STRING);
		} else {
			$current_user = wp_get_current_user();
			$author = $current_user->display_name;
		}
		$author = trim ($author);
		
		if (strlen($author) == 0) {
			$author = "Neuveden";	
		}
		
		$latitude = (double) filter_input (INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		$longitude = (double) filter_input (INPUT_POST, "longitude", FILTER_SANITIZE_STRING);		
		

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
		
		$this->addPhotos($this->db->getLastId(), false);
		
		array_push($this->messages, new JPInfoMessage("Dílo bylo úspěšně přidáno. Po schválení se objeví v katalogu. Děkujeme!"));
		
		return $this->getInitPublicForm();
	}
	
	public function getInitPublicForm() {
		$objekt = new stdClass();
		$objekt->info = "";
		$objekt->latitude = 0;
		$objekt->longitude = 0;
		
		return $objekt;
	}
	
	public function add() {
		return $this->addInternal(false);
	}
	
	private function addInternal($public) {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$row = $this->setAuthors($row);
			$row->schvaleno = $public ? 0 : 1;
			$result = $this->db->create($row);

			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nový objekt."));
			} else {
				$idObject = $this->db->getLastId();
				
				if (!$public) {
					array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně přidán. 
						<a href="'.$this->getUrl(JPController::URL_VIEW, $idObject).'">Zobrazit detail</a>'));
				} else {
					array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně přidán a čeká na schválení administrátorem. Děkujeme za přidání!'));					
				}
				
				// Záznam přidán, nyní přidáme fotky (když selže, tak to jen uživateli oznámíme)
				$this->addPhotos($idObject, false);
				
				// Nastavíme autora objektu
				$this->addAuthor($idObject);
				
				// Nastavíme štítky
				$this->addTags($idObject);
				
				return new stdClass();
			}
		}
		
		return $row;
	}
	
	private function addPhotos($idObject, $existPhotos) {
		global $_wp_additional_image_sizes;
		global $current_user;
		
     	$sizes = array();
		
		if (!function_exists('wp_handle_upload')) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		
		$upload_overrides = array('test_form' => false);
		$uploadFiles = array ();
		if (isset($_FILES['photo1']) && $_FILES['photo1']['name'] != null) {
			array_push($uploadFiles, $_FILES['photo1']);
		}
		if (isset($_FILES['photo2']) && $_FILES['photo2']['name'] != null) {
			array_push($uploadFiles, $_FILES['photo2']);
		}
		if (isset($_FILES['photo3']) && $_FILES['photo3']['name'] != null) {
			array_push($uploadFiles, $_FILES['photo3']);
		}
		
		$photos = array();
		$newPhotos = array();
		$isFirst = true;
		foreach ($uploadFiles as $uploadFile) {
			
			$result = wp_handle_upload($uploadFile, $upload_overrides);
			
			if ($result["error"] != null) {
				array_push($this->messages, new JPErrorMessage("Při nahrávání fotky '".$uploadFile["name"]."' nastala chyba, 
					díky které fotka nebyla k objektu nahrána. Chyba: ".$result["error"]));
			} else {
				$photos[0] = $this->getRelativePathToImg($result["file"]);
				
				// vše ok, máme info o obrázku, vygenerujeme náhledy
				$sizes["thumbnail"] = array(200, 200, true);
				$sizes["medium"] = array(600, 600, true);
				$sizes["large"] = array(1024, 1024, true);
				$sizes["512"] = array(512, 384, false);
				$sizes["100"] = array(100, 75, false);
				
				$i = 0;
				foreach($sizes as $size) {
					$i++;
					$image = wp_get_image_editor($result["file"]);
					
					if (!is_wp_error($image)) {
							
						$imgSize = $image->get_size();
						if ($size[2]) {
							$imgSize = ImgUtils::resizeToProporcional($imgSize["width"], $imgSize["height"], $size[0], $size[1], $size[2]);
						} else {
							$imgSize["width"] = $size[0];
							$imgSize["height"] = $size[1];
						}

						$image->resize($imgSize["width"], $imgSize["height"], true);						
						$filename = $image->generate_filename($size[0]);
						$output = $image->save($filename);
						
						// Korekce cesty k souboru pro uložení do db
						$path = $this->getRelativePathToImg($output["path"]);
						$photos[$i] = $path;
					} else {
						array_push($this->messages, new JPErrorMessage("Pro fotografii '".$uploadFile['name']." se nepodařilo 
							vygenerovat náhled: ".$size[0].'x'.$size[1]." Chyba: ".$image->error));
					} 
				}
				
				// všechny náhledy vygenerovány?
				if (count($photos) == 6) {
					$photo = new stdClass();
					$photo->img_original = $photos[0];
					$photo->img_thumbnail = $photos[1];
					$photo->img_medium = $photos[2];
					$photo->img_large = $photos[3];
					$photo->img_512 = $photos[4];
					$photo->img_100 = $photos[5];
					$photo->objekt = $idObject;
					$photo->autor = $current_user->display_name;;
					$photo->primarni = 0;
					$photo->datum_nahrani = date('Y-m-d');
					
					if ($isFirst && !$existPhotos) {
						$photo->primarni = 1;
						$isFirst = false;	
					}
					
					$result = $this->dbPhoto->create($photo);
					if (!$result) {
						array_push($this->messages, new JPErrorMessage("Fotografii '".$uploadFile['name']." se nepodařilo uložit."));
					} else {
						array_push($newPhotos, $this->dbPhoto->getById($this->dbPhoto->getLastId()));							
					}
				}
			}
		}

		return $newPhotos;
		
	}

	private function addAuthor($idObject) {
		$author = $this->getAuthorFromAddForm();
		if ($author == null) {
			return null;	
		}
		
		$row = new stdClass();
		$row->objekt = $idObject;
		$row->autor = $author->id;
		
		$result = $this->dbObject2Author->create($row);
		if (!$result) {
			array_push($this->messages, new JPErrorMessage("Autora '".$author->jmeno."' se nepodařilo přidat k objektu."));
		}
	}
	
	private function addTags($idObject) {
		
		foreach ($this->getAllTags() as $tag) {
			$tagName = "tag".$tag->id;
			$value = filter_input (INPUT_POST, $tagName, FILTER_SANITIZE_STRING); 
			if ($value == "on") {
				
				$row = new stdClass();
				$row->objekt = $idObject;
				$row->stitek = $tag->id;

				$result = $this->dbObject2Tag->create($row);
				
				$this->cacheTagSelected = null;
			}
		}	
	}

	private function getRelativePathToImg($path) {
		$upload_dir = wp_upload_dir();
		$baseDir = $upload_dir['basedir']; 
		return str_replace($baseDir, "", $path);
	}
	
	public function manageAuthors() {
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
		foreach($authors as $author) {
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
			<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));
	}
	
	public function manageCollections() {
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
		foreach($collections as $collection) {
			if ($collection > 0) {
				$row = new stdClass();
				$row->objekt = $this->getObjectId();
				$row->soubor = $collection;
			
				$result = $this->dbObject2Collection->create($row);
			}
			
			$i++;
		}
		
		array_push($this->messages, new JPInfoMessage('Soubory děl byly úspěšně nastaveny. 
			<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));		
		
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
					$result = $this->dbSource->update($source, $source->id, true);
				} else {
					$result = $this->dbSource->create($source, true);
				}
			}
			
			array_push($this->messages, new JPInfoMessage('Zdroje byly aktualizovány. 
				<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));
				
			return $this->getSelectedSources();
		}
		
		return $sources;
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
				array_push($this->messages, new JPErrorMessage("Objekt se nepodařilo aktualizovat."));
			} else {
				array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně aktualizován. 
				<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));
			}
			
			// Nastavíme štítky
			$this->dbObject2Tag->deleteTagsForObject($this->getObjectFromUrl()->id);
			$this->addTags($this->getObjectFromUrl()->id);
		}
		
		return $row;
	}
	
	public function approve() {
		$object = $this->getObjectById($this->getObjectFromUrl()->id);
		if ($object == null) {
			return null;	
		}		
		
		$this->db->approveObject($this->getObjectFromUrl()->id);
		$object->schvaleno = 1;
		
		array_push($this->messages, new JPInfoMessage("Objekt byl úspěšně schválen."));
		
		return $object;
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
	
	private function validatePhotos() {
		// právě jedna fotka musí být hlavní (primární)
		$photos = $this->getPhotosForObject();
		$foundPrimary = false;
		$duplicatePrimary = false;
		
		foreach ($photos as $photo) {
			$id = "primarni".$photo->id;
			
			if (isset($_POST[$id])) {
				if (!$foundPrimary) {
					$foundPrimary = true;	
				} else {
					$duplicate = true;			
				}
			}
			
			$author = filter_input (INPUT_POST, "autor".$photo->id, FILTER_SANITIZE_STRING);
			if (strlen($author) > 255) {
				array_push($this->messages, new JPErrorMessage("Jméno autora nesmí být delší než 255 znaků."));
			}
			
			$url = filter_input (INPUT_POST, "url".$photo->id, FILTER_SANITIZE_STRING);
			if (strlen($url) > 255) {
				array_push($this->messages, new JPErrorMessage("Adresa (URL) u fotografie nesmí být delší než 255 znaků."));
			}
		}
		
		if (!$foundPrimary) {
			array_push($this->messages, new JPErrorMessage("Žádná z nahraných fotografií není zvolena jako hlavní."));
		}
		if ($duplicatePrimary) {
			array_push($this->messages, new JPErrorMessage("Jako hlavní fotografie smí být zvolena pouze jediná."));
		}	
	
		return count($this->messages) === 0;
	}
	
	private function refreshPhoto($photo) {
		$author = filter_input (INPUT_POST, "autor".$photo->id, FILTER_SANITIZE_STRING);
		$description = filter_input (INPUT_POST, "popis".$photo->id, FILTER_SANITIZE_STRING);
		$url = filter_input (INPUT_POST, "url".$photo->id, FILTER_SANITIZE_STRING);
		
		$id = "primarni".$photo->id;
		if (isset($_POST[$id])) {
			$primary = 1;	
		} else {
			$primary = 0;	
		}
		
		$id = "soukroma".$photo->id;
		if (isset($_POST[$id])) {
			$soukroma = 1;	
		} else {
			$soukroma = 0;	
		}
		
		$photo->autor = $author;
		$photo->popis = $description;
		$photo->primarni = $primary;
		$photo->soukroma = $soukroma;
		$photo->url = $url;
		
		return $photo;
	}
	
	public function managePhotos() {
		$id = $this->getObjectFromUrl()->id;
		if ($id == null) {
			return null;
		}
		
		// Nejdříve zaktualizujeme existující fotografie
		$photos = $this->getPhotosForObject();
		$newPhotos = array();
		if (count($photos) > 0) {
			if ($this->validatePhotos()) {
				foreach ($photos as $photo) {
						
					$idDelete = "delete".$photo->id; 
					if (isset($_POST[$idDelete])) {
						$this->dbPhoto->delete($photo->id);	
					} else {						 
						$photo = $this->refreshPhoto($photo);
						array_push ($newPhotos, $photo);
						$result = $this->dbPhoto->update($photo, $photo->id);
					}
				}
			} else {
				foreach ($photos as $photo) {
					$photo = $this->refreshPhoto($photo);
					array_push ($newPhotos, $photo);
				}
			}
		}
		
		// Nahrajeme nové fotografie
		$photos = $this->addPhotos($id, count($photos) > 0);
		if ($photos != null && count($photos) > 0) {
			foreach($photos as $photo) {
				array_push($newPhotos, $photo);
			}	
		}
		
		if (count($this->messages) == 0) {
			array_push($this->messages, new JPInfoMessage('Úprava fotografií byla dokončena. 
			<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail objektu</a>'));
		}
		
		return $newPhotos;
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
			array_push($this->messages, new JPInfoMessage('Objekt byl úspěšně smazán. 
				<a href="'.$this->getUrl(JPController::URL_LIST).'">Zpět na seznam</a>'));
			$this->dbPhoto->deletePhotosByObject($id);
		}
	}
	
	public function getPhotosForObject() {
		if ($this->getObjectId() == null) {
			return null;
		}
		
		return $this->dbPhoto->getPhotosByObject($this->getObjectId());
	}
	
	public function getPhotosForObjectMain() {
		$photos = $this->getPhotosForObject();
		
		foreach($photos as $photo) {
			if ($photo->primarni) {
				return $photo;	
			}
		}
		
		return null;
	}
	
	public function getPhotosForObjectNotMain() {
		$newPhotos = array ();
		$photos = $this->getPhotosForObject();
		
		foreach($photos as $photo) {
			if (!$photo->primarni) {
				array_push($newPhotos, $photo);	
			}
		}
		
		return $newPhotos;
		
	}
	
	public function getAllAuthors() {
		return $this->dbAuthor->getAll();
	}
	
	public function getAllCollections() {
		return $this->dbCollection->getAll();
	}	
	
	public function getAuthorsForObject() {
		if ($this->getObjectId() == null) {
			return null;
		}
		
		return $this->db->getAuthorsForObject($this->getObjectId());	
	}
	
	public function getCollectionsForObject() {
		if ($this->getObjectId() == null) {
			return null;
		}
		
		return $this->dbObject2Collection->getCollectionsForObject($this->getObjectId());	
	}	
	
	public function getAuthorsByObject($idObject) {
		return $this->db->getAuthorsForObject($idObject);	
	}
	
	
	public function getCooperationsForObject() {
		if ($this->getObjectId() == null) {
			return null;
		}
		
		return $this->db->getCooperationsForObject($this->getObjectId());	
	}
	
	public function getSourcesForObject() {
		if ($this->getObjectId() == null) {
			return null;
		}
		
		return $this->dbSource->getSourcesForObject($this->getObjectId());	
	}
	
	public function getSelectedAuthors() {
		$authors = array ();
		foreach($this->getAuthorsForObject() as $author) {
			array_push($authors, $author->id);
		}
		
		// doplníme ty nevyplněné (prázdné)
		for ($i = count($authors); $i < 3; $i++) {
			array_push($authors, 0);
		}
		
		return $authors;
	}
	
	public function getSelectedCollections() {
		$collections = array ();
		
		foreach($this->getCollectionsForObject() as $collection) {
			array_push($collections, $collection->id);
		}	
		
		// doplníme ty nevyplněné (prázdné)
		for ($i = count($collections); $i < 3; $i++) {
			array_push($collections, 0);
		}		
		
		return $collections;
	}	
	
	public function getCooperations() {
		$cooperations = array ();
		foreach($this->getCooperationsForObject() as $cooperation) {
			array_push($cooperations, $cooperation->spoluprace);
		}
		
		// doplníme ty nevyplněné (prázdné)
		for ($i = count($cooperations); $i < 3; $i++) {
			array_push($cooperations, "");
		}
		
		return $cooperations;
	}
	
	public function getSelectedSources() {
		$sources = array ();		
		foreach($this->getSourcesForObject() as $source) {
			array_push($sources, $source);
		}
		
		// doplníme pět dalších
		for ($i = 1; $i <= 5; $i++) {
			array_push($sources, 0);
		}
		
		return $sources;
	}
	
	private function getAuthorFromAddForm() {
		$idAuthor = (int) filter_input (INPUT_POST, "autor", FILTER_SANITIZE_STRING);
		if ($idAuthor <= 0) {
			return null;	
		}
		
		return $this->dbAuthor->getById($idAuthor);
	}
	
	private function getFormValues() {
		$row = new stdClass();
		$row->nazev = filter_input (INPUT_POST, "nazev", FILTER_SANITIZE_STRING);
		$row->latitude = (double) filter_input (INPUT_POST, "latitude", FILTER_SANITIZE_STRING);
		$row->longitude = (double) filter_input (INPUT_POST, "longitude", FILTER_SANITIZE_STRING);
		$row->kategorie = (int) filter_input (INPUT_POST, "kategorie", FILTER_SANITIZE_STRING);
		$row->popis = filter_input (INPUT_POST, "popis", FILTER_SANITIZE_STRING);
		$row->obsah = $_POST["editor"]; // TODO: sanitize 
		$row->interni = $_POST["interni"]; // TODO: sanitize
		$row->rok_vzniku = filter_input (INPUT_POST, "rok_vzniku", FILTER_SANITIZE_STRING);
		$row->prezdivka = filter_input (INPUT_POST, "prezdivka", FILTER_SANITIZE_STRING);
		$row->material = filter_input (INPUT_POST, "material", FILTER_SANITIZE_STRING);
		$row->pamatkova_ochrana = filter_input (INPUT_POST, "pamatkova_ochrana", FILTER_SANITIZE_STRING);
		$row->pristupnost = filter_input (INPUT_POST, "pristupnost", FILTER_SANITIZE_STRING);
		
		$row->zruseno = filter_input (INPUT_POST, "zruseno", FILTER_SANITIZE_STRING);
		$row->zruseno = ($row->zruseno === "on" ? 1 : 0);

		$row->zpracovano = filter_input (INPUT_POST, "zpracovano", FILTER_SANITIZE_STRING);
		$row->zpracovano = ($row->zpracovano === "on" ? 1 : 0);
		
		return $row;
	}
	
	private function getFormAuthorsValues() {
		$authors = array ();
		
		array_push($authors, (int) filter_input (INPUT_POST, "autor1", FILTER_SANITIZE_STRING));
		array_push($authors, (int) filter_input (INPUT_POST, "autor2", FILTER_SANITIZE_STRING));
		array_push($authors, (int) filter_input (INPUT_POST, "autor3", FILTER_SANITIZE_STRING));
		
		return $authors;
	}
	
	private function getFormCooperationsValues() {
		$cooperations = array ();
		
		array_push($cooperations, filter_input (INPUT_POST, "spoluprace1", FILTER_SANITIZE_STRING));
		array_push($cooperations, filter_input (INPUT_POST, "spoluprace2", FILTER_SANITIZE_STRING));
		array_push($cooperations, filter_input (INPUT_POST, "spoluprace3", FILTER_SANITIZE_STRING));
		
		return $cooperations;
	}
	
	private function getFormCollectionsValues() {
		$collections = array ();
		
		array_push($collections, (int) filter_input (INPUT_POST, "collection1", FILTER_SANITIZE_STRING));
		array_push($collections, (int) filter_input (INPUT_POST, "collection2", FILTER_SANITIZE_STRING));
		array_push($collections, (int) filter_input (INPUT_POST, "collection3", FILTER_SANITIZE_STRING));
		
		return $collections;
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
				$source->objekt = $this->getObjectId();
				$source->autor = null;

				array_push($sources, $source);
			}
		}
		
		return $sources;
	}
	
	public function getGoogleMapPointContent($lat, $lng) {
		$map = new GoogleMapsBuilder($KV_SETTINGS["gm_key"], $lat, $lng);
		return $map->getOutput();
	}
	
	public function getGoogleMapPointEditContent($lat, $lng) {
		$map = new GoogleMapsBuilder($KV_SETTINGS["gm_key"], $lat, $lng);
		return $map->getOutputEdit();
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

	public function getStringId() {
		return "object";	
	}
	
	public function getObjectId() {
		global $wp_query;
		
		$id = (int) $wp_query->query_vars['objekt'];
		if ($id == null) {
			return parent::getObjectId();
		}
		
		return $id;
	}
	
	public function getObjectCategory($id) {
		return $this->dbCategory->getById($id);	
	}
	
	public function getRandomObjectWithPhoto() {
		$count = $this->db->getCountObjectsWithPhotos();		
		$randNumber = rand(1, $count);

		return $this->db->getRandomObjectWithPhoto($randNumber);
	}
	
	public function getLastObjectWithPhoto() {
		return $this->db->getLastObjectWithPhoto();
	}
	
	public function getCatalogPage($page, $search) {
		return $this->db->getCatalogPage($page, $search, $this->getCurrentTag());		
	}
		
	public function getAuthorFullname($obj) {
		return trim($obj->titul_pred." ".$obj->jmeno." ".$obj->prijmeni." ".$obj->titul_za);	
	}		
	
	public function getCountKeSchvaleni() {
		return $this->db->getCountKeSchvaleni();	
	}
	
	public function getAllTags() {
		return $this->dbTag->getAll();	
	}
	
	public function getIsTagSelected($idTag) {
			
		$idObject = $this->getObjectId(); 
		if ($idObject == null) {
			return false;
		}

		if ($this->cacheTagSelected == null) {
			$this->cacheTagSelected = $this->dbObject2Tag->getTagsForObject($idObject);	
		}
		
		foreach($this->cacheTagSelected as $selectedTag) {
			if ($selectedTag->stitek == $idTag) {
				return true;	
			}
		}
		
		return false; 
	}
	
	public function getIsShowedTag() {
		global $wp_query;
		
		$id = (int) $wp_query->query_vars['stitek'];
		return $this->dbTag->getById($id) != null;
	}
	
	public function getCurrentTag() {
		global $wp_query;
		
		$id = (int) $wp_query->query_vars['stitek'];
		return $this->dbTag->getById($id);
	}
	
	public function getTagsForObject($idObject) {
		return $this->dbObject2Tag->getTagsForObject($idObject);
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

?>

