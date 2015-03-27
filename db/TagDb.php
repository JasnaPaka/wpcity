<?php

class TagDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."stitek";
	}
	
	public function getDefaultOrder() {
		return "id";
	}
	
}
		
?>	