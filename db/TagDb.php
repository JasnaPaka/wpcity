<?php

class TagDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."stitek";
	}
	
	public function getDefaultOrder() {
		return "nazev";
	}


	public function update($data, $id) {
		global $wpdb;
		
		$values = array (
			"nazev" => $data->nazev,
			"popis" => $data->popis
		);
		
		$types = array (
			'%s', '%s'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
		
}