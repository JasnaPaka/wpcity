<?php
declare(strict_types=1);

/**
 * Class WikidataIdentifier slouží pro práci s unikátním identifikátorem pro Wikidata. Unikátnost vychází z našich
 * IDček, ke kterým je v případě autorů a souborů děl přičtena konstanta.
 */
class WikidataIdentifier
{

	public const IDENTIFIER_AUTHOR = 100000;
	public const IDENTIFIER_COLLECTION = 1000000;

	/**
	 * Vrátí URL na položku katalogu dle vstupního ID.
	 *
	 * @param int $id
	 * @return string
	 */
	public static function getURLForRedirect(int $id):string {

		if (!self::getIsValidIdentifier($id)) {
			throw new InvalidArgumentException("Vstupni ID neni platne.");
		}

		if ($id > self::IDENTIFIER_COLLECTION) {
			return "/katalog/soubor/".($id - self::IDENTIFIER_COLLECTION)."/";
		}

		if ($id > self::IDENTIFIER_AUTHOR) {
			return "/katalog/autor/".($id - self::IDENTIFIER_AUTHOR)."/";
		}

		return "/katalog/dilo/".$id."/";
	}

	public static function getIdentifierForObject(int $id):int {
		return $id;
	}

	public static function getIdentifierForAuthor(int $id):int {
		return self::IDENTIFIER_AUTHOR + $id;
	}

	public static function getIdentifierForCollection(int $id):int {
		return self::IDENTIFIER_COLLECTION + $id;
	}

	/**
	 * Provede test, zda je vstupní identifikátor platný.
	 *
	 * Pozor: testuje se pouze platnost z hlediska hodnoty, ta neříká nic o tom, zda je k ID záznam v databázi.
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function getIsValidIdentifier(int $id):bool {
		if (self::IDENTIFIER_COLLECTION === $id || self::IDENTIFIER_AUTHOR === $id) {
			return false;
		}

		return $id > 0;
	}

	/**
	 * Provede test, zda je vstupní identifikátor platným identifikátorem Wikidat. Ten má formát "Qčíslo".
	 *
	 * @param string $identifier
	 * @return bool
	 */
	public static function getIsValidWikiDataIdentifier(string $identifier):bool {
		return preg_match("/^Q{1}([0-9]+)$/", $identifier) > 0;
	}
}