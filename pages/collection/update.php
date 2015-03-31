<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";
	$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

	include_once $ROOT."controllers/CollectionController.php";
	$controller = new CollectionController();
	
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
	<p>Soubor děl nebyl nalezen. </p>
	
	<p><a href="admin.php?page=collection">Zpět na výpis</a></p>
</div>

<?php 
	} else {
?>

<h2>Úprava souboru děl '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=collection&amp;action=update&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post">

<?php include_once $ROOT."/pages/collection/i-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Upravit" type="submit">
	<a href="admin.php?page=collection&amp;action=view&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" class="button">Zpět na detail</a>
</p>

</form>

<?php 
	}
?>

</div>