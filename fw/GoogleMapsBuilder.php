<?php

class GoogleMapsBuilder {
	private $apiKey;
	private $lat;
	private $lng;
	
	function __construct($apiKey, $lat, $lng) {
		$this->apiKey = $apiKey;
		$this->lat = $lat;
		$this->lng = $lng;
	}
	
	public function getOutput() {
		$content = file_get_contents(plugin_dir_path( __FILE__ )."templates/google-maps.tpl");
		
		$content = str_replace("KEY_REPLACEMENT", $this->apiKey, $content);
		$content = str_replace("LNG_REPLACEMENT", $this->lng, $content);
		$content = str_replace("LAT_REPLACEMENT", $this->lat, $content);
		
		return $content;
	}
	
	public function getOutputEdit() {
		$content = file_get_contents(plugin_dir_path( __FILE__ )."templates/google-maps-edit.tpl");
		
		$content = str_replace("KEY_REPLACEMENT", $this->apiKey, $content);
		$content = str_replace("LNG_REPLACEMENT", isset($this->lng) ? $this->lng : 0, $content);
		$content = str_replace("LAT_REPLACEMENT", isset($this->lat) ? $this->lat : 0, $content);
		
		return $content;
	}	
}

?>