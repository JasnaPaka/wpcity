<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/CategoryController.php";
	$controller = new CategoryController();
	
	if (isset($_POST["submit"])) {
		$row = $controller->update();
	} else {
		$row = $controller->getObjectFromUrl();
	}
?>

<div class="wrap">

<?php 
	if ($row == null) {
?>

<div class="error below-h2">
	<p>Kategorie nebyla nalezena. </p>
	
	<p><a href="admin.php?page=category">Zpět na výpis</a></p>
</div>

<?php 
	} else if ($row->systemova) {
?>

<div class="error below-h2">
	<p>Kategorie je systémová (vyžadovaná) a nelze ji tudíž upravovat. </p>
	
	<p><a href="admin.php?page=category">Zpět na výpis</a></p>
</div>

<?php 
	} else {
?>

<h2>Úprava kategorie '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=category&amp;action=update&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post">

<?php include_once $ROOT."/pages/category/i-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Upravit" type="submit">
	<a href="admin.php?page=category" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>