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
	
	public function update($data, $id) {
		global $wpdb;
		
		$values = array (
			"nazev" => $data->nazev,
			"url" => $data->url,
			"ikona" => $data->ikona,
		);
		
		$types = array (
			'%s',
			'%s',			
			'%s'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
	
	/**
	 * Vrací počet objektů v konkrétní kategorii.
	 */
	public function getCountObjectsInCategory($idCategory) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM kv_objekt WHERE kategorie = %d AND deleted = 0 AND schvaleno = 1", $idCategory);
		return $wpdb->get_var($sql);
	}
	
}

?>