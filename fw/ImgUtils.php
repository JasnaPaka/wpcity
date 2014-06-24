<?php

class ImgUtils {

	public static function resizeToProporcional($width, $height, $maxWidth, $maxHeight) {
	
		$size = array();
	
		$propWidth = $width / $maxWidth;
		$propHeight = $height / $maxHeight;
		
		if ($propWidth <= 1 && $propHeight <= 1) {
			$size["width"] = $width;
			$size["height"] = $height;
			
			return $size;
		}
		
		$prop = ($propWidth > $propHeight ? $propWidth : $propHeight);
	
		$size['width'] =	round($width / $prop);
		$size['height'] = round($height / $prop);
		
		return $size;
	}
	
}

?>