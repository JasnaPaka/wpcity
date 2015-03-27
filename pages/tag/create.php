<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/TagController.php";
	$controller = new TagController();
		
	if (isset($_POST["submit"])) {
		$row = $controller->add();
	}
?>

<div class="wrap">

<h2>Přidání štítku</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=tag&amp;action=create" method="post">

<?php include_once $ROOT."/pages/tag/i-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit">
	<a href="admin.php?page=tag" class="button">Zpět na výpis</a>
</p>

</form>

</div>