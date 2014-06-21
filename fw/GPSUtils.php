<?php

class GPSUtils {

  public static function getIsValidLatitude($latitude){
    if (preg_match("/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}$/", $latitude)) {
        return true;
    } else {
        return false;
    }
  }
  
  
  public static function getIsValidLongitude($longitude){
    if(preg_match("/^-?([1]?[1-7][1-9]|[1]?[1-8][0]|[1-9]?[0-9])\.{1}\d{1,6}$/",
      $longitude)) {
       return true;
    } else {
       return false;
    }
  }
  
}
  
?>