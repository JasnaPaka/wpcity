<?php
declare(strict_types=1);

include_once "utils/WikidataIdentifier.php";

use PHPUnit\Framework\TestCase;

class WikidataIdentifierTest extends TestCase
{

	public function testGetURLForRedirect() {
		$this->assertEquals("/katalog/dilo/1/", WikidataIdentifier::getURLForRedirect(1));
		$this->assertEquals("/katalog/autor/1/", WikidataIdentifier::getURLForRedirect(100001));
		$this->assertEquals("/katalog/soubor/1/", WikidataIdentifier::getURLForRedirect(1000001));
	}

	public function testGetURLForRedirectWrongArgument() {
		$this->expectException(InvalidArgumentException::class);
		WikidataIdentifier::getURLForRedirect(0);
	}

	public function testGetURLForRedirectWrongArgument2() {
		$this->expectException(InvalidArgumentException::class);
		WikidataIdentifier::getURLForRedirect(-1);
	}

	public function testGetIdentifierForObject() {
		$this->assertEquals(1, WikidataIdentifier::getIdentifierForObject(1));
	}

	public function testGetIdentifierForAuthor() {
		$this->assertEquals(100001, WikidataIdentifier::getIdentifierForAuthor(1));
	}

	public function testGetIdentifierForCollection() {
		$this->assertEquals(1000001, WikidataIdentifier::getIdentifierForCollection(1));
	}

	public function testGetIsValidIdentifier() {
		$this->assertTrue(WikidataIdentifier::getIsValidIdentifier(9));
		$this->assertFalse(WikidataIdentifier::getIsValidIdentifier(0));
		$this->assertFalse(WikidataIdentifier::getIsValidIdentifier(-5));
		$this->assertFalse(WikidataIdentifier::getIsValidIdentifier(WikidataIdentifier::IDENTIFIER_AUTHOR));
		$this->assertFalse(WikidataIdentifier::getIsValidIdentifier(WikidataIdentifier::IDENTIFIER_COLLECTION));
	}

	public function testGetIsValidWikiDataIdentifier() {
		$this->assertTrue(WikidataIdentifier::getIsValidWikiDataIdentifier("Q123"));
		$this->assertFalse(WikidataIdentifier::getIsValidWikiDataIdentifier(""));
		$this->assertFalse(WikidataIdentifier::getIsValidWikiDataIdentifier("Q123abc"));
		$this->assertFalse(WikidataIdentifier::getIsValidWikiDataIdentifier("AQ123"));
	}

}