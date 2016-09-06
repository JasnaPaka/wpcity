<?php


class TagGroupDb extends JPDb
{

	function __construct() {
		parent::__construct();

		$this->tableName = $this->dbPrefix."stitek_skupina";
	}

	public function getDefaultOrder() {
		return "-poradi DESC, nazev";
	}

	protected function getOrderSQL($order) {
		if ($order == "name") {
			return "nazev";
		}

		return $this->getDefaultOrder();
	}

	public function update($data, $id) {
		global $wpdb;

		$values = array (
			"nazev" => $data->nazev,
			"popis" => $data->popis,
			"barva" => $data->barva,
			"poradi" => $data->poradi
		);

		$types = array (
			'%s', '%s', '%s', '%d'
		);

		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
}