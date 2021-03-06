<?php

include_once $ROOT . "utils/WikidataSource.php";


class WikidataBuilder
{
	private $sourceDb;
	private $sources;

	public function __construct($sourceDb, $sources) {
		$this->sourceDb = $sourceDb;
		$this->sources = $sources;
	}

	public function process() {
		foreach ($this->sources as $source) {
			if ($source->typ != SourceTypes::CODE_WIKIDATA || strlen($source->identifikator) < 3) {
				continue;
			}

			$wd = new WikidataSource($source->identifikator);
			if (!$wd->getIsOK()) {
				return false;
			}

			// 1) Smažeme staré námi zaznamenané zdroje z Wikidata
			if ($source->objekt != null) {
				$this->sourceDb->deleteSystemForObject($source->objekt, SourceTypes::CODE_WIKIDATA);
			} else if ($source->autor != null) {
				$this->sourceDb->deleteSystemForAuthor($source->autor, SourceTypes::CODE_WIKIDATA);
			} else {
				$this->sourceDb->deleteSystemForCollection($source->soubor, SourceTypes::CODE_WIKIDATA);
			}

			// 2) Založíme nové záznamy (pokud existují)
			$pkId = $wd->getPamatkovyKatalogId();
			if (strlen($pkId) > 1) {
				$data = $this->createObject(SourceTypes::CODE_PAMATKOVY_KATALOG, $pkId, null, null,
					SourceTypes::CODE_WIKIDATA, $source->objekt, $source->autor, $source->soubor);
				$this->sourceDb->createWithObject($data, $source->objekt != null, $source->soubor != null);
			}
			
			$dpId = $wd->getDrobnePamatkyId();
			if (strlen($dpId) > 1) {
			    $data = $this->createObject(SourceTypes::CODE_DP, $dpId, null, null,
			        SourceTypes::CODE_WIKIDATA, $source->objekt, $source->autor, $source->soubor);
			    $this->sourceDb->createWithObject($data, $source->objekt != null, $source->soubor != null);
			}

			$cswiki = $wd->getCsWikiUrl();
			if (strlen($cswiki) > 1) {
				$data = $this->createObject(SourceTypes::CODE_CS_WIKI, null, null, $cswiki,
					SourceTypes::CODE_WIKIDATA, $source->objekt, $source->autor, $source->soubor);
				$this->sourceDb->createWithObject($data, $source->objekt != null, $source->soubor != null);
			}

			$monumnetId = $wd->getMonumnetId();
			if (strlen($monumnetId) > 1) {
				$data = $this->createObject(SourceTypes::CODE_MONUMNET, $monumnetId, null, null,
					SourceTypes::CODE_WIKIDATA, $source->objekt, $source->autor, $source->soubor);
				$this->sourceDb->createWithObject($data, $source->objekt != null, $source->soubor != null);
			}

		}

		return true;
	}

	private function createObject($typ, $identifikator, $nazev, $url, $system_zdroj, $objectId, $authorId, $souborId) {
		$objekt = new stdClass();
		$objekt->typ = $typ;
		$objekt->identifikator = $identifikator;
		$objekt->nazev = $nazev;
		$objekt->url = $url;
		$objekt->system_zdroj = $system_zdroj;
		$objekt->cerpano = 0;
		$objekt->deleted = 0;
		$objekt->objekt = $objectId;
		$objekt->autor = $authorId;
		$objekt->soubor = $souborId;

		return $objekt;
	}
}