<?php

class CategoryDb {
	
	public function getAll() {
		global $wpdb;
		
		return $wpdb->get_results("SELECT * FROM kv_kategorie ORDER BY nazev");	
	}
	
	public function getByUrl($url) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM kv_kategorie WHERE url = %s", $url); 
		$rows = $wpdb->get_results ($sql);
		if (count($rows) === 0) {
			return null;
		}
		
		return $rows[0];
	}
	
	public function getById($id) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM kv_kategorie WHERE id = %d", $id); 
		$rows = $wpdb->get_results ($sql);
		if (count($rows) === 0) {
			return null;
		}
		
		return $rows[0];
	}
	
	public function create($data) {
		global $wpdb;
		
		return $wpdb->insert("kv_kategorie", (array) $data);
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$data->id = $id;
		return $wpdb->replace("kv_kategorie", (array) $data);
	}
	
	public function delete($id) {
		global $wpdb;
		
		return $wpdb->delete('kv_kategorie', array('id' => $id));
	}
}

?>