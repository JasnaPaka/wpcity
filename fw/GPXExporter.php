<?php

/**
 * Class GPXExporter slouží k exportu bodů (POI) do formátu GPX 1.1.
 */
class GPXExporter
{
	const XML_HEADER = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

	const GPX_HEADER = "<gpx creator=\"GPXExporter\" version=\"1.1\" 
                        xmlns=\"http://www.topografix.com/GPX/1/1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" 
                        xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd\">";
	const GPX_FOOTER = "</gpx>";

	private $pois = array();

	public function addPoi($lat, $lon, $name)
	{

		$poi = array($lat, $lon, $name);

		$this->pois[] = $poi;
	}

	/**
	 * Vrací obsah vygenerovaného souboru ve formátu GPX.
	 *
	 * @return string
	 */
	public function getGpxContent()
	{
		$content = self::XML_HEADER . "\n";
		$content .= self::GPX_HEADER . "\n";

		// poi
		foreach ($this->pois as $poi) {
			$content .= sprintf("<wpt lat=\"%f\" lon=\"%f\">\n", $poi[0], $poi[1]);
			$content .= sprintf("    <name>%s</name>\n", $this->removeInvalidXMLChars($poi[2]));
			$content .= "</wpt>\n";
		}

		$content .= self::GPX_FOOTER . "\n";

		return $content;
	}

	public function download($filename)
	{
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Type: application/gpx+xml');
		header('Content-Length: ' . strlen($this->getGpxContent()));
		header('Connection: close');

		print $this->getGpxContent();
	}

	/**
	 * Provede odstranění znaků, které nemohou být v textu v XML souboru.
	 *
	 * @param $str původní řetězec
	 * @return mixed|string opravený řetězec
	 */
	private function removeInvalidXMLChars($str)
	{
		// http://www.xiven.com/weblog/2013/08/30/PHPInvalidUTF8InXMLRevisited
		// Strip invalid UTF-8 byte sequences - this part may not be strictly necessary, could be separated to another function
		$str = mb_convert_encoding(mb_convert_encoding($str, 'UTF-16', 'UTF-8'), 'UTF-8', 'UTF-16');

		// Remove various characters not allowed in XML
		$str = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $str);

		return $str;
	}
}