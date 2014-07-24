<?php

class PhotoDb extends JPDb {
	
	protected $tableName = "kv_fotografie";
	
	public function getDefaultOrder() {
		return "id";
	}
	
	public function deletePhotosByObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET deleted = 1 WHERE objekt = %d", $idObject);
		return $wpdb->query($sql);
	}
	
	public function getPhotosByObject($idObject) {
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE objekt = %d AND deleted = 0 ORDER BY ".$this->getDefaultOrder(), $idObject); 
		return $wpdb->get_results ($sql);
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$values = array (
			"img_original" => $data->img_original,
			"img_thumbnail" => $data->img_thumbnail,
			"img_medium" => $data->img_medium,
			"img_large" => $data->img_large,
			"objekt" => $data->objekt,
			"deleted" => $data->deleted,
			"primarni" => $data->primarni,
			"autor" => $data->autor,
			"popis" => $data->popis,
		);
		
		$types = array (
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',			
			'%s'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
	
}


?>
	