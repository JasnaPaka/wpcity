<?php

class TagDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."stitek";
	}
	
	public function getDefaultOrder() {
		return "nazev";
	}

	public function getAll($order = "")
	{
		global $wpdb;

		if ($order == "groups") {
			return $wpdb->get_results("SELECT s.*, ss.barva, ss.poradi FROM ".$this->tableName. " s LEFT JOIN ".$this->dbPrefix."stitek_skupina ss
			ON ss.id = s.skupina WHERE s.deleted = 0 ORDER BY (CASE WHEN ss.poradi IS NULL then 999999 ELSE ss.poradi END), s.nazev");
		}

		return parent::getAll($order);
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