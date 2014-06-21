<?php

class ObjectDb {

	public function getAll() {
		global $wpdb;
		
		return $wpdb->get_results("SELECT * FROM kv_objekt ORDER BY nazev");	
	}

	
	public function getCountObjectsInCategory($idCategory) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM kv_objekt WHERE kategorie = %d", $idCategory); 	
		return $wpdb->get_var ($sql);
	}

	public function create($data) {
		global $wpdb;
		
		return $wpdb->insert("kv_objekt", (array) $data);
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$data->id = $id;
		return $wpdb->replace("kv_objekt", (array) $data);
	}
	
	public function delete($id) {
		global $wpdb;
		
		return $wpdb->delete('kv_objekt', array('id' => $id));
	}
	
	public function getById($id) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM kv_objekt WHERE id = %d", $id); 
		$rows = $wpdb->get_results ($sql);
		if (count($rows) === 0) {
			return null;
		}
		
		return $rows[0];
	}
	
}

?>