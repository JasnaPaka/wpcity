<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/ObjectDb.php";

/**
 * Nástroje na export
 */
class ExportController extends JPController {
	
	private $dbObject;
	
	function __construct() {
		$this->dbObject = new ObjectDb();
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
		switch($this->getAction()) {
			case "nophotos":
				$this->exportNoPhotos();
				break;
			case "aliens":
				$this->exportAliens();
				break;
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
	 * Export objektů kategorie Vetřelci a volavky do CSV.
	 */
	private function exportAliens() {
				$separator = ",";	

		putenv('TMPDIR='.getenv('TMPDIR'));
		$tmpName = ini_get('upload_tmp_dir')."/objekty-vetrelci-volavky";
		
		$file = fopen($tmpName, 'w');

		fwrite($file, "name".$separator."latitude".$separator."longitude\n");

		foreach($this->dbObject->getObjectsAliens() as $obj) {
			$str = $obj->nazev.$separator.$obj->latitude.$separator.$obj->longitude."\n";
			fwrite($file, $str);
		}
		
		fclose($file);
		
		$this->download($tmpName, "objekty-vetrelci-volavky.csv");
	
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
}


?>