<?php

class Object2CollectionDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."objekt2soubor";
	}
	
	public function getDefaultOrder() {
		return "id";
	}
	
	public function getCountObjectsInCollection($idCollection) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM ".$this->tableName." WHERE soubor = %d AND deleted = 0", $idCollection);
		return $wpdb->get_var($sql);			
	}
		
}
		
?>	