<?php

class PhotoDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."fotografie";
	}	
	
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
			"img_512" => $data->img_512,
			"img_100" => $data->img_100,
			"objekt" => $data->objekt,
			"deleted" => $data->deleted,
			"primarni" => $data->primarni,
			"soukroma" => $data->soukroma,
			"autor" => $data->autor,
			"url" => $data->url,
			"popis" => $data->popis,
		);
		
		$types = array (
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',			
			'%s',
			'%s'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
	
	
	public function getPhotosWithoug512() {
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE img_512 IS NULL"); 
		return $wpdb->get_results ($sql);
	}
	
	public function getPhotosWithoug100() {
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE img_100 IS NULL"); 
		return $wpdb->get_results ($sql);
	}		
	
}


?>
	