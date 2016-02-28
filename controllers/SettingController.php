<?php

$ROOT = plugin_dir_path( __FILE__ )."../";

include_once $ROOT."fw/JPMessages.php";
include_once $ROOT."fw/JPController.php";

include_once $ROOT."db/SettingDb.php";

class SettingController extends JPController {
    
    public static $SETTING_DESCRIPTION = "popisProjektu";
    public static $SETTING_IMAGE = "obrazekProjektu";
    public static $SETTING_FACEBOOK_URL = "profilFacebook";
    public static $SETTING_GM_KEY = "gmKey";
    public static $SETTING_GM_LAT = "gmLat";
    public static $SETTING_GM_LON = "gmLon";
    public static $SETTING_GM_ZOOM = "gmZoom";
    
    private $dbSetting;
    
    private $nastaveni = null;

    function __construct() {
        $this->dbSetting = new SettingDb();
    }
    
    public function getStringId() {
        return "setting";	
    }
    
    private function validate($row) {
        
        // latitude
        if (strlen($row->gm_lat) > 0 && !GPSUtils::getIsValidLatitude($row->gm_lat)) {
            array_push($this->messages, new JPErrorMessage("Neplatná latitude u GPS souřadnice."));
        }

        // longitude
        if (strlen($row->gm_lon) > 0 && !GPSUtils::getIsValidLongitude($row->gm_lon)) {
            array_push($this->messages, new JPErrorMessage("Neplatná longitude u GPS souřadnice."));
        }
        
        if (!$row->gm_zoom >1) {
            array_push($this->messages, new JPErrorMessage("Úroveň přiblížení (zoom) musí být kladné číslo."));
        }
        
        return count($this->messages) === 0; 
    }
    
    private function getFormValues() {
        
        $row = new stdClass();
        $row->popisProjektu = $_POST[SettingController::$SETTING_DESCRIPTION];
        $row->obrazekProjektu = filter_input (INPUT_POST, SettingController::$SETTING_IMAGE, FILTER_SANITIZE_STRING);
        $row->profilFacebook = filter_input (INPUT_POST, SettingController::$SETTING_FACEBOOK_URL, FILTER_SANITIZE_STRING);
        $row->gmKey = filter_input (INPUT_POST, SettingController::$SETTING_GM_KEY, FILTER_SANITIZE_STRING);
        $row->gmLat = filter_input (INPUT_POST, SettingController::$SETTING_GM_LAT, FILTER_SANITIZE_STRING);
        $row->gmLon = filter_input (INPUT_POST, SettingController::$SETTING_GM_LON, FILTER_SANITIZE_STRING);
        $row->gmZoom = (int) filter_input (INPUT_POST, SettingController::$SETTING_GM_ZOOM, FILTER_SANITIZE_STRING);
        
        return $row;
    }
    
    public function update() {
        $this->nastaveni = $this->getFormValues();
        if (!$this->validate($this->nastaveni)) {
            return $this->nastaveni;
        }

        foreach ($this->nastaveni as $key => $value) {
            $this->dbSetting->setSetting($key, $value);
        }       

        array_push($this->messages, new JPInfoMessage('Nastavení bylo úspěšně aktualizováno.')); 
        
        $this->nastaveni = null;
        
        return $this->getRow();
    }
    
    public function getRow() {
        
        if ($this->nastaveni == null) {
            $this->nastaveni = new stdClass();
            $this->nastaveni->popisProjektu = $this->getSettingDescription();
            $this->nastaveni->obrazekProjektu = $this->getSettingImage();
            $this->nastaveni->profilFacebook = $this->getSettingFB();
            $this->nastaveni->gmKey = $this->getSettingGMKey();
            $this->nastaveni->gmLat = $this->getSettingGMLat();
            $this->nastaveni->gmLon = $this->getSettingGMLon();
            $this->nastaveni->gmZoom = $this->getSettingGMZoom();
        }
        
        return $this->nastaveni;
    }
    
    private function getSettingDescription() {
        return $this->dbSetting->getSetting(SettingController::$SETTING_DESCRIPTION)->hodnota;
    }
    
    private function getSettingImage() {
        return $this->dbSetting->getSetting(SettingController::$SETTING_IMAGE)->hodnota;
    }

    private function getSettingFB() {
        return $this->dbSetting->getSetting(SettingController::$SETTING_FACEBOOK_URL)->hodnota;
    }    
    
    private function getSettingGMKey() {
        return $this->dbSetting->getSetting(SettingController::$SETTING_GM_KEY)->hodnota;
    }

    private function getSettingGMLat() {
        return $this->dbSetting->getSetting(SettingController::$SETTING_GM_LAT)->hodnota;
    }
    
    private function getSettingGMLon() {
        return $this->dbSetting->getSetting(SettingController::$SETTING_GM_LON)->hodnota;
    }
    
    private function getSettingGMZoom() {
        return $this->dbSetting->getSetting(SettingController::$SETTING_GM_ZOOM)->hodnota;
    }
}

