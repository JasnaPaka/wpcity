<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/AuthorController.php";
	$controller = new AuthorController();
		
	if (isset($_POST["submit"])) {
		$row = $controller->add();
	}
?>

<div class="wrap">

<h2>Přidání autora</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=author&amp;action=create" method="post">

<?php include_once $ROOT."/pages/author/i-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit">
	<a href="admin.php?page=author" class="button">Zpět na výpis</a>
</p>

</form>

</div>