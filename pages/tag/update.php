<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/TagController.php";
	$controller = new TagController();
	
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
	<p>Štítek nebyl nalezen.</p>
	
	<p><a href="admin.php?page=tag">Zpět na výpis</a></p>
</div>

<?php 
	} else {
?>

<h2>Úprava štítku '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=tag&amp;action=update&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post">

<?php include_once $ROOT."/pages/tag/i-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Upravit" type="submit">
	<a href="admin.php?page=tag" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>