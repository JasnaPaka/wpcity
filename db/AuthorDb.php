<?php

class AuthorDb extends JPDb {
	
	protected $tableName = "kv_autor";
	
	public function getDefaultOrder() {
		return "prijmeni, jmeno";
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$sql = "UPDATE ".$this->tableName." SET jmeno = %s, prijmeni = %s, obsah = %s WHERE id = %d";
		$sql = $wpdb->prepare($sql, $data->jmeno, $data->prijmeni, $data->obsah, $id);
		$result = $wpdb->query($sql);
		
		echo $data->id;
		
		// aktualizace data narození
		if (isset($data->datum_narozeni)) {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_narozeni = %s WHERE id = %d", $data->datum_narozeni, $id);
			$result = $wpdb->query($sql);			
		}  else {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_narozeni = NULL WHERE id = %d", $id);
			$result = $wpdb->query($sql);
		}
		

		// aktualizace data úmrtí
		if (isset($data->datum_umrti)) {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_umrti = %s WHERE id = %d", $data->datum_umrti, $id);
			$result = $wpdb->query($sql);		
		}  else {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_umrti = NULL WHERE id = %d", $id);
			$result = $wpdb->query($sql);
		}
		
		return true;
	}	

	public function getCountObjectsForAuthor($idAuthor) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM kv_objekt obj INNER JOIN kv_objekt2autor o2a ON o2a.objekt = obj.id INNER JOIN kv_autor aut ON aut.id = o2a.autor 
			WHERE aut.id = %d AND obj.deleted = 0 AND o2a.deleted = 0 AND aut.deleted = 0 AND obj.schvaleno = 1", $idAuthor); 	
		return $wpdb->get_var ($sql);
	}
	
	
	public function getPage($page, $order = "") {
		global $wpdb;
		
		$page--;
		$offset = $page * JPDb::MAX_ITEMS_ON_PAGE;
		
		return $wpdb->get_results("SELECT aut.*, count(*) as pocet FROM ".$this->tableName." aut 
			INNER JOIN kv_objekt2autor o2a ON o2a.autor = aut.id INNER JOIN kv_objekt obj ON obj.id = o2a.objekt
			WHERE aut.deleted = 0 AND o2a.deleted = 0 AND obj.deleted = 0 AND obj.schvaleno = 1 
			GROUP BY aut.id ORDER BY ".$this->getOrderSQL($order)." LIMIT ".JPDb::MAX_ITEMS_ON_PAGE." OFFSET ".$offset);
	}
	
	protected function getOrderSQL($param) {
		if (strlen($param) == 0) {
			return $this->getDefaultOrder();	
		}	
		
		switch($param) {
			case "nazev":
				return "prijmeni, jmeno";
			case "pocet-objektu":
				return "pocet desc";
			default:
				return "prijmeni, jmeno";
		}
	}
	
}


?>
	