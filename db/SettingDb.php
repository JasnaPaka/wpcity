<?php

/**
 * Přístup k nastavení pluginu.
 */
class SettingDb extends JPDb {
    
    const SETTING_DESCRIPTION = "popisProjektu";
    const SETTING_IMAGE = "obrazekProjektu";
    const SETTING_FACEBOOK_URL = "profilFacebook";
    const SETTING_GM_KEY = "gmKey";
    const SETTING_GM_LAT = "gmLat";
    const SETTING_GM_LON = "gmLon";
    const SETTING_GM_ZOOM = "gmZoom";
    const SETTING_MAP_IMAGE = "obrazekMapy";
    const SETTING_GOOGLE_SEACH = "vyhledavaniGoogleKlic";

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
        
        $setting = $this->getSetting($key);
        if ($setting) {        
            $sql = $wpdb->prepare("UPDATE ".$this->tableName." SET hodnota = %s WHERE nazev = %s", $value, $key);
            return $wpdb->query($sql);
        } else {
            $wpdb->insert( 
                $this->tableName, 
                array( 
                    'nazev' => $key, 
                    'hodnota' => $value 
                ), 
                array( 
                    '%s', 
                    '%s' 
                ) 
            );
        }
    }
    
    public function getAll($order = "") {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM ".$this->tableName." ORDER BY ".$this->getOrderSQL($order));	
    }    
    
    public function getSettingGMApiKey() {
        return $this->getSetting(self::SETTING_GM_KEY);
    }
}