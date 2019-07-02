<?php

$ROOT = plugin_dir_path( __FILE__ )."../";
include_once $ROOT . "utils/WikidataSource.php";

/**
 * Class WikidataDiffBuilder slouží k porovnání hodnot u nás v databázi s těmi, co jsou vedeny ve Wikidatech. Porovnání
 * je realizováno oběma směry.
 */
class WikidataDiffBuilder
{

	private static function findAuthorWD(array $authorsWD, string $identifier) {
		foreach ($authorsWD as $authorWD) {
			if ($authorWD->identifikator === $identifier) {
				return $authorWD;
			}
		}

		return null;
	}

	private static function compareStringValue($item, string $description, $dbValue, $wdValue, bool $strict):bool {
		if ($dbValue == null && $wdValue == null) {
			return false;
		}

		if ((isset($wdValue) && strlen(trim($dbValue)) == 0)
			|| (!isset($wdValue) && strlen(trim($dbValue)) > 0)
			|| ($strict && trim(strtolower($dbValue)) !== trim(strtolower($wdValue)))
			|| (!$strict && strpos($dbValue, $wdValue) === false && strpos($wdValue, $dbValue) === false)) {

			$item->popis = $description;
			$item->ourValue = $dbValue;
			$item->wdValue = $wdValue;
			return true;
		}

		return false;
	}

	private static function compareDateValue($item, string $description, $dbValue, $wdValue):bool {
		if ($dbValue == null && $wdValue == null) {
			return false;
		}

		if ((isset($wdValue) && !isset($dbValue))
			|| (!isset($wdValue) && isset($dbValue))
			|| (($wdValue)->format("Y-m-d") !== (new DateTime($dbValue))->format("Y-m-d"))) {

			$item->popis = $description;
			$item->ourValue = $dbValue;
			$item->wdValue = $wdValue;
			return true;
		}

		return false;
	}


	private static function createDiffObject($author) {
		$item = new stdClass();
		$item->nalezen = true;
		$item->id = $author->id;
		$item->jmeno = $author->jmeno;
		$item->prijmeni = $author->prijmeni;
		$item->identifikator = $author->identifikator;
		return $item;
	}

	/**
	 * Porovná údaje o autorech u nás a ve Wikidatech (oba směry).
	 *
	 * @param array $authors autoři s identifikátorem Wikidat v naší databázi
	 * @param array $authorsWD informace o autorech stažené z Wikidat.
	 * @return array pole se změnami
	 */
	public static function getAuthorsDiff(array $authors, array $authorsWD):array {

		$data = array();

		foreach ($authors as $author) {
			$item = self::createDiffObject($author);

			$authorWD = self::findAuthorWD($authorsWD, $author->identifikator);
			if ($authorWD == null) {
				$item->nalezen = false;
				$data[] = $item;
				continue;
			}

			// místo narození
			if (self::compareStringValue($item, "Místo narození", $author->misto_narozeni,
					$authorWD->mistoNarozeni, false)) {
				$data[] = $item;
				$item = self::createDiffObject($author);
			}

			// místo úmrtí
			if (self::compareStringValue($item, "Místo úmrtí", $author->misto_umrti,
					$authorWD->mistoUmrti, false)) {
				$data[] = $item;
				$item = self::createDiffObject($author);
			}

			// datum narození
			if (self::compareDateValue($item, "Datum narození", $author->datum_narozeni,
					$authorWD->datumNarozeni)) {
				$data[] = $item;
				$item = self::createDiffObject($author);
			}

			// datum úmrtí
			if (self::compareDateValue($item, "Datum úmrtí", $author->datum_umrti,
				$authorWD->datumUmrti)) {
				$data[] = $item;
				$item = self::createDiffObject($author);
			}

            // AbArt
            if (self::compareStringValue($item, "Identifikátor abArt", $author->abart,
                $authorWD->abart, false)) {
                $data[] = $item;
                $item = self::createDiffObject($author);
            }
		}

		return $data;
	}

	/**
	 * Vrátí položky, kde chybí informace o provázání z jedné strany (u nás či ve Wikidatech).
	 *
	 * @param array $dbItems objekty s identifikátory u nás
	 * @param array $wdItems objekty na Wikidatech
	 * @return array
	 */
	public static function getWDIdentifiersDiff(array $dbItems, array $wdItems) {
		$data = array();
		$duplicates = array();

		// kontrola, zda nechybí identifikátory na straně WikiDat
		foreach ($dbItems as $item) {
			if (array_search($item->wdIdentifikator, $duplicates) !== false) {
				continue;
			}
			$duplicates[] = $item->wdIdentifikator;

			$tempItem = null;
			foreach ($wdItems as $wdItem) {
				if ($wdItem->identifikator === $item->wdIdentifikator) {
					$tempItem = $wdItem;
				}
			}

			if ($tempItem == null) {
				$obj = new stdClass();
				$obj->wdIdentifikator = $item->wdIdentifikator;
				$obj->dbNazev = $item->dbNazev;
				$obj->dbIdentifikator = $item->dbIdentifikator;
				$obj->chybiNaWD = true;

				foreach($wdItems as $tempItem) {
					if ($tempItem->identifikator === $item->wdIdentifikator) {
						$obj->wdNazev = $tempItem->nazev;
					}
				}

				$data[] = $obj;
			}
		}

		// kontrola, zda nechybí identifikátory na naší straně
		$duplicates = array();
		foreach($wdItems as $wdItem) {
			if (array_search($wdItem->identifikator, $duplicates) !== false) {
				continue;
			}
			$duplicates[] = $wdItem->identifikator;

			$tempItem = null;
			foreach ($dbItems as $dbItem) {
				if ($dbItem->wdIdentifikator === $wdItem->identifikator) {
					$tempItem = $dbItem;
				}
			}

			if ($tempItem == null) {
				$obj = new stdClass();
				$obj->wdNazev = $wdItem->nazev;
				$obj->wdIdentifikator = $wdItem->identifikator;
				$obj->chybiNaWD = false;
				$data[] = $obj;
			}
		}

		// doplníme názvy položek ve Wikidatech, kde nejsou uvedeny
		$ids = array();
		foreach ($data as $item) {
			if (!isset($item->wdNazev)) {
				$ids[] = $item->wdIdentifikator;
			}
		}

		$items = WikidataSource::getWDItemsInfo($ids);
		foreach ($data as $item) {
			if (!isset($item->wdNazev)) {
				$temp = null;
				foreach ($items as $i) {
					if ($i->identifikator === $item->wdIdentifikator) {
						$temp = $i;
					}
				}

				if ($temp != null) {
					$item->wdNazev = $temp->nazev;
				}
			}
		}

		return $data;
	}
}