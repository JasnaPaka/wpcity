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

	const SITE_CSWIKI_CODE = "cswiki";
	const SITE_COMMONS_CODE = "commonswiki";

	const CLAIMS_PAMATKOVY_KATALOG = "P4075";
	const CLAIMS_MONUMNET = "P762";

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

	/**
	 * Vrátí kód položky Wikidat na základě identifikátoru z Monumentu nebo false, pokud se ho nepodařilo načíst.
	 *
	 * @param $monumnetId
	 * @return bool
	 */
	public static function getWikidataIdentifier($monumnetId) {
		$options = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Accept-language: cs, en\r\n" .
					"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0\r\n"
			)
		);

		$context = stream_context_create($options);
		$json = file_get_contents(sprintf(self::SEARCH_URL, $monumnetId), false, $context);
		if (!$json) {
			return false;
		}

		if (!self::getIsJson($json)) {
			return false;
		}

		$jsonData = json_decode($json);

		return $jsonData->origin->qid;
	}
}