<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/ObjectDb.php";
include_once $ROOT."db/PhotoDb.php";

class GeneralController extends JPController {
	
	protected $db;	
	
	function __construct() {
		$this->db = new ObjectDb();
	}

	public function getStringId() {
		return "general";	
	}
	
	public function getCountKeSchvaleni() {
		return $this->db->getCountKeSchvaleni();	
	}
	
	public function getNeschvaleneList() {
		return $this->db->getNeschvaleneList();	
	}

}
