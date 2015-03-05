<?php

class AuthorDb extends JPDb {
	
	function __construct() {
		parent::__construct();
		
		$this->tableName = $this->dbPrefix."autor";
	}
	
	public function getDefaultOrder() {
		return "prijmeni, jmeno";
	}
	
	public function update($data, $id) {
		global $wpdb;
		
		$sql = "UPDATE ".$this->tableName." SET jmeno = %s, prijmeni = %s, titul_pred = %s, titul_za = %s, obsah = %s, zpracovano = %d WHERE id = %d";
		$sql = $wpdb->prepare($sql, $data->jmeno, $data->prijmeni, $data->titul_pred, $data->titul_za, $data->obsah, $data->zpracovano, $id);
		$result = $wpdb->query($sql);
		
		// aktualizace data narození
		if (isset($data->datum_narozeni)) {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_narozeni = %s WHERE id = %d", $data->datum_narozeni, $id);
			$result = $wpdb->query($sql);			
		}  else {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_narozeni = NULL WHERE id = %d", $id);
			$result = $wpdb->query($sql);
		}
		

		// aktualizace data úmrtí
		if (isset($data->datum_umrti)) {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_umrti = %s WHERE id = %d", $data->datum_umrti, $id);
			$result = $wpdb->query($sql);		
		}  else {
			$sql = $wpdb->prepare("UPDATE ".$this->tableName." SET datum_umrti = NULL WHERE id = %d", $id);
			$result = $wpdb->query($sql);
		}
		
		return true;
	}	

	public function getCountObjectsForAuthor($idAuthor, $iZrusene = false) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(DISTINCT obj.id) FROM ".$this->dbPrefix."objekt obj INNER JOIN ".$this->dbPrefix."objekt2autor o2a ON o2a.objekt = obj.id 
			INNER JOIN ".$this->dbPrefix."autor aut ON aut.id = o2a.autor 
			WHERE aut.id = %d AND obj.deleted = 0 AND o2a.deleted = 0 AND aut.deleted = 0 AND obj.schvaleno = 1", $idAuthor);
			 	
		return $wpdb->get_var ($sql);
	}
	
	
	public function getPage($page, $order = "") {
		global $wpdb;
		
		$page--;
		$offset = $page * JPDb::MAX_ITEMS_ON_PAGE;
		
		return $wpdb->get_results("SELECT aut.*, count(*) as pocet FROM ".$this->tableName." aut 
			INNER JOIN ".$this->dbPrefix."objekt2autor o2a ON o2a.autor = aut.id INNER JOIN ".$this->dbPrefix."objekt obj ON obj.id = o2a.objekt
			WHERE aut.deleted = 0 AND o2a.deleted = 0 AND obj.deleted = 0 AND obj.schvaleno = 1 
			GROUP BY aut.id ORDER BY ".$this->getOrderSQL($order)." LIMIT ".JPDb::MAX_ITEMS_ON_PAGE." OFFSET ".$offset);
	}
	
	protected function getOrderSQL($param) {
		if (strlen($param) == 0) {
			return $this->getDefaultOrder();	
		}	
		
		switch($param) {
			case "nazev":
				return "prijmeni, jmeno";
			case "pocet-objektu":
				return "pocet desc";
			default:
				return "prijmeni, jmeno";
		}
	}
	
	public function getListByNazev($nazev, $order="") {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE (CONCAT (prijmeni, ' ', jmeno) LIKE %s OR CONCAT (jmeno, ' ', prijmeni) LIKE %s) 
			AND deleted = 0 ORDER BY ".$this->getOrderSQL($order), '%'.$nazev.'%', '%'.$nazev.'%');
		return $wpdb->get_results ($sql);
	}
	
	public function getCountByNazev($nazev) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT count(*) FROM ".$this->tableName." WHERE (CONCAT (prijmeni, ' ', jmeno) LIKE %s OR CONCAT (jmeno, ' ', prijmeni) LIKE %s) 
			AND deleted = 0", '%'.$nazev.'%', '%'.$nazev.'%');
		return $wpdb->get_var ($sql); 
	}
	
	
	public function getCatalogPage($page, $search) {
		global $wpdb;

		$startObject = $page * 9;
		
		if ($search != null) {
				$sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." aut WHERE deleted = 0 AND 
					((CONCAT (aut.prijmeni, ' ', aut.jmeno) LIKE %s OR CONCAT (aut.jmeno, ' ', aut.prijmeni) LIKE %s)) 
					ORDER BY ".$this->getOrderSQL($order), "%".$search."%", "%".$search."%");
				return $wpdb->get_results($sql);
		} 
		
		
		return $wpdb->get_results("SELECT * FROM ".$this->tableName." aut WHERE deleted = 0 ORDER BY ".$this->getOrderSQL($order)." LIMIT 9 OFFSET ".$startObject);
	}	
	
	
	public function getImgForAuthor($authorId) {
		global $wpdb;
		
		$sql = $wpdb->prepare("SELECT fot.img_512 FROM ".$this->dbPrefix."objekt2autor o2a INNER JOIN ".$this->dbPrefix."objekt obj ON obj.id = o2a.objekt
			INNER JOIN ".$this->dbPrefix."fotografie fot ON fot.objekt = obj.id WHERE o2a.deleted = 0 AND obj.deleted = 0 AND fot.deleted = 0 
			AND fot.primarni = 1 AND o2a.autor = %d
			ORDER BY fot.id LIMIT 1", $authorId);
			
		$authors = $wpdb->get_results ($sql);
		if (count($authors) > 0) {
			return $authors[0];
		}
			
		return null;
	}
}


?>
	