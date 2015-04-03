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
	
	public function getObjectsInCollection($idCollection) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT obj.* FROM ".$this->tableName." o2c INNER JOIN ".$this->dbPrefix."objekt obj ON o2c.objekt = obj.id
			  WHERE o2c.soubor = %d AND o2c.deleted = 0 ORDER BY ".$this->getDefaultOrder(), $idCollection);
		return $wpdb->get_results($sql);			
	}	
	
	public function getCollectionsForObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT s.* FROM ".$this->tableName." o2c INNER JOIN ".$this->dbPrefix."soubor s ON o2c.soubor = s.id 
			WHERE o2c.objekt = %d AND o2c.deleted = 0", $idObject);
			
		return $wpdb->get_results($sql);			
	}
		
	public function deleteOldRelationsForObject($idObject) {
		global $wpdb;
		
		return $wpdb->update($this->tableName, array ("deleted" => 1), array("objekt" => $idObject), array ('%d'));
	}		
		
}
		
?>	