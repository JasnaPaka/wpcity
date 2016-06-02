<?php

class StringUtils {

	public static function odstranDiakritiku($text) {
		return strtr($text, 'áäčďéěëíµňôóöŕřšťúůüýžÁÄČĎÉĚËÍĄŇÓÖÔŘŔŠŤÚŮÜÝŽ', 'aacdeeeilnooorrstuuuyzaacdeeelinooorrstuuuyz');
	}
	
}
