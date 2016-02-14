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
	 * Vrací seznam nesmazaných zdrojů pro konkrétní objekt.
	 */
	public function getSourcesForObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." zdr WHERE zdr.deleted = 0 AND zdr.objekt = %d ORDER BY ".$this->getDefaultOrder(), $idObject);
		
		return $wpdb->get_results ($sql); 
	}
	
	public function getSourcesForAuthor($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." zdr WHERE zdr.deleted = 0 AND zdr.autor = %d ORDER BY ".$this->getDefaultOrder(), $idObject);
		
		return $wpdb->get_results ($sql); 
	}
	
	public function update($data, $id, $isObject) {
		global $wpdb;
		
		$values = array (
			"nazev" => $data->nazev,
			"url" => $data->url,
			"isbn" => $data->isbn,
			"cerpano" => $data->cerpano,
			"deleted" => $data->deleted,
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
			'%d',
			'%d',
			'%d'
		);
				
		$wpdb->update($this->tableName, $values, array("id" => $id), $types);
		
		$wpdb->print_error();
	}	
	
	public function create($data, $isObject) {
		global $wpdb;
		
		$values = array (
			"nazev" => $data->nazev,
			"url" => $data->url,
			"isbn" => $data->isbn,
			"cerpano" => $data->cerpano,
			"deleted" => $data->deleted,
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
			'%d',
			'%d',
			'%d'
		);		
		
		
		return $wpdb->insert($this->tableName, (array) $values, $types);
	}	
		
}