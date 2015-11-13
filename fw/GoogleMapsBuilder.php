<?php

class GoogleMapsBuilder {
	private $KV_SETTINGS;
	private $lat;
	private $lng;
	
	function __construct($KV_SETTINGS, $lat, $lng) {
		$this->KV_SETTINGS = $KV_SETTINGS;
		$this->lat = $lat;
		$this->lng = $lng;
	}
	
	public function getOutput() {
		$content = file_get_contents(plugin_dir_path( __FILE__ )."templates/google-maps.tpl");
		
		$content = str_replace("KEY_REPLACEMENT", $this->KV_SETTINGS["gm_key"], $content);
		$content = str_replace("LNG_REPLACEMENT", $this->KV_SETTINGS["gm_lng"], $content);
		$content = str_replace("LAT_REPLACEMENT", $this->KV_SETTINGS["gm_lat"], $content);
                $content = str_replace("ZOOM_REPLACEMENT", $this->KV_SETTINGS["gm_zoom"], $content);
		
                $content = str_replace("LNG_POI_REPLACEMENT", isset($this->lng) ? $this->lng : 0, $content);
		$content = str_replace("LAT_POI_REPLACEMENT", isset($this->lat) ? $this->lat : 0, $content);
		
                return $content;
	}
	
	public function getOutputEdit() {
		$content = file_get_contents(plugin_dir_path( __FILE__ )."templates/google-maps-edit.tpl");
                
		$content = str_replace("KEY_REPLACEMENT", $this->KV_SETTINGS["gm_key"], $content);
		$content = str_replace("LNG_REPLACEMENT", $this->KV_SETTINGS["gm_lng"], $content);
		$content = str_replace("LAT_REPLACEMENT", $this->KV_SETTINGS["gm_lat"], $content);
                $content = str_replace("ZOOM_REPLACEMENT", $this->KV_SETTINGS["gm_zoom"], $content);
                
		$content = str_replace("LNG_POI_REPLACEMENT", isset($this->lng) ? $this->lng : 0, $content);
		$content = str_replace("LAT_POI_REPLACEMENT", isset($this->lat) ? $this->lat : 0, $content);
		
		return $content;
	}	
}

