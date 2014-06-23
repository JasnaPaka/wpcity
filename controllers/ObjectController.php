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
	
	public function getList() {
		if (!$this->getSearchValueValid()) {
			return parent::getList();	
		}
		
		if ($this->getShowAll()) {
			return $this->db->getListByNazev($this->getSearchValue());
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
	
	
	public function add() {
		$row = $this->getFormValues();
		
		$result = $this->validate($row);
		if ($result) {
			$result = $this->db->create($row);
			if (!$result) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se uložit nový objekt."));
			} else {
				array_push($this->messages, new JPInfoMessage("Objekt byl úspěšně přidán."));
				
				// Záznam přidán, nyní přidáme fotky (když selže, tak to jen uživateli oznámíme)
				$this->addPhotos();
				
				return new stdClass();
			}
		}
		
		return $row;
	}
	
	private function addPhotos() {
		global $_wp_additional_image_sizes;
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
		
		foreach ($uploadFiles as $uploadFile) {
			
			$result = wp_handle_upload($uploadFile, $upload_overrides);
			if ($result["error"] != null) {
				array_push($this->messages, new JPErrorMessage("Při nahrávání fotky '".$uploadFile["name"]."' nastala chyba, 
					díky které fotka nebyla k objektu nahrána. Chyba: ".$result["error"]));
			} else {
				
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
				
				foreach($sizes as $size) {
					
				}
				
				/*$image = wp_get_image_editor($result["file"]);
				if (!is_wp_error($image)) {
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
			 
			 		foreach($sizes as $size){
						$image->resize($size[0], $size[1], true );
			 		}
						
				} else {
					array_push($this->messages, new JPErrorMessage("Při vytváření náhledů se vyskytla chyba.
						 Chyba: ".$image->get_error_message()));
				}*/	
			}
		}
		
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