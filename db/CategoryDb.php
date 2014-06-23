<?php

class CategoryDb extends JPDb {
	
	protected $tableName = "kv_kategorie";
	
	public function getDefaultOrder() {
		return "nazev";
	}
	
	public function getByUrl($url) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM kv_kategorie WHERE url = %s AND deleted = 0", $url); 
		$rows = $wpdb->get_results ($sql);
		if (count($rows) === 0) {
			return null;
		}
		
		return $rows[0];
	}
	
}

?>