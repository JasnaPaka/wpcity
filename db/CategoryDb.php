<?php

class CategoryDb extends JPDb {
	
    function __construct() {
        parent::__construct();

        $this->tableName = $this->dbPrefix."kategorie";
    }

    public function getDefaultOrder() {
        return "poradi desc, nazev";
    }

    public function getByUrl($url) {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE url = %s AND deleted = 0", $url); 
        $rows = $wpdb->get_results ($sql);
        if (count($rows) === 0) {
            return null;
        }

        return $rows[0];
    }

    public function update($data, $id) {
        global $wpdb;

        $values = array (
            "nazev" => $data->nazev,
            "url" => $data->url,
            "ikona" => $data->ikona,
            "barva" => $data->barva,
            "checked" => ($data->checked ? 1 : 0),
            "zoom" => $data->zoom,
            "poradi" => $data->poradi,
            "popis" => $data->popis
        );

        $types = array (
            '%s',
            '%s',			
            '%s',
            '%s',
            '%d',
            '%d',
            '%d',
            '%s'
        );

        return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
    }

    /**
     * Vrací počet objektů v konkrétní kategorii.
     */
    public function getCountObjectsInCategory($idCategory) {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT count(*) FROM ".$this->dbPrefix."objekt WHERE kategorie = %d AND deleted = 0 AND schvaleno = 1", $idCategory);
        return $wpdb->get_var($sql);
    }

    public function getCategoryByUrl($url) {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE url LIKE %s AND deleted = 0", '%'.$url.'%');
        $results = $wpdb->get_results ($sql);
        if (count($results) > 0) {
            return $results[0];	
        }

        return null; 
    }
	
}
