<?php

class AuthorDb extends JPDb {
	
	protected $tableName = "kv_autor";
	
	public function getDefaultOrder() {
		return "jmeno";
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$values = array (
			"jmeno" => $data->jmeno,
			"datum_narozeni" => $data->datum_narozeni,
			"datum_umrti" => $data->datum_umrti,
			"obsah" => $data->obsah
		);
		
		$types = array (
			'%s',
			'%s',			
			'%s',
			'%s'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}	
	
}


?>
	