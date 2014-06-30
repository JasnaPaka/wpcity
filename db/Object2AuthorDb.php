<?php

class Object2AuthorDb extends JPDb {
	
	protected $tableName = "kv_objekt2autor";
	
	public function getDefaultOrder() {
		return "id";
	}
	
}

?>
	
	