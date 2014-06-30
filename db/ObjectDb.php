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
	
	public function update($data, $id) {
		global $wpdb;
		
		$values = array (
			"nazev" => $data->nazev,
			"latitude" => $data->latitude,
			"longitude" => $data->longitude,
			"kategorie" => $data->kategorie,
			"obsah" => $data->obsah,
			"rok_vzniku" => $data->rok_vzniku,
			"prezdivka" => $data->prezdivka,
			"material" => $data->material,
			"pamatkova_ochrana" => $data->pamatkova_ochrana,
			"pristupnost" => $data->pristupnost,
			"pridal_autor" => $data->pridal_autor,
			"pridal_datum" => $data->pridal_datum,
			"upravil_autor" => $data->upravil_autor,
			"upravil_datum" => $data->upravil_datum
		);
		
		$types = array (
			'%s',
			'%f',
			'%f',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
	
	public function getAuthorsForObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT aut.* FROM kv_autor aut INNER JOIN kv_objekt2autor o2s ON aut.id = o2s.autor WHERE o2s.objekt = %d ORDER BY o2s.id", $idObject);
		return $wpdb->get_results ($sql); 
	}
		
	
		
}

?>