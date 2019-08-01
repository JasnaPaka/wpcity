<?php

/**
 * Třída zapouzdřuje strojovou komunikaci s Wikidaty. Umožňuje dohledat položku ve Wikidatech na základě identifikátoru
 * z Monumentu a přečíst zajímavé hodnoty z položky Wikidat.
 *
 * Použití:
 * - statická metoda getWikidataIdentifier() - podle identifikátoru z Monumnetu (ala "23944/4-1330") dohledá kód z Wikidat.
 * - instanci WikidataSource se podstrčí identifikátor Wikidat (Qcislo) a pak lze číst hodnoty, které se nám hodí do katalogu.
 *   Po instanci je dobré pomocí metody getIsOK() ověřit, zda se JSON v pořádku načetl.
 */
class WikidataSource
{
	const DATA_URL = "https://www.wikidata.org/wiki/Special:EntityData/%s.json";
	const SEARCH_URL = "https://tools.wmflabs.org/hub/P762:%s?format=json";

	const SPARQL_URL = "https://query.wikidata.org/sparql?query=%s&format=json";
	const SPARQL_AUTHOR_QUERY = 'SELECT ?item ?itemLabel ?datum_n ?datum_u ?misto_n ?misto_nLabel ?misto_uLabel ?abart
								{
								  VALUES ?item { %s } .
								  OPTIONAL { ?item wdt:P569 ?datum_n } .
								  OPTIONAL { ?item wdt:P570 ?datum_u } .
								  OPTIONAL { ?item wdt:P19 ?misto_n } .
								  OPTIONAL { ?item wdt:P20 ?misto_u } .
								  OPTIONAL { ?item wdt:P6844 ?abart } .
								  SERVICE wikibase:label { bd:serviceParam wikibase:language "cs,en" } .
								}';

	const SPARQL_ITEMS_P4935 = "SELECT ?item ?itemLabel ?kvItem
								WHERE
								{
								  ?item wdt:P4935 ?kvItem
								  SERVICE wikibase:label { bd:serviceParam wikibase:language \"cs,en\" }
								}";

	const SPARQL_ITEMS_INFO = "SELECT ?item ?itemLabel
								{
								  VALUES ?item { %s } .
								  SERVICE wikibase:label { bd:serviceParam wikibase:language \"cs,en\" }
								}";
	
	const SITE_CSWIKI_CODE = "cswiki";
	const SITE_COMMONS_CODE = "commonswiki";

	const CLAIMS_PAMATKOVY_KATALOG = "P4075";
	const CLAIMS_MONUMNET = "P762";
	const CLAIMS_DROBNE_PAMATKY = "P6736";

	private $identifikator;
	private $jsonData;
	private $ok;

	public function __construct($identifikator)
	{
		$this->identifikator = $identifikator;
		$this->init();
	}

	protected function init() {
		$this->ok = true;

		$content = file_get_contents(sprintf(self::DATA_URL, $this->identifikator));
		if (!$content) {
			$this->ok = false;
		} else {
			if (!$this->getIsJson($content)) {
				$this->ok = false;
			} else {
				$this->jsonData = json_decode($content);
			}
		}
	}

	/**
	 * Vrací true, pokud se JSON k položce Wikidat úspěšně načetl.
	 *
	 * @return mixed
	 */
	public function getIsOK() {
		return $this->ok;
	}

	private static function getIsJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * Vrátí url k článku na České Wikipedii nebo false, pokud se ho nepodařilo nalézt.
	 * @return bool
	 */
	public function getCsWikiUrl() {
		return $this->getSiteUrl(self::SITE_CSWIKI_CODE);
	}

	/**
	 * Vrátí url k článku na Wikimedia Commons nebo false, pokud se ho nepodařilo nalézt.
	 * @return bool
	 */
	public function getCsWikimediaUrl() {
		return $this->getSiteUrl(self::SITE_COMMONS_CODE);
	}

	/**
	 * Vrátí identifikátor památky v Památkovém katalogu nebo false, pokud nebyl nalezen.
	 * @return bool
	 */
	public function getPamatkovyKatalogId() {
		return $this->getClaimId(self::CLAIMS_PAMATKOVY_KATALOG);
	}
	
	public function getDrobnePamatkyId() {
	    return $this->getClaimId(self::CLAIMS_DROBNE_PAMATKY);
	}

	/**
	 * Vrátí identifikátor památky v Monumnetu nebo false, pokud nebyl nalezen.
	 * @return bool
	 */
	public function getMonumnetId() {
		return $this->getClaimId(self::CLAIMS_MONUMNET);
	}

	private function getSiteUrl($siteCode) {
		$items = $this->jsonData->entities;
		if (is_array($items) && sizeof($items) !== 1) {
			return false;
		}

		foreach ($items as $item) {
			$sitelinks = $item->sitelinks;
			foreach ($sitelinks as $sitelink) {
				if ($sitelink->site === $siteCode) {
					return $sitelink->url;
				}
			}
		}

		return false;
	}

	private function getClaimId($siteId) {
		$items = $this->jsonData->entities;
		if (is_array($items) && sizeof($items) !== 1) {
			return false;
		}

		foreach ($items as $item) {
			$claims = $item->claims;
			foreach ($claims as $claim) {
				foreach ($claim as $data) {
					$rejstrikId = $data->mainsnak->property;
					if ($rejstrikId === $siteId) {
						return $data->mainsnak->datavalue->value;
					}
				}
			}
		}

		return false;
	}

	private static function createContext() {
		$options = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: cs, en\r\n" .
					"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0\r\n"
			)
		);

		$context = stream_context_create($options);
		return $context;
	}

	/**
	 * Vrátí kód položky Wikidat na základě identifikátoru z Monumentu nebo false, pokud se ho nepodařilo načíst.
	 *
	 * @param $monumnetId
	 * @return bool
	 */
	public static function getWikidataIdentifier($monumnetId) {

		$json = file_get_contents(sprintf(self::SEARCH_URL, $monumnetId), false,
				self::createContext());
		if (!$json) {
			return false;
		}

		if (!self::getIsJson($json)) {
			return false;
		}

		$jsonData = json_decode($json);

		return $jsonData->origin->qid;
	}

	/**
	 * Vytvoří dotaz na údaje o autorech ve Wikidatech.
	 *
	 * @param string $query vstupní dotaz (spíš jeho kostra)
	 * @param array $ids seznam identifikátorů
	 * @return string
	 */
	private static function prepareSPAROLQuery(string $query, array $ids):string {
		$idsStr = "";
		foreach ($ids as $id) {
			$idsStr = $idsStr." wd:".$id;
		}

		$query = sprintf($query, $idsStr);

		//$query = trim(preg_replace('/\s\s+/', ' ', $query));
		$query = urlencode($query);

		return $query;
	}

	/**
	 * Vrátí informace o autorech ve Wikidatech dle vstupních identifikátorů Wikidat
	 *
	 * @param array $ids seznam identifikátorů Wikidat, pro které zjišťujeme informace
	 * @return array
	 */
	public static function getInfoAuthors(array $ids):array {
		$data = array();

		$query = self::prepareSPAROLQuery(self::SPARQL_AUTHOR_QUERY, $ids);
		$jsonStr = file_get_contents(sprintf(self::SPARQL_URL, $query), false, self::createContext());
		$json = json_decode($jsonStr);

		foreach ($json->results->bindings as $item) {
			$obj = new stdClass();

			if ($item->datum_n != null) {
				$obj->datumNarozeni = new DateTime($item->datum_n->value);
			}
			if ($item->datum_u != null) {
				$obj->datumUmrti = new DateTime($item->datum_u->value);
			}
			if ($item->misto_nLabel != null) {
				$obj->mistoNarozeni = $item->misto_nLabel->value;
			}
			if ($item->misto_uLabel != null) {
				$obj->mistoUmrti = $item->misto_uLabel->value;
			}
			if ($item->abart != null) {
			    $obj->abart = $item->abart->value;
            }

			$wdUrl = $item->item->value;
			$parts = explode("/", $wdUrl);
			$obj->identifikator = $parts[sizeof($parts) - 1];

			$data[] = $obj;
		}

		return $data;
	}


	public static function getWDItems():array {
		$items = array();

		$query = urlencode(self::SPARQL_ITEMS_P4935);
		$jsonStr = file_get_contents(sprintf(self::SPARQL_URL, $query),
				false, self::createContext());
		$json = json_decode($jsonStr);

		foreach ($json->results->bindings as $item) {
			$obj = new stdClass();
			$wdUrl = $item->item->value;
			$parts = explode("/", $wdUrl);
			$obj->nazev =  $item->itemLabel->value;
			$obj->identifikator = $parts[sizeof($parts) - 1];

			$items[] = $obj;
		}

		return $items;
	}

	/**
	 * Vrátí informace o heslech na Wikidatech.
	 *
	 * @param array $ids identifikátory Wikidat, ke kterým se dohledává informace.
	 * @return array
	 */
	public static function getWDItemsInfo(array $ids):array {
		$data = array();

		$idsStr = "";
		foreach ($ids as $id) {
			$idsStr = $idsStr." wd:".$id;
		}

		$query = urlencode(sprintf(self::SPARQL_ITEMS_INFO, $idsStr));
		$jsonStr = file_get_contents(sprintf(self::SPARQL_URL, $query),
			false, self::createContext());
		$json = json_decode($jsonStr);

		foreach ($json->results->bindings as $item) {
			$obj = new stdClass();
			$wdUrl = $item->item->value;
			$parts = explode("/", $wdUrl);
			$obj->identifikator = $parts[sizeof($parts) - 1];
			$obj->nazev = $item->itemLabel->value;

			$data[] = $obj;
		}

		return $data;
	}

}

