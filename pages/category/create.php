<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/CategoryController.php";
	$controller = new CategoryController();
		
	if (isset($_POST["submit"])) {
		$row = $controller->add();
	}
?>

<div class="wrap">

<h2>Přidání kategorie</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=category&amp;action=create" method="post">

<?php include_once $ROOT."/pages/category/i-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit">
	<a href="admin.php?page=category" class="button">Zpět na výpis</a>
</p>

</form>

</div>