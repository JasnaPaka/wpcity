<?php

include_once $ROOT . "utils/SourceTypes.php";

class SourceTypes
{
	const CODE_WIKIDATA = "WIKIDATA";
	const CODE_KNIHA = "KNIHA";
	const CODE_PAMATKOVY_KATALOG = "PAMATKOVY_KATALOG";
	const CODE_CS_WIKI = "CS_WIKI";
	const CODE_MONUMNET = "MONUMNET";

	private static $instance;

	private $values;

	public function __construct()
	{
		$this->values = array(
			new SourceType(self::CODE_KNIHA, "Kniha",
				null, null),
			new SourceType(self::CODE_WIKIDATA, "Wikidata",
				"%s na serveru Wikidata", "https://www.wikidata.org/wiki/%s"),
			new SourceType(self::CODE_PAMATKOVY_KATALOG, "Památkový katalog",
				"%s v Památkovém katalogu",
				"http://pamatkovykatalog.cz/?mode=parametric&presenter=ElementsResults&catalogNumber=%s", true),
			new SourceType(self::CODE_CS_WIKI, "Česká Wikipedie",
				"%s na České Wikipedii",null, true),
			new SourceType(self::CODE_MONUMNET, "Monumnet",
				"%s na MonumNetu","http://monumnet.npu.cz/pamfond/list.php?CiRejst=%s", true)
		);
	}

	public static function getInstance()
	{
		if ( is_null( self::$instance ) )
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getValues() {
		return $this->values;
	}

	public function getNonSystemValues() {
		$values = array();
		foreach ($this->values as $value) {
			if ($value->getSystem()) {
				continue;
			}

			$values[] = $value;
		}

		return $values;
	}

	public function getSystemValues() {
		$values = array();
		foreach ($this->values as $value) {
			if (!$value->getSystem()) {
				continue;
			}

			$values[] = $value;
		}

		return $values;
	}

	public function getIsCodeValid($code) {
		return $this->getSourceType($code) != null;
	}

	public function getSourceType($code) {
		foreach ($this->values as $value) {
			if ($value->getCode() === $code) {
				return $value;
			}
		}

		return null;
	}

	public function validate($source) {
		$messages = array();

		// pokud je zvolen typ, musí být i identifikátor
		if (strlen($source->typ) > 0 && strlen(trim($source->identifikator)) == 0) {
			$sc = $this->getSourceType($source->typ);
			array_push($messages,
				new JPErrorMessage("Je zvolen typ (zdroj), ale není vyplněn identifikátor. Typ: ".$sc->getName()."."));
		}

		// pokud se jedná o Wikidata, nevyplňuje se ani název, ani url
		if ($source->typ === self::CODE_WIKIDATA && (strlen($source->nazev) > 0 || strlen($source->url) > 0)) {
			array_push($messages,
				new JPErrorMessage("U zdroje 'Wikidata' vyplňte pouze identifikátor. Zbytek není nutný."));
		}

		// u knih musí být název vyplněn
		if ($source->typ === self::CODE_KNIHA && strlen($source->nazev) == 0) {
			array_push($messages,
				new JPErrorMessage("U zdroje 'Kniha' je potřeba uvést položku název."));
		}

		return $messages;
	}

	public function getIsKniha($code) {
		return $code === self::CODE_KNIHA;
	}
}