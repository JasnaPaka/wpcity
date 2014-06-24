<?php

class PhotoDb extends JPDb {
	
	protected $tableName = "kv_fotografie";
	
	public function getDefaultOrder() {
		return "id";
	}
	
	public function deletePhotosByObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("UPDATE ".$tableName." SET deleted = 1 WHERE object = %d", $idObject);
		return $wpdb->query($sql);
	}
	
	public function getPhotosByObject($idObject) {
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE objekt = %d ORDER BY ".$this->getDefaultOrder(), $idObject); 
		return $wpdb->get_results ($sql);	
	}
	
}


?>
	