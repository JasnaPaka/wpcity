<?php

class PoiDb extends JPDb {
   
    function __construct() {
        parent::__construct();

        $this->tableName = $this->dbPrefix."bod";
    }

    public function getDefaultOrder() {
        return "id";
    }
    
    public function getPoisForObject($idObject) {
        global $wpdb;
        
        $sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE deleted = 0 AND objekt = %d ORDER BY ".$this->getDefaultOrder(), $idObject);        
        return $wpdb->get_results ($sql);        
    }
    
    public function update($data, $id) {
        global $wpdb;

        $values = array (
            "nazev" => $data->nazev,
            "latitude" => $data->latitude,
            "longitude" => $data->longitude,
            "popis" => $data->popis,
            "objekt" => $data->objekt
        );

        $types = array (
            '%s',
            '%f',
            '%f',
            '%s',
            '%d',
        );

        return $wpdb->update($this->tableName, $values, array("id" => $id), $types);
    }
    
}
