<?php

class AuthorDb extends JPDb {
	
	protected $tableName = "kv_autor";
	
	public function getDefaultOrder() {
		return "jmeno";
	}
	
}


?>
	