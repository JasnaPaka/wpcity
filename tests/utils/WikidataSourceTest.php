<?php

include_once "utils/WikidataSource.php";

use PHPUnit\Framework\TestCase;

class WikidataSourceTest extends TestCase
{

	public function testDownloadJSON() {
		$source = new WikidataSource("Q20758044");
		$this->assertTrue($source->getIsOK());

		$this->assertEquals("https://cs.wikipedia.org/wiki/Pomn%C3%ADk_gener%C3%A1la_Pattona", $source->getCsWikiUrl());
	}

	public function testDownloadPamatka() {
		$source = new WikidataSource("Q204871");
		$this->assertTrue($source->getIsOK());
		$this->assertEquals("1000001651", $source->getPamatkovyKatalogId());

		$source = new WikidataSource("Q43453");
		$this->assertTrue($source->getIsOK());
		$this->assertFalse($source->getPamatkovyKatalogId());
	}

	public function testWikidataIdentifier() {
		$this->assertEquals("Q369130", WikidataSource::getWikidataIdentifier("23944/4-1330"));
	}

	/*public function testDataForAuthors() {
		$identifiers = array();
		$identifiers[] = "Q446948";   // Mikoláš Aleš
		$identifiers[] = "Q18745842"; // Břetislav Holakovský

		$authors = WikidataSource::getDataForAuthors($identifiers);
		$this->assertEquals (2, sizeof($authors));
*/
		/*foreach ($authors as $author) {
			echo $author->datum_narozeni."\n";
			$datum = DateTime::createFromFormat(DateTime::ISO8601, substr($author->datum_narozeni, 1));
			echo $datum->format("d. m. Y")."\n";

			//echo $datum."\n";
		}*/
	//}

}