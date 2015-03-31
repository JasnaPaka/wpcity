<?php

class CollectionDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."soubor";
	}
	
	public function getDefaultOrder() {
		return "nazev";
	}
	
	
}