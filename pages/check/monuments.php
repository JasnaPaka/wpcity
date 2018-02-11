<?php //
	$ROOT = plugin_dir_path( __FILE__ )."../../";
	$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

	include_once $ROOT."controllers/CheckController.php";
	$controller = new CheckController();

	$rows = $controller->getMonuments();
?>

<div class="wrap">

	<h1>Doplnění odkaz na Wikidata pro památky</h1>

	<?php
		foreach ($rows as $row) {
			$wikiId = $controller->findMonument($row->pamatkova_ochrana);
			if (strlen($wikiId) < 2) {
				print ($row->id." - nezdařilo se<br />");
			} else {
				$result = $controller->processMonument($row, $wikiId);
				if ($result) {
					print ($row->id." - OK<br />");
				} else {
					print ($row->id." - CHYBA<br />");
				}
			}
		}
	?>

</div>