<?php


class TagGroupDb extends JPDb
{

	function __construct() {
		parent::__construct();

		$this->tableName = $this->dbPrefix."stitek_skupina";
	}

	public function getDefaultOrder() {
		return "nazev";
	}

}