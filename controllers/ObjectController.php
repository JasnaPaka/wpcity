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
include_once $ROOT."db/Object2AuthorDb.php";

include_once $ROOT."config.php";

class ObjectController extends JPController {
	
	protected $db;
	private $dbCategory;
	private $dbPhoto;
	private $dbAuthor;
	private $dbObject2Author;
	
	private $categories;
	
	function __construct() {
		$this->db = new ObjectDb();
		$this->dbCategory = new CategoryDb();
		$this->dbPhoto = new PhotoDb();
		$this->dbAuthor = new AuthorDb();
		$this->dbObject2Author = new Object2AuthorDb();
	}
	
	public function getList() {
		if (!$this->getSearchValueValid()) {
			if ($this->getSearchValue() != null) {
				array_push($this->messages, new JPErrorMessage("Hledaný výraz musí mít alespoň tři znaky."));
			}
			
			return parent::getList();	
		}
		
		if ($this->getShowAll()) {
			return $this->db->getListByNazev($this->getSearchValue());
		} else {
			return $this->db->getPageByNazev($this->getPageCurrent(), $this->getSearchValue());	
		}
		
		return $this->db->getListByNazev($this->getSearchValue(), $this->getCurrentOrder());
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
		$this->addInternal(true);
	}
	
	public function add() {
		$this->addInternal(false);
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
				foreach (get_intermediate_image_sizes() as $s) {
					$sizes[ $s ] = array( 0, 0 );
		 			if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
		 				$sizes[ $s ][0] = get_option( $s . '_size_w' );
		 				$sizes[ $s ][1] = get_option( $s . '_size_h' );
		 			}else{
		 				if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
		 					$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
		 			}
		 		}
				
				$i = 0;
				foreach($sizes as $size) {
					$i++;
					$image = wp_get_image_editor($result["file"]);
					
					if (!is_wp_error($image)) {
							
						$imgSize = $image->get_size();
						$imgSize = ImgUtils::resizeToProporcional($imgSize["width"], $imgSize["height"], $size[0], $size[1]);

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
				if (count($photos) == 4) {
					$photo = new stdClass();
					$photo->img_original = $photos[0];
					$photo->img_thumbnail = $photos[1];
					$photo->img_medium = $photos[2];
					$photo->img_large = $photos[3];
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

	private function getRelativePathToImg($path) {
		$upload_dir = wp_upload_dir();
		$baseDir = $upload_dir['basedir']; 
		return str_replace($baseDir, "", $path);
	}
	
	public function manageAuthors() {
		$authors = $this->getFormAuthorsValues();
		
		// Validace
		foreach ($authors as $author) {
			if ($author > 0 && $this->dbAuthor->getById($author) == null) {
				array_push($this->messages, new JPErrorMessage("Jeden ze zvolených autorů nebyl nalezen. Patrně byl před chvílí smazán."));
				return;
			}
		}
		
		// Smazání starých vazeb a vytvoření nových
		$this->dbObject2Author->deleteOldRelationsForObject($this->getObjectId());
		foreach($authors as $author) {
			if ($author > 0) {
				$row = new stdClass();
				$row->objekt = $this->getObjectId();
				$row->autor = $author;
			
				$result = $this->dbObject2Author->create($row);
			}
		}
		
		array_push($this->messages, new JPInfoMessage('Autoři byli úspěšně nastaveni. 
			<a href="'.$this->getUrl(JPController::URL_VIEW).'">Zobrazit detail</a>'));
		
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
		}
		
		return $row;
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
		
		$id = "primarni".$photo->id;
		if (isset($_POST[$id])) {
			$primary = 1;	
		} else {
			$primary = 0;	
		}
		
		$photo->autor = $author;
		$photo->popis = $description;
		$photo->primarni = $primary;
		
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
	
	public function getAllAuthors() {
		return $this->dbAuthor->getAll();
	}
	
	public function getAuthorsForObject() {
		if ($this->getObjectId() == null) {
			return null;
		}
		
		return $this->db->getAuthorsForObject($this->getObjectId());	
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
		$row->rok_vzniku = filter_input (INPUT_POST, "rok_vzniku", FILTER_SANITIZE_STRING);
		$row->prezdivka = filter_input (INPUT_POST, "prezdivka", FILTER_SANITIZE_STRING);
		$row->material = filter_input (INPUT_POST, "material", FILTER_SANITIZE_STRING);
		$row->pamatkova_ochrana = filter_input (INPUT_POST, "pamatkova_ochrana", FILTER_SANITIZE_STRING);
		$row->pristupnost = filter_input (INPUT_POST, "pristupnost", FILTER_SANITIZE_STRING);
		
		return $row;
	}
	
	private function getFormAuthorsValues() {
		$authors = array ();
		
		array_push($authors, (int) filter_input (INPUT_POST, "autor1", FILTER_SANITIZE_STRING));
		array_push($authors, (int) filter_input (INPUT_POST, "autor2", FILTER_SANITIZE_STRING));
		array_push($authors, (int) filter_input (INPUT_POST, "autor3", FILTER_SANITIZE_STRING));
		
		return $authors;
	}
	
	public function getGoogleMapPointContent($lat, $lng) {
		$map = new GoogleMapsBuilder($KV_SETTINGS["gm_key"], $lat, $lng);
		return $map->getOutput();
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
		
}

?>