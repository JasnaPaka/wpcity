<?php

class SourceDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."zdroj";
	}
	
	public function getDefaultOrder() {
		return "id";
	}
	
	/**
	 * Vrací seznam nesmazaných zdrojů pro konkrétní objekt. V závislosti na druhém parametru vrací ty, které jsou
	 * uživatelem zaznamenané nebo ty, které jsou spravovány systémem.
	 */
	public function getSourcesForObject($idObject, $system = false) {
		global $wpdb;

		$szSql = $system ? "IS NOT NULL" : "IS NULL";

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." 
			zdr WHERE zdr.deleted = 0 AND zdr.objekt = %d AND zdr.system_zdroj ".$szSql." ORDER BY ".$this->getDefaultOrder(), $idObject);
		
		return $wpdb->get_results ($sql);
	}

	public function deleteSystemForObject($objectId, $code) {
		global $wpdb;

		$sql = $wpdb->prepare("DELETE FROM ".$this->tableName." WHERE
			objekt = %d AND system_zdroj = %s", $objectId, $code);

		return $wpdb->query($sql);
	}

	public function deleteSystemForAuthor($authorId, $code) {
		global $wpdb;

		$sql = $wpdb->prepare("DELETE FROM ".$this->tableName." WHERE
			autor = %d AND system_zdroj = %s", $authorId, $code);

		return $wpdb->query($sql);
	}

	public function getSourcesForAuthor($idObject, $system = false) {
		global $wpdb;

		$szSql = $system ? "IS NOT NULL" : "IS NULL";

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." 
			zdr WHERE zdr.deleted = 0 AND zdr.autor = %d AND zdr.system_zdroj ".$szSql." ORDER BY ".$this->getDefaultOrder(), $idObject);
		
		return $wpdb->get_results ($sql); 
	}
	
	public function updateWithObject($data, $id, $isObject) {
		global $wpdb;
		
		$values = array (
			"typ" => $data->typ,
			"identifikator" => $data->identifikator,
			"nazev" => $data->nazev,
			"url" => $data->url,
			"cerpano" => $data->cerpano,
			"deleted" => $data->deleted,
			"system_zdroj" => $data->system_zdroj
		);
		
		if ($isObject) {
			$values["objekt"] = $data->objekt;
		} else {
			$values["autor"] = $data->autor;
		}
		
		$types = array (
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s'
		);
				
		$wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
	
	public function createWithObject($data, $isObject) {
		global $wpdb;
		
		$values = array (
			"typ" => $data->typ,
			"identifikator" => $data->identifikator,
			"nazev" => $data->nazev,
			"url" => $data->url,
			"cerpano" => $data->cerpano,
			"deleted" => $data->deleted,
			"system_zdroj" => $data->system_zdroj
		);
		
		if ($isObject) {
			$values["objekt"] = $data->objekt;
		} else {
			$values["autor"] = $data->autor;
		}
		
		$types = array (
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s'
		);
		
		
		return $wpdb->insert($this->tableName, (array) $values, $types);
	}	
		
}