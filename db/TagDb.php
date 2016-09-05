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
			"popis" => $data->popis,
			"skupina" => $data->skupina
		);

		$types = array (
			'%s', '%s', '%d'
		);

		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}

	public function getTagWithTagGroup($idTagGroup) {
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM " . $this->tableName . " WHERE skupina = %d AND deleted = 0 ORDER BY nazev", $idTagGroup);
		return $wpdb->get_results($sql);
	}
		
}