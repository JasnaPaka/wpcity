<?php
$ROOT = plugin_dir_path(__FILE__) . "../";

include_once $ROOT . "db/CategoryDb.php";
include_once $ROOT . "db/ObjectDb.php";

include_once $ROOT . "fw/JPMessages.php";
include_once $ROOT . "fw/JPController.php";
include_once $ROOT . "fw/GPXExporter.php";
include_once $ROOT . "fw/StringUtils.php";

/**
 * Kontroler pro stahování.
 */
class DownloadController extends JPController
{

	private $dbObject;
	private $dbCategory;

	function __construct()
	{
		$this->dbObject = new ObjectDb();
		$this->dbCategory = new CategoryDb();
	}

	public function getStringId()
	{
		return "download";
	}

	/**
	 * Hlavní akce na stahování
	 */
	public function download()
	{
		global $wp_query;

		$category_id = (int)$wp_query->query_vars["stahnout"];
		$action = filter_input(INPUT_GET, "filtr", FILTER_SANITIZE_STRING);

		$objects = array();
		if ($category_id > 0) {
			$category = $this->dbCategory->getById($category_id);
		}

		switch ($action) {
			case "bezfotografie":
				$objects = $this->dbObject->getObjectsWithNoPhotos();
				break;
			case "bezfotografieneexistujici":
				$objects = $this->dbObject->getObjectsWithNoPhotos(false, true);
				break;
			case "bezfotografieverejne":
				$objects = $this->dbObject->getObjectsWithNoPhotos(true);
				break;
			case "existujici":
				if ($category != null) {
					$objects = $this->dbObject->getListByCategory($category_id, "", false);
				} else {
					$objects = $this->dbObject->getList("", false);
				}
				break;
			default:

				if ($category != null) {
					$objects = $this->dbObject->getListByCategory($category_id, "", true);
				} else {
					$objects = $this->dbObject->getAllPublic();
				}
				break;
		}

		if (sizeof($objects) == 0) {
			return;
		}

		$exporter = new GPXExporter();
		foreach ($objects as $object) {
			$exporter->addPoi($object->latitude, $object->longitude, $object->nazev);
		}

		$exporter->download($this->getFilename($category_id, $action));
	}

	private function getFilename($category_id, $action)
	{
		$date = "-".date("Y-m-d");

		switch ($action) {
			case "bezfotografie":
				return "krizky-a-vetrelci-bez-fotografie".$date.".gpx";
				break;
			case "bezfotografieverejne":
				return "krizky-a-vetrelci-bez-fotografie-verejne".$date.".gpx";
				break;
			case "existujici":
				$category = $this->dbCategory->getById($category_id);
				if ($category != null) {
					return "kv-kategorie-" . $this->getCategoryNameForFile($category->nazev) . "-existujici".$date.".gpx";
				} else {
					return "krizky-a-vetrelci-existujici".$date.".gpx";
				}
				break;
			default:
				$category = $this->dbCategory->getById($category_id);
				if ($category != null) {
					return "kv-kategorie-" . $this->getCategoryNameForFile($category->nazev) .$date.".gpx";
				} else {
					return "krizky-a-vetrelci".$date.".gpx";
				}
				break;
		}
	}

	private function getCategoryNameForFile($name)
	{
		// TODO: Rozchodit časem implementaci. Zlobí diakritika.
		//$name = iconv("utf-8", "us-ascii//TRANSLIT", $name);
		$name = str_replace(" ", "-", $name);
		return $name;
	}
}