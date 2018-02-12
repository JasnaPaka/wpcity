<?php


class ObjectSourceController extends ObjectController
{

	private function getFormSourcesValues()
	{
		$sources = array();

		foreach ($_POST as $key => $value) {
			$pos = strpos($key, "zdroj");

			if ($pos === 0) {

				$source = new stdClass();
				$id = (int)filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
				if ($id > 0) {
					$source->id = $id;
				}

				$source->typ = filter_input(INPUT_POST, "typ" . $value, FILTER_SANITIZE_STRING);
				$source->identifikator = filter_input(INPUT_POST, "identifikator" . $value, FILTER_SANITIZE_STRING);

				$source->nazev = filter_input(INPUT_POST, "nazev" . $value, FILTER_SANITIZE_STRING);
				$source->url = filter_input(INPUT_POST, "url" . $value, FILTER_SANITIZE_STRING);

				$source->cerpano = filter_input(INPUT_POST, "cerpano" . $value, FILTER_SANITIZE_STRING);
				$source->cerpano = ($source->cerpano === "on" ? 1 : 0);

				$source->deleted = filter_input(INPUT_POST, "deleted" . $value, FILTER_SANITIZE_STRING);
				$source->deleted = ($source->deleted === "on" ? 1 : 0);
				$source->objekt = $this->getObjectId();
				$source->autor = null;
				$source->system_zdroj = null;

				array_push($sources, $source);
			}
		}

		return $sources;
	}

	private function validateSources($sources)
	{
		foreach ($sources as $source) {
			if (!isset($source->id) && strlen($source->nazev) == 0 && strlen($source->url) > 0) {
				array_push($this->messages,
					new JPErrorMessage("Každý zdroj, který má zadáno URL, musí mít i název."));
			}

			if (strlen($source->typ) > 0) {
				$sc = SourceTypes::getInstance()->getSourceType($source->typ);
				if ($sc == null) {
					array_push($this->messages,
						new JPErrorMessage("Neznámý typ zdroje."));
				} else if (!$source->deleted) {
					$results = SourceTypes::getInstance()->validate($source);
					if (sizeof($results) > 0) {
						foreach ($results as $result) {
							array_push($this->messages, $result);
						}
					}
				}
			}
		}

		return count($this->messages) === 0;
	}

	public function getSelectedSources()
	{
		$sources = array();
		foreach ($this->getSourcesForObject() as $source) {
			array_push($sources, $source);
		}

		// doplníme pět dalších
		for ($i = 1; $i <= 5; $i++) {
			array_push($sources, 0);
		}

		return $sources;
	}


	public function manageSources()
	{
		$sources = $this->getFormSourcesValues();
		if (count($sources) == 0) {
			return $this->getSelectedSources();
		}

		$result = $this->validateSources($sources);
		if ($result) {
			foreach ($sources as $source) {
				if (strlen($source->typ) == 0 && strlen($source->nazev) == 0) {
					continue;
				}

				if (isset($source->id)) {
					$result = $this->dbSource->updateWithObject($source, $source->id, true);
				} else {
					$result = $this->dbSource->createWithObject($source, true);
				}
			}

			// Zpracujeme identifikatory
			$wb = new WikidataBuilder($this->dbSource, $sources);
			if (!$wb->process()) {
				array_push($this->messages, new JPErrorMessage("Nepodařilo se zaktualizovat zdroje z Wikidat."));
			}

			array_push($this->messages, new JPInfoMessage('Zdroje byly aktualizovány.
                    <a href="' . $this->getUrl(JPController::URL_VIEW) . '">Zobrazit detail</a>'));

			return $this->getSelectedSources();
		}

		return $sources;
	}

	public function getAllSourceTypes() {
		return SourceTypes::getInstance()->getNonSystemValues();
	}
}