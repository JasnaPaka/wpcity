<?php

class ObjectsDb {
	
	public function getCountObjectsInCategory($idCategory) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM kv_objekt WHERE kategorie = %d", $idCategory); 	
		return $wpdb->get_var ($sql);
	}
}

?>