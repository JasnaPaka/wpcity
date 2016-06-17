<?php

/**
 * Třída na základě souřadnic (lat/long) vrátí informaci o místě (adresa, městská část apod.)
 */
class GoogleGeocode {

   const URL = "https://maps.googleapis.com/maps/api/geocode/xml?&latlng=%LAT%,%LONG%&key=%KEY%";

   private $apikey;

   public function __construct($apiKey) {
       $this->apikey = $apiKey;
   }

   private function getUrl($lat, $long) {
       $url = self::URL;

       $url = str_replace("%LAT%", $lat, $url);
       $url = str_replace("%LONG%", $long, $url);
       $url = str_replace("%KEY%", $this->apikey, $url);

       return $url;
   }

   public function getGeocode($lat, $long) {
       $response = file_get_contents($this->getUrl($lat, $long));
       if ($response == FALSE) {
           return null;
       }
       
       $xml = new SimpleXMLElement($response);
       if ($xml->status != "OK") {
           return null;
       }
       return $xml;
   }
   
   public function getLokalitaMestskaCast($lat, $long) {
        $xml = $this->getGeocode($lat, $long);
        if ($xml == null) {
            return null;
        }
            
        $lokalita = $xml->result[0]->address_component[3]->long_name;
        $mestska_cast = $xml->result[0]->address_component[4]->long_name;
        
        if (strlen($lokalita) > 0 && strlen($mestska_cast) > 0) {
            return array ($lokalita, $mestska_cast);
        }
        
        return null;
   }

}
