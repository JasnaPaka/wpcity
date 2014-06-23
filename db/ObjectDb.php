<?php

class ObjectDb extends JPDb {
	
	protected $tableName = "kv_objekt";
	
	public function getDefaultOrder() {
		return "nazev";
	}
	
	public function getCountObjectsInCategory($idCategory) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM kv_objekt WHERE kategorie = %d AND deleted = 0", $idCategory); 	
		return $wpdb->get_var ($sql);
	}
	
	public function getListByNazev($nazev) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE nazev LIKE %s AND deleted = 0 ORDER BY nazev", '%'.$nazev.'%');
		return $wpdb->get_results ($sql);
	}
	
	public function getPageByNazev($page, $nazev) {
		global $wpdb;
		
		$page--;
		$offset = $page * JPDb::MAX_ITEMS_ON_PAGE;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE nazev LIKE %s AND deleted = 0 
						ORDER BY nazev LIMIT ".JPDb::MAX_ITEMS_ON_PAGE." OFFSET ".$offset, '%'.$nazev.'%');
		return $wpdb->get_results ($sql); 
	}
	
	public function getCountByNazev($nazev) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM ".$this->tableName." WHERE nazev LIKE %s AND deleted = 0", '%'.$nazev.'%');
		return $wpdb->get_var ($sql); 
	}
	
		
}

?>