<?php

include_once $ROOT . "utils/License.php";


class Licenses
{

	private $licenses = array();

	/**
	 * Licenses constructor.
	 * @param $licenses
	 */
	public function __construct()
	{
		$this->licenses[] = new License("1", "Creative Commons Attribution-ShareAlike 4.0 (výchozí)",
			"CC-BY-SA 4.0", "https://creativecommons.org/licenses/by-sa/4.0/", 10);
		$this->licenses[] = new License("2", "Soukromá fotografie (archiv, sběratel)",
			null, null, 20);
		$this->licenses[] = new License("3", "Creative Commons Attribution-ShareAlike 3.0 Unported",
			"CC BY-SA 3.0", "https://creativecommons.org/licenses/by-sa/3.0/deed.en", 30);
		$this->licenses[] = new License("4", "Creative Commons Attribution-ShareAlike 2.5 Generic",
			"CC BY-SA 2.5", "https://creativecommons.org/licenses/by-sa/2.5/deed.en", 40);
	}

	public function getLicenses() {
		return $this->licenses;
	}


}