<?php

class AuthorDb extends JPDb {
	
	protected $tableName = "kv_autor";
	
	public function getDefaultOrder() {
		return "jmeno";
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$sql = "UPDATE ".$this->tableName." SET jmeno = %s, obsah = %s WHERE id = %d";
		$sql = $wpdb->prepare($sql, $data->jmeno, $data->obsah, $id);
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
	
}


?>
	