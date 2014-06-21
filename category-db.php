<?php

class CategoryDb {
	
	/**
	 * Vrátí všechny kategorie seřazené podle názvu.
	 */
	public function getAll() {
		global $wpdb;
		
		return $wpdb->get_results("SELECT * FROM kv_kategorie ORDER BY nazev");	
	}
	
	public function findByUrl($url) {
		global $wpdb;
		
		$wpdb->show_errors();
		
		
		$sql = $wpdb->prepare("SELECT * FROM kv_kategorie WHERE url = %s", $url); 
		return $wpdb->get_results ($sql);
	}
	
	public function create($data) {
		global $wpdb;
		
		return $wpdb->insert("kv_kategorie", $data);
	}
}

?>