<?php

$ROOT = plugin_dir_path(__FILE__) . "../";

include_once $ROOT . "fw/JPMessages.php";
include_once $ROOT . "fw/JPController.php";

include_once $ROOT . "db/SettingDb.php";

class SettingController extends JPController
{

	public static $SETTING_DESCRIPTION = "popisProjektu";
	public static $SETTING_IMAGE = "obrazekProjektu";
	public static $SETTING_FACEBOOK_URL = "profilFacebook";
	public static $SETTING_GM_KEY = "gmKey";
	public static $SETTING_GM_LAT = "gmLat";
	public static $SETTING_GM_LON = "gmLon";
	public static $SETTING_GM_ZOOM = "gmZoom";
	public static $SETTING_MAP_IMAGE = "obrazekMapy";
	public static $SETTING_GOOGLE_SEACH = "vyhledavaniGoogleKlic";
	public static $SETTING_CITY_API_URL = "webovaSluzbaCityURL";
	public static $SETTING_REQUEST = "hledame";

	private $dbSetting;

	private $nastaveni = null;

	function __construct()
	{
		$this->dbSetting = new SettingDb();
	}

	public function getStringId()
	{
		return "setting";
	}

	private function validate($row)
	{

		// latitude
		if (strlen($row->gm_lat) > 0 && !GPSUtils::getIsValidLatitude($row->gm_lat)) {
			array_push($this->messages, new JPErrorMessage("Neplatná latitude u GPS souřadnice."));
		}

		// longitude
		if (strlen($row->gm_lon) > 0 && !GPSUtils::getIsValidLongitude($row->gm_lon)) {
			array_push($this->messages, new JPErrorMessage("Neplatná longitude u GPS souřadnice."));
		}

		if (!$row->gm_zoom > 1) {
			array_push($this->messages, new JPErrorMessage("Úroveň přiblížení (zoom) musí být kladné číslo."));
		}

		return count($this->messages) === 0;
	}

	private function getFormValues()
	{

		$row = new stdClass();
		$row->popisProjektu = $_POST[SettingController::$SETTING_DESCRIPTION];
		$row->obrazekProjektu = filter_input(INPUT_POST, SettingController::$SETTING_IMAGE, FILTER_SANITIZE_STRING);
		$row->profilFacebook = filter_input(INPUT_POST, SettingController::$SETTING_FACEBOOK_URL, FILTER_SANITIZE_STRING);
		$row->gmKey = filter_input(INPUT_POST, SettingController::$SETTING_GM_KEY, FILTER_SANITIZE_STRING);
		$row->gmLat = filter_input(INPUT_POST, SettingController::$SETTING_GM_LAT, FILTER_SANITIZE_STRING);
		$row->gmLon = filter_input(INPUT_POST, SettingController::$SETTING_GM_LON, FILTER_SANITIZE_STRING);
		$row->gmZoom = (int)filter_input(INPUT_POST, SettingController::$SETTING_GM_ZOOM, FILTER_SANITIZE_STRING);
		$row->obrazekMapy = filter_input(INPUT_POST, SettingController::$SETTING_MAP_IMAGE, FILTER_SANITIZE_STRING);
		$row->vyhledavaniGoogleKlic = filter_input(INPUT_POST, SettingController::$SETTING_GOOGLE_SEACH, FILTER_SANITIZE_STRING);
		$row->webovaSluzbaCityURL = filter_input(INPUT_POST, SettingController::$SETTING_CITY_API_URL, FILTER_SANITIZE_STRING);
		$row->hledame = $_POST[SettingController::$SETTING_REQUEST];

		return $row;
	}

	public function update()
	{
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

	public function getRow()
	{

		if ($this->nastaveni == null) {
			$this->nastaveni = new stdClass();
			$this->nastaveni->popisProjektu = $this->getSettingValue(SettingController::$SETTING_DESCRIPTION);
			$this->nastaveni->obrazekProjektu = $this->getSettingValue(SettingController::$SETTING_IMAGE);
			$this->nastaveni->profilFacebook = $this->getSettingValue(SettingController::$SETTING_FACEBOOK_URL);
			$this->nastaveni->gmKey = $this->getSettingValue(SettingController::$SETTING_GM_KEY);
			$this->nastaveni->gmLat = $this->getSettingValue(SettingController::$SETTING_GM_LAT);
			$this->nastaveni->gmLon = $this->getSettingValue(SettingController::$SETTING_GM_LON);
			$this->nastaveni->gmZoom = $this->getSettingValue(SettingController::$SETTING_GM_ZOOM);
			$this->nastaveni->obrazekMapy = $this->getSettingValue(SettingController::$SETTING_MAP_IMAGE);
			$this->nastaveni->vyhledavaniGoogleKlic = $this->getSettingValue(SettingController::$SETTING_GOOGLE_SEACH);
			$this->nastaveni->webovaSluzbaCityURL = $this->getSettingValue(SettingController::$SETTING_CITY_API_URL);
			$this->nastaveni->hledame = $this->getSettingValue(SettingController::$SETTING_REQUEST);
		}

		return $this->nastaveni;
	}

	private function getSettingValue($key)
	{
		return $this->dbSetting->getSetting($key)->hodnota;
	}
}

