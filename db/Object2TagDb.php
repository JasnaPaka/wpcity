<?php

class Object2TagDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."objekt2stitek";
	}
	
	public function getDefaultOrder() {
		return "id";
	}
	
	public function getCountObjectsWithTag($idTag) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM ".$this->tableName." WHERE stitek = %d AND deleted = 0", $idTag);
		return $wpdb->get_var($sql);
	}		
	
}
		
?>	