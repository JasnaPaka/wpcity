<?php

class ObjectDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."objekt";
	}
	
	public function getDefaultOrder() {
		return "nazev";
	}
	
	public function getCountObjectsInCategory($idCategory) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM ".$this->tableName." WHERE kategorie = %d AND deleted = 0 AND zruseno = 0 AND schvaleno = 1", $idCategory); 	
		return $wpdb->get_var ($sql);
	}
	
	public function getListByNazev($nazev, $order="", $iZrusene = false, $iNeschvalene = false) {
		global $wpdb;
		
		$iZrusene = $iZrusene ? " " : "AND zruseno = 1";
		$iNeschvalene = $iNeschvalene ? " " : "AND schvaleno = 1";
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE nazev LIKE %s AND deleted = 0 ".$iZrusene." ".$iNeschvalene." ORDER BY ".$this->getOrderSQL($order), '%'.$nazev.'%');
		
		return $wpdb->get_results ($sql);
	}
	
	public function getListByCategory($idCategory, $order="", $withCanceled = false) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE kategorie = %d AND deleted = 0 AND schvaleno = 1 ".($withCanceled ? "" : "AND zruseno = 0")." ORDER BY ".$this->getOrderSQL($order), $idCategory);
		return $wpdb->get_results ($sql);
	}
	
	public function getListByAuthor($idAuthor, $iZrusene = false) {
		global $wpdb;
		
		$iZruseneStr = $iZrusene ? " " : "AND kv.zruseno = 0";
		
		$sql = $wpdb->prepare("SELECT DISTINCT kv.*, fot.img_512 as img_512, fot.skryta as skryta FROM ".$this->tableName." kv INNER JOIN ".$this->dbPrefix."objekt2autor o2a ON kv.id = o2a.objekt
			LEFT JOIN ".$this->dbPrefix."fotografie fot ON fot.objekt = kv.id
			WHERE o2a.autor = %d AND kv.deleted = 0 AND o2a.deleted = 0 AND (fot.deleted = 0 OR fot.deleted IS NULL) AND kv.schvaleno = 1 ".$iZruseneStr." AND (fot.primarni IS NULL OR fot.primarni = 1) ORDER BY kv.nazev", $idAuthor);
			
		return $wpdb->get_results ($sql);
	}
	
	public function getPageByNazev($page, $nazev, $order="") {
		global $wpdb;
		
		$page--;
		$offset = $page * JPDb::MAX_ITEMS_ON_PAGE;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE nazev LIKE %s AND deleted = 0 AND schvaleno = 1 AND zruseno = 0
						ORDER BY ".$this->getOrderSQL($order)." LIMIT ".JPDb::MAX_ITEMS_ON_PAGE." OFFSET ".$offset, '%'.$nazev.'%');
		return $wpdb->get_results ($sql); 
	}
	
	public function getCountByNazev($nazev) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM ".$this->tableName." WHERE nazev LIKE %s AND deleted = 0 AND schvaleno = 1 AND zruseno = 0", '%'.$nazev.'%');
		return $wpdb->get_var ($sql); 
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$values = array (
			"nazev" => $data->nazev,
			"latitude" => $data->latitude,
			"longitude" => $data->longitude,
			"kategorie" => $data->kategorie,
			"popis" => $data->popis,
			"obsah" => $data->obsah,
			"interni" => $data->interni,
			"rok_vzniku" => $data->rok_vzniku,
			"prezdivka" => $data->prezdivka,
			"material" => $data->material,
			"pamatkova_ochrana" => $data->pamatkova_ochrana,
			"pristupnost" => $data->pristupnost,
			"upravil_autor" => $data->upravil_autor,
			"upravil_datum" => $data->upravil_datum,
			"zruseno" => ($data->zruseno ? 1 : 0),
			"zpracovano" => ($data->zpracovano ? 1 : 0)
		);
		
		$types = array (
			'%s',
			'%f',
			'%f',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d'
		);
		
		return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
	}
	
	public function getAuthorsForObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT aut.* FROM ".$this->dbPrefix."autor aut INNER JOIN ".$this->dbPrefix."objekt2autor o2a ON aut.id = o2a.autor 
			WHERE o2a.objekt = %d AND aut.deleted = 0 AND o2a.deleted = 0 ORDER BY o2a.id", $idObject);
			
		return $wpdb->get_results ($sql); 
	}
	
	public function getCooperationsForObject($idObject) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT o2a.spoluprace FROM ".$this->dbPrefix."autor aut INNER JOIN ".$this->dbPrefix."objekt2autor o2a ON aut.id = o2a.autor 
			WHERE o2a.objekt = %d AND aut.deleted = 0 AND o2a.deleted = 0 ORDER BY o2a.id", $idObject);
			
		return $wpdb->get_results ($sql); 
	}
	
	/**
	 * Vrací seznam objektů, které u sebe nemají žádnou fotografii. Ignorují se systémové kategorie.
	 */
	public function getObjectsWithNoPhotos() {
		global $wpdb;
		
		return $wpdb->get_results("SELECT DISTINCT obj.* FROM ".$this->tableName." obj 
			LEFT JOIN ".$this->dbPrefix."fotografie fot ON obj.id = fot.objekt
			INNER JOIN ".$this->dbPrefix."kategorie kat ON obj.kategorie = kat.id 
			WHERE (fot.id is null OR fot.skryta = 1) AND obj.deleted = 0 AND obj.schvaleno = 1 AND kat.systemova = 0 AND obj.zruseno = 0 ");
	}
	
	protected function getOrderSQL($param) {
		if (strlen($param) == 0) {
			return $this->getDefaultOrder();	
		}	
		
		switch($param) {
			case "nazev":
				return "nazev";
			case "vytvoreni":
				return "pridal_datum desc";
			case "aktualizace":
				return "upravil_datum desc";
			default:
				return "nazev";
		}
	}
	
	public function getCountObjectsWithPhotos() {
		global $wpdb;

		return $wpdb->get_var("SELECT count(*) FROM ".$this->tableName." obj 
			INNER JOIN ".$this->dbPrefix."fotografie fot ON obj.id = fot.objekt
			INNER JOIN ".$this->dbPrefix."kategorie kat ON obj.kategorie = kat.id 
			WHERE fot.primarni = 1 AND fot.skryta = 0 AND obj.deleted = 0 AND obj.schvaleno = 1 AND kat.systemova = 0 AND obj.zruseno = 0 ");
	}
	
	public function getRandomObjectWithPhoto($randomNumber) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT obj.id, obj.nazev, fot.img_512, obj.kategorie FROM ".$this->tableName." obj 
			INNER JOIN ".$this->dbPrefix."fotografie fot ON obj.id = fot.objekt
			INNER JOIN ".$this->dbPrefix."kategorie kat ON obj.kategorie = kat.id 
			WHERE fot.primarni = 1 AND fot.skryta = 0 AND obj.deleted = 0 AND obj.schvaleno = 1 AND kat.systemova = 0 AND obj.zruseno = 0 ORDER BY obj.id LIMIT 1 OFFSET %d", $randomNumber);		
			
		$results = $wpdb->get_results ($sql);
		return $results[0];
	}
	
	
	public function getLastObjectWithPhoto() {
		global $wpdb;
		
		$results = $wpdb->get_results ("SELECT obj.id, obj.nazev, fot.img_512 FROM ".$this->tableName." obj 
			INNER JOIN ".$this->dbPrefix."fotografie fot ON obj.id = fot.objekt
			INNER JOIN ".$this->dbPrefix."kategorie kat ON obj.kategorie = kat.id 
			WHERE fot.primarni = 1 AND obj.deleted = 0 AND fot.skryta = 0 AND obj.schvaleno = 1 AND kat.systemova = 0 AND obj.zruseno = 0 ORDER BY obj.id DESC LIMIT 1");
		return $results[0];		
	}
	
	
	public function getCatalogPage($page, $search, $tag) {
		global $wpdb;

		$startObject = $page * 9;
		
		if ($search != null) {
			$sql = $wpdb->prepare("SELECT obj.id, obj.nazev, fot.img_512, kat.nazev as katnazev, fot.skryta as skryta FROM ".$this->tableName." obj 
				LEFT JOIN ".$this->dbPrefix."fotografie fot ON obj.id = fot.objekt
				INNER JOIN ".$this->dbPrefix."kategorie kat ON obj.kategorie = kat.id 
				WHERE obj.nazev LIKE %s AND (fot.primarni = 1 OR fot.primarni IS NULL) AND obj.deleted = 0 AND (fot.deleted = 0 OR fot.deleted IS NULL) AND obj.schvaleno = 1 ORDER BY obj.nazev", 
				"%".$search."%");	
				
			return $wpdb->get_results($sql);
		}
		
		if ($tag != null) {
			$sql = $wpdb->prepare("SELECT obj.id, obj.nazev, fot.img_512, kat.nazev as katnazev, fot.skryta as skryta FROM ".$this->tableName." obj 
			LEFT JOIN ".$this->dbPrefix."fotografie fot ON obj.id = fot.objekt
			INNER JOIN ".$this->dbPrefix."objekt2stitek o2s ON o2s.objekt = obj.id
			INNER JOIN ".$this->dbPrefix."kategorie kat ON obj.kategorie = kat.id 
			WHERE (fot.primarni = 1 OR fot.primarni IS NULL) AND obj.deleted = 0 AND (fot.deleted = 0 OR fot.deleted IS NULL) AND obj.schvaleno = 1 
				AND o2s.stitek = %d ORDER BY obj.nazev", $tag->id);
			
			return $wpdb->get_results($sql);
		}
			
			
		
		return $wpdb->get_results("SELECT obj.id, obj.nazev, fot.img_512, kat.nazev as katnazev, fot.skryta as skryta FROM ".$this->tableName." obj 
			LEFT JOIN ".$this->dbPrefix."fotografie fot ON obj.id = fot.objekt
			INNER JOIN ".$this->dbPrefix."kategorie kat ON obj.kategorie = kat.id 
			WHERE (fot.primarni = 1 OR fot.primarni IS NULL) AND obj.deleted = 0 AND (fot.deleted = 0 OR fot.deleted IS NULL) AND obj.schvaleno = 1 ORDER BY obj.nazev 
			LIMIT 9 OFFSET ".$startObject);
	}
	
	
	public function getCountKeSchvaleni() {
		global $wpdb;
		
		return $wpdb->get_var ("SELECT count(*) FROM ".$this->tableName." WHERE schvaleno = 0 AND deleted = 0"); 
	}
	
	public function getNeschvaleneList($order="") {
		global $wpdb;
		
		return $wpdb->get_results ("SELECT * FROM ".$this->tableName." WHERE schvaleno = 0 AND deleted = 0 ORDER BY ".$this->getOrderSQL($order)); 		
	}
			
		
	public function approveObject($id) {
		global $wpdb;
		
		$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET schvaleno = 1 WHERE id = %d", $id);
		return $wpdb->get_results ($sql);
	}
		
}

?>