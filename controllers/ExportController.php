<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/ObjectDb.php";
include_once $ROOT."db/PhotoDb.php";
include_once $ROOT."db/CategoryDb.php";

/**
 * Nástroje na export
 */
class ExportController extends JPController {
	
	private $dbObject;
	private $dbPhoto;
	private $dbCategory;
	
	function __construct() {
		$this->dbObject = new ObjectDb();
		$this->dbPhoto = new PhotoDb();
		$this->dbCategory = new CategoryDB();
	}

	/**
	 * Vrátí název akce exportu nebo null, pokud žádná není.
	 */
	private function getAction() {
		if (isset($_GET["action"])) {
			return filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
		}
		
		return null;
	}


	/**
	 * Hlavní metoda, která rozhoduje na základě parametrů v URL, jaký export se chce.
	 */
	public function export() {
		$id = (int) filter_input (INPUT_GET, "id", FILTER_SANITIZE_STRING);
		
		switch($this->getAction()) {
			case "nophotos":
				$this->exportNoPhotos();
				break;
			case "category":			
				$this->exportCategory($id, false);
				break;
			case "categoryWithCanceled": 
				$this->exportCategory($id, true);
				break;
			case "categoryNoAuthors":
				$this->exportNoAuthors($id);
				break;
			case "img_512":
				$this->rebuildImages512();
				break;
			case "img_100":
				$this->rebuildImages100();
				break;
			default:
				if (strpos($this->getAction(), "category") == 0) {
					$id = str_replace($this->getAction(), "category", "");
				} else {
					printf("Nedefinovana akce.");
					break;					
				}				
		}
	}
	
	/**
	 * Export objektů bez fotek do CSV.
	 */
	private function exportNoPhotos() {
		$separator = ",";	

		putenv('TMPDIR='.getenv('TMPDIR'));
		$tmpName = ini_get('upload_tmp_dir')."/objekty-bez-fotek";
		
		$file = fopen($tmpName, 'w');

		fwrite($file, "name".$separator."latitude".$separator."longitude\n");

		foreach($this->dbObject->getObjectsWithNoPhotos() as $obj) {
			$str = $obj->nazev.$separator.$obj->latitude.$separator.$obj->longitude."\n";
			fwrite($file, $str);
		}
		
		fclose($file);
		
		$this->download($tmpName, "objekty-bez-fotek.csv");
	}
	
	/**
	 * Export bodů bez uvedeného autora do CSV.
	 * 
	 * @param unknown $id
	 */
	private function exportNoAuthors($id) {
		$separator = ",";
		
		putenv('TMPDIR='.getenv('TMPDIR'));
		$tmpName = ini_get('upload_tmp_dir')."/objekty-bez-autora";
		
		$file = fopen($tmpName, 'w');
		
		fwrite($file, "name".$separator."latitude".$separator."longitude\n");
		
		foreach($this->dbObject->getObjectsWithoutAuthors($id) as $obj) {
			$str = $obj->nazev.$separator.$obj->latitude.$separator.$obj->longitude."\n";
			fwrite($file, $str);
		}
		
		fclose($file);
		
		$this->download($tmpName, "objekty-bez-autora.csv");
	}
	
	/**
	 * Export objektů kategorie do CSV.
	 */
	private function exportCategory($categoryId, $withCanceled) {
				$separator = ",";	
				
		$category = $this->dbCategory->getById($categoryId);
		if ($category == null) {
			printf("Kategorie pod zadaným ID nebyla nalezena.");
			return;
		}

		putenv('TMPDIR='.getenv('TMPDIR'));
		$tmpName = ini_get('upload_tmp_dir')."/objekty-kategorie";
		
		$file = fopen($tmpName, 'w');

		fwrite($file, "name".$separator."latitude".$separator."longitude\n");

		foreach($this->dbObject->getListByCategory($categoryId, "", $withCanceled) as $obj) {
			// čárky používáme jako oddělovač, v názvu tak dělají neplechu
			$nazev = str_replace(",", "", $obj->nazev);
			
			$str = $nazev.$separator.$obj->latitude.$separator.$obj->longitude."\n";
			fwrite($file, $str);
		}
		
		fclose($file);
		
		$nazev = "objekty-".str_replace(" ", "-", $category->nazev).".csv";
		$this->download($tmpName, $nazev);	
	}
	
	
	
	/**
	 * Nabídnutí vygenerovaného souboru ke stažení. Parametrem je název vygenerovaného souboru na 
	 * disku a název souboru, jak se má na výstupu jmenovat.
	 */
	private function download($tmpName, $filename) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$filename);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($tmpName));
		ob_clean();
		flush();
		readfile($tmpName);
		
		unlink($tmpName);
	}

	public function getStringId() {
		return "export";	
	}
	
	private function rebuildImages512() {
		global $wpdb;
		
		$images = $this->dbPhoto->getPhotosWithoug512();
		
		foreach ($images as $image) {
			$editor = wp_get_image_editor(ABSPATH."wp-content/uploads/sites/".$wpdb->blogid.$image->img_original);
			if (!is_wp_error($editor)) {
				$editor->resize(512, 384, true);
				
				$fn = $editor->generate_filename(512);
				$output = $editor->save($fn);
						
				$path = $this->getRelativePathToImg($output["path"]);

				$image->img_512 = $path;
				$this->dbPhoto->update($image, $image->id);
			}
		}
	}
	
	private function rebuildImages100() {
		global $wpdb;
		
		$images = $this->dbPhoto->getPhotosWithoug100();
		
		foreach ($images as $image) {
			$editor = wp_get_image_editor(ABSPATH."wp-content/uploads/sites/".$wpdb->blogid.$image->img_original);
			if (!is_wp_error($editor)) {
				$editor->resize(100, 75, true);
				
				$fn = $editor->generate_filename(100);
				$output = $editor->save($fn);
						
				$path = $this->getRelativePathToImg($output["path"]);

				$image->img_100 = $path;
				$this->dbPhoto->update($image, $image->id);
			}
		}
	}	
	
	private function getRelativePathToImg($path) {
		$upload_dir = wp_upload_dir();
		$baseDir = $upload_dir['basedir']; 
		return str_replace($baseDir, "", $path);
	}
	
	public function getCategories() {
		return $this->dbCategory->getAll();		
	}	
	
}


?>