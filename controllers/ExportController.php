<?php

$ROOT = plugin_dir_path(__FILE__) . "../";

include_once $ROOT . "fw/JPMessages.php";
include_once $ROOT . "fw/JPController.php";
include_once $ROOT . "fw/CityService.php";

include_once $ROOT . "db/ObjectDb.php";
include_once $ROOT . "db/PhotoDb.php";
include_once $ROOT . "db/CategoryDb.php";

/**
 * Nástroje na export
 */
class ExportController extends JPController
{

	private $dbObject;
	private $dbPhoto;
	private $dbCategory;
	private $dbSetting;
	private $dbAuthor;
	private $dbSource;

	function __construct()
	{
		$this->dbObject = new ObjectDb();
		$this->dbPhoto = new PhotoDb();
		$this->dbCategory = new CategoryDB();
		$this->dbSetting = new SettingDB();
		$this->dbAuthor = new AuthorDb();
		$this->dbSource = new SourceDb();
	}

	/**
	 * Vrátí název akce exportu nebo null, pokud žádná není.
	 */
	private function getAction()
	{
		if (isset($_GET["action"])) {
			return filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
		}

		return null;
	}


	/**
	 * Hlavní metoda, která rozhoduje na základě parametrů v URL, jaký export se chce.
	 */
	public function export()
	{
		$id = (int)filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);

		switch ($this->getAction()) {
			case "nophotos":
				$this->exportNoPhotos();
				break;
			case "nophotosPublic":
				$this->exportNoPhotos(true);
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
			case "importadres":
				$this->importadres();
				break;
			case "newPhotoRequired":
				$this->newPhotoRequired();
				break;
			case "newPhotoRequired2":
				$this->newPhotoRequired2();
				break;
            case "exportAuthorsWikidata":
                $this->exportAuthorsWikidata();
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
	 * @param bool $public zda se mají exportovat pouze veřejné objekty
	 */
	private function exportNoPhotos($public = false)
	{
		$separator = ",";

		$tmpName = sys_get_temp_dir() . "/objekty-bez-fotek"  + random_int(1, 50000);

		$file = fopen($tmpName, 'w');

		fwrite($file, "name" . $separator . "latitude" . $separator . "longitude\n");

		foreach ($this->dbObject->getObjectsWithNoPhotos($public) as $obj) {
			$str = $this->printString($obj->nazev) . $separator . $obj->latitude . $separator . $obj->longitude . "\n";
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
	private function exportNoAuthors($id)
	{
		$separator = ",";

		$tmpName = sys_get_temp_dir() . "/objekty-bez-autora"  + random_int(1, 50000);

		$file = fopen($tmpName, 'w');

		fwrite($file, "name" . $separator . "latitude" . $separator . "longitude\n");

		foreach ($this->dbObject->getObjectsWithoutAuthors($id) as $obj) {
			$str = $this->printString($obj->nazev) . $separator . $obj->latitude . $separator . $obj->longitude . "\n";
			fwrite($file, $str);
		}

		fclose($file);

		$this->download($tmpName, "objekty-bez-autora.csv");
	}

	/**
	 * Export objektů kategorie do CSV.
	 * @param $categoryId
	 * @param $withCanceled
	 */
	private function exportCategory($categoryId, $withCanceled)
	{
		$separator = ",";

		$category = $this->dbCategory->getById($categoryId);
		if ($category == null) {
			printf("Kategorie pod zadaným ID nebyla nalezena.");
			return;
		}

		$tmpName = sys_get_temp_dir() . "/objekty-kategorie"  + random_int(1, 50000);

		$file = fopen($tmpName, 'w');

		fwrite($file, "name" . $separator . "latitude" . $separator . "longitude\n");

		foreach ($this->dbObject->getListByCategory($categoryId, "", $withCanceled) as $obj) {
			$str = $this->printString($obj->nazev) . $separator . $obj->latitude . $separator . $obj->longitude . "\n";
			fwrite($file, $str);
		}

		fclose($file);

		$nazev = "objekty-" . str_replace(" ", "-", $category->nazev) . ".csv";
		$this->download($tmpName, $nazev);
	}


	/**
	 * Nabídnutí vygenerovaného souboru ke stažení. Parametrem je název vygenerovaného souboru na
	 * disku a název souboru, jak se má na výstupu jmenovat.
	 */
	private function download($tmpName, $filename)
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($tmpName));
		ob_clean();
		flush();
		readfile($tmpName);

		unlink($tmpName);
	}

	public function getStringId()
	{
		return "export";
	}

	private function rebuildImages512()
	{
		global $wpdb;

		$images = $this->dbPhoto->getPhotosWithoug512();

		foreach ($images as $image) {
			$editor = wp_get_image_editor(ABSPATH . "wp-content/uploads/sites/" . $wpdb->blogid . $image->img_original);
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

	private function rebuildImages100()
	{
		global $wpdb;

		$images = $this->dbPhoto->getPhotosWithoug100();

		foreach ($images as $image) {
			$editor = wp_get_image_editor(ABSPATH . "wp-content/uploads/sites/" . $wpdb->blogid . $image->img_original);
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

	/**
	 * Nastavení městských částí a čtvrtí u děl, kde tento údaj chybí na základě
	 * údajů z Google Geocode.
	 */
	private function importadres()
	{
		$objects = $this->dbObject->getObjectsWithoutLocation();
		$controller = new ObjectController();

		$i = 0;
		foreach ($objects as $object) {
			$i++;

			$controller->updateLocation($object->id);

			if ($i == 200) {
				break;
			}
		}
	}

	private function getRelativePathToImg($path)
	{
		$upload_dir = wp_upload_dir();
		$baseDir = $upload_dir['basedir'];
		return str_replace($baseDir, "", $path);
	}

	public function getCategories()
	{
		return $this->dbCategory->getAll();
	}

	/**
	 * Provede ošetření řetězce při sestavování CSV. Ošetřuje hlavně čárku, která
	 * je používána jako oddělovač jednotlivých sloupců CSV.
	 *
	 * @param type $str
	 */
	private function printString($str)
	{
		if (strpos($str, ",") === FALSE) {
			return $str;
		} else {
			return "\"" . $str . "\"";
		}
	}

	/**
	 * Exportuje do CSV díla, kde je hlavní fotografie na výšku.
	 */
	public function newPhotoRequired() {
		global $wpdb;
		$separator = ",";

		$tmpName = sys_get_temp_dir() . "/objekty-foto-na-vysku".random_int(1, 50000);

		$file = fopen($tmpName, 'w');

		fwrite($file, "name" . $separator . "latitude" . $separator . "longitude\n");

		foreach ($this->dbObject->getObjectsWithPrimaryPhoto() as $obj) {
			$path = ABSPATH . "wp-content/uploads/sites/" . $wpdb->blogid . $obj->img_original;
			if (!file_exists($path)) {
				continue;
			}

			$info = getimagesize($path);
			if (!$info) {
				continue;
			}

			if ($info[1] > $info[0]) {
				$str = $this->printString($obj->nazev) . $separator . $obj->latitude . $separator . $obj->longitude . "\n";
				fwrite($file, $str);
			}

		}

		fclose($file);

		$nazev = "objekty-foto-na-vysku.csv";
		$this->download($tmpName, $nazev);
	}

	/**
	 * Exportuje do CSV díla, která jsou označena na přefocení.
	 */
	public function newPhotoRequired2() {
		$separator = ",";

		$tmpName = sys_get_temp_dir(). "/objekty-prefoceni".random_int(1, 50000);
		$file = fopen($tmpName, 'w');

		fwrite($file, "name" . $separator . "latitude" . $separator . "longitude\n");

		foreach ($this->dbObject->getObjectsNeedNewPhoto() as $obj) {
			$str = $this->printString($obj->nazev) . $separator . $obj->latitude . $separator . $obj->longitude . "\n";
			fwrite($file, $str);
		}

		fclose($file);

		$nazev = "objekty-prefoceni.csv";
		$this->download($tmpName, $nazev);
	}

	public function exportAuthorsWikidata() {
        global $wpdb;
        $separator = ",";

        $tmpName = sys_get_temp_dir() . "/authors-wikidata".random_int(1, 50000);

        $file = fopen($tmpName, 'w');
        $authors = $this->dbAuthor->getAuthorsWithoutWD();

        fwrite($file, "id" . $separator."počet" . $separator . "jméno" . $separator . "příjmení" . $separator. "datum narození"
            . $separator. "datum úmrtí" . $separator. "místo narození" . $separator. "místo úmrtí". $separator."abART\n");

        foreach ($authors as $author) {
            $sources = $this->dbSource->getSourcesForAuthor($author->id, false);
            $nalezeno = false;
            $abArt = null;

            foreach ($sources as $source) {
                if ($source->typ == SourceTypes::CODE_WIKIDATA) {
                    $nalezeno = true;
                }
                if ($source->typ == SourceTypes::CODE_ABART) {
                    $abArt = $source->identifikator;
                }
            }

            if ($nalezeno) {
                continue;
            }

            if ($author->datum_narozeni == null) {
                continue;
            }

            fwrite($file, ($author->id + 100000) . $separator .$author->pocet . $separator . $author->jmeno . $separator . $author->prijmeni . $separator . $author->datum_narozeni .
                $separator . $author->datum_umrti . $separator . $author->misto_narozeni . $separator . $author->misto_umrti . $separator . ($abArt == null ? "" : $abArt) ."\n");
        }

        fclose($file);

        $nazev = "wikidata-authors.csv";
        $this->download($tmpName, $nazev);
    }

}
