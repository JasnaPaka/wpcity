<?php

class CollectionDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."soubor";
	}
	
	public function getDefaultOrder() {
		return "nazev";
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$values = array (
			"nazev" => $data->nazev,
			"latitude" => $data->latitude,
			"longitude" => $data->longitude,
			"popis" => $data->popis,
			"obsah" => $data->obsah,
			"zpracovano" => ($data->zpracovano ? 1 : 0),
			"interni" => $data->interni,
			"upravil_autor" => $data->upravil_autor,
			"upravil_datum" => $data->upravil_datum
		);
		
		$types = array (
			'%s',
			'%f',
			'%f',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%d'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}	
	
	
}