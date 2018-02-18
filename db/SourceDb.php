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
	 * Vrátí seznam zdrojů pro objekt. Standardně vrací seznam děl, které může uživatel
	 * editovat. Může však na základě druhého parametru vracet i zdroje "systémové", které
	 * jsou spravovány automaticky.
	 *
	 * @param $idObject id objektu
	 * @param bool $system true, pokud se mají brát systémové zdroje, jinak false (výchozí)
	 * @return mixed
	 */
	public function getSourcesForObject($idObject, $system = false) {
		global $wpdb;

		$szSql = $system ? "IS NOT NULL" : "IS NULL";

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." 
			zdr WHERE zdr.deleted = 0 AND zdr.objekt = %d AND zdr.system_zdroj ".$szSql." 
			ORDER BY ".$this->getDefaultOrder(), $idObject);
		
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

	public function deleteSystemForCollection($collectionId, $code) {
		global $wpdb;

		$sql = $wpdb->prepare("DELETE FROM ".$this->tableName." WHERE
			soubor = %d AND system_zdroj = %s", $collectionId, $code);

		return $wpdb->query($sql);
	}

	/**
	 * Vrátí seznam zdrojů pro autora. Standardně vrací seznam děl, které může uživatel
	 * editovat. Může však na základě druhého parametru vracet i zdroje "systémové", které
	 * jsou spravovány automaticky.
	 *
	 * @param $idAuthor id autora
	 * @param bool $system true, pokud se mají brát systémové zdroje, jinak false (výchozí)
	 * @return mixed
	 */
	public function getSourcesForAuthor($idAuthor, $system = false) {
		global $wpdb;

		$szSql = $system ? "IS NOT NULL" : "IS NULL";

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." 
			zdr WHERE zdr.deleted = 0 AND zdr.autor = %d AND zdr.system_zdroj ".$szSql." 
				ORDER BY ".$this->getDefaultOrder(), $idAuthor);
		
		return $wpdb->get_results ($sql); 
	}

	/**
	 * Vrátí seznam zdrojů pro soubor děl. Standardně vrací seznam děl, které může uživatel
	 * editovat. Může však na základě druhého parametru vracet i zdroje "systémové", které
	 * jsou spravovány automaticky.
	 *
	 * @param $idCollection id souboru děl
	 * @param bool $system true, pokud se mají brát systémové zdroje, jinak false (výchozí)
	 * @return mixed
	 */
	public function getSourcesForCollection($idCollection, $system = false) {
		global $wpdb;

		$szSql = $system ? "IS NOT NULL" : "IS NULL";

		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." 
			zdr WHERE zdr.deleted = 0 AND zdr.soubor = %d AND zdr.system_zdroj ".$szSql." 
				ORDER BY ".$this->getDefaultOrder(), $idCollection);

		return $wpdb->get_results ($sql);
	}
	
	public function updateWithObject($data, $id, $isObject, $idCollection) {
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
		} else if ($idCollection) {
			$values["soubor"] = $data->soubor;
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
			'%s',
			'%d'
		);
				
		$wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
	
	public function createWithObject($data, $isObject, $idCollection) {
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
		} else if ($idCollection) {
			$values["soubor"] = $data->soubor;
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
			'%s',
			'%d'
		);
		
		
		return $wpdb->insert($this->tableName, (array) $values, $types);
	}	
		
}