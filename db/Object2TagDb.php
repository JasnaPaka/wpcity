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
	
	public function getTagsForObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE objekt = %d AND deleted = 0", $idObject);
		return $wpdb->get_results ($sql);	
	}
	
	public function deleteTagsForObject($idObject) {
		global $wpdb;
		
		$where = array (
			"objekt" => $idObject,
		);
		
		$wpdb->delete($this->tableName, $where);
	}
		
}
		
?>	