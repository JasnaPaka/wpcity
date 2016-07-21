<?php

/**
 * Class CityService umožňuje zavolat webovou službu pro zjištění městského obvodu na základě souřadnic. Základem je
 * metoda "call()", která vrací navrácené XML. Pomocí metody "getStatusCode()" pak lze zjistit, jak bylo volání
 * úspěšné.
 *
 * @author Pavel Cvrček
 */
class CityService
{
	const HTTP_200 = "200";
	const HTTP_404 = "404";
	const HTTP_500 = "500";

	private $url;
	private $output;
	private $statusCode;

	public function __construct($url)
	{
		$this->url = $url;
	}

	private function getFullUrl($lat, $long) {
		return $this->url."?lat=".$lat."&long=".$long;
	}

	/**
	 * Provede volání webové služby.
	 *
	 * @param $lat první část souřadnice
	 * @param $long druhá část souřadnice
	 * @return string třídu s navrácenými hodnotami
	 */
	public function call($lat, $long) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->getFullUrl($lat,$long));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$this->output = curl_exec($curl);
		$this->statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		return $this->processOutput();
	}

	/**
	 * @return string XML z posledního volání
	 */
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * @return int stavový kód posledního volání
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	private function processOutput()
	{
		$result = new stdClass();
		$xml = simplexml_load_string($this->output);

		$result->code = $xml->code;

		if ($this->statusCode == self::HTTP_200) {
			$result->umo = $xml->umo;
		} else {
			$result->msg = $xml->msg;
		}

		return $result;
	}

}