<?php

abstract class JPDb {

	CONST MAX_ITEMS_ON_PAGE = 20;	
	
	function __construct() {
		global $wpdb;
		
		$wpdb->show_errors();	
	}
	
	public function getAll() {
		global $wpdb;
		
		return $wpdb->get_results("SELECT * FROM ".$this->tableName." WHERE deleted = 0 ORDER BY ".$this->getDefaultOrder());	
	}
	
	public function getPage($page) {
		global $wpdb;
		
		$page--;
		$offset = $page * JPDb::MAX_ITEMS_ON_PAGE;
		
		return $wpdb->get_results("SELECT * FROM ".$this->tableName." WHERE deleted = 0 ORDER BY ".$this->getDefaultOrder()." LIMIT ".JPDb::MAX_ITEMS_ON_PAGE." OFFSET ".$offset);
	}

	public function getCount() {
		global $wpdb;
		
		return $wpdb->get_var ("SELECT count(*) FROM ".$this->tableName." WHERE deleted = 0");
	}
	
	public function getById($id) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE id = %d AND deleted = 0", $id); 
		$rows = $wpdb->get_results ($sql);
		if (count($rows) === 0) {
			return null;
		}
		
		return $rows[0];
	}
	
	public function create($data) {
		global $wpdb;
		
		return $wpdb->insert($this->tableName, (array) $data);
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$data->id = $id;
		return $wpdb->replace($this->tableName, (array) $data);
	}
	
	public function delete($id) {
		global $wpdb;
		
		return $wpdb->update($this->tableName, array ("deleted" => 1), array("id" => $id), array ('%d'));
	}
	
	public function getLastId() {
		global $wpdb;
		
		return $wpdb->insert_id;
	}
		
	
	abstract public function getDefaultOrder();

}

?>