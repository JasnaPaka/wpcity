<?php

class SettingDb extends JPDb {

    function __construct() {
        parent::__construct();

        $this->tableName = $this->dbPrefix."nastaveni";
    }

    public function getDefaultOrder() {
        return "id";
    }       
    
    public function getSetting($key) {
        global $wpdb;
        
        $sql = $wpdb->prepare("SELECT * FROM ".$this->tableName." WHERE nazev = %s LIMIT 1", $key);
	$rows = $wpdb->get_results ($sql);
        
        return $rows[0];
    }
    
    public function setSetting($key, $value) {
        global $wpdb;
        
        $sql = $wpdb->prepare("UPDATE ".$this->tableName." SET hodnota = %s WHERE nazev = %s", $value, $key);
	return $wpdb->query($sql);
    }
    
}