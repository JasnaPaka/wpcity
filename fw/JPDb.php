<?php

class JPDb {

	CONST MAX_ITEMS_ON_PAGE = 20;	
	
	public function getAll() {
		global $wpdb;
		
		return $wpdb->get_results("SELECT * FROM ".$this->tableName." ORDER BY nazev");	
	}
	
	public function getPage($page) {
		global $wpdb;
		
		$page--;
		$offset = $page * JPDb::MAX_ITEMS_ON_PAGE;
		
		return $wpdb->get_results("SELECT * FROM ".$this->tableName." ORDER BY nazev LIMIT ".JPDb::MAX_ITEMS_ON_PAGE." OFFSET ".$offset);
	}

	public function getCount() {
		global $wpdb;
		
		return $wpdb->get_var ("SELECT count(*) FROM ".$this->tableName);
	}
	
	public function getById($id) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE id = %d", $id); 
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
		
		return $wpdb->delete($this->tableName, array('id' => $id));
	}

}

?>