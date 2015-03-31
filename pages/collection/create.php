<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";
	$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

	include_once $ROOT."controllers/CollectionController.php";
	$controller = new CollectionController();
		
	if (isset($_POST["submit"])) {
		$row = $controller->add();
	}
?>

<div class="wrap">

<h2>Přidání souboru děl</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=collection&amp;action=create" method="post">

<?php include_once $ROOT."/pages/collection/i-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit">
	<a href="admin.php?page=collection" class="button">Zpět na výpis</a>
</p>

</form>

</div>