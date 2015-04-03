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
		
		$sql = $wpdb->prepare("SELECT DISTINCT obj.*, img_512 FROM ".$this->tableName." o2c INNER JOIN ".$this->dbPrefix."objekt obj ON o2c.objekt = obj.id
			LEFT JOIN ".$this->dbPrefix."fotografie fot ON fot.objekt = obj.id
			  WHERE o2c.soubor = %d AND o2c.deleted = 0 AND obj.deleted = 0
			   AND obj.schvaleno = 1 AND (fot.deleted = 0 OR fot.deleted IS NULL) AND (fot.primarni = 1 OR fot.primarni IS NULL)
			  ORDER BY obj.nazev", $idCollection);
			  
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
	
	public function getImgForCollection($idCollection) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT fot.img_512 FROM ".$this->tableName." o2c INNER JOIN ".$this->dbPrefix."objekt obj ON o2c.objekt = obj.id
			INNER JOIN ".$this->dbPrefix."fotografie fot ON fot.objekt = obj.id
			  WHERE o2c.soubor = %d AND o2c.deleted = 0 AND obj.deleted = 0 AND obj.schvaleno = 1 AND (fot.deleted = 0 OR fot.deleted IS NULL) AND (fot.primarni = 1 OR fot.primarni IS NULL) 
			   ORDER BY obj.nazev"." LIMIT 1", $idCollection);

		return $wpdb->get_var($sql);
	}
		
		
}
		
?>	