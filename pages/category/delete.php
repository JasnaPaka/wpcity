<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/CategoryController.php";
	$controller = new CategoryController();
	
	if (isset($_POST["submit"])) {
		$row = $controller->delete();
	} else {
		$row = $controller->getObjectFromUrl();
	}
?>

<div class="wrap">

<?php 
	if ($row == null && isset($_POST["submit"])) {
?>

<div class="error below-h2">
	<p>Kategorie byla úspěšně smazána.</p>
	
	<p><a href="admin.php?page=category">Zpět na výpis</a></p>
</div>

<?php 
	} else if ($row == null) {
?>

<div class="error below-h2">
	<p>Kategorie nebyla nalezena. </p>
	
	<p><a href="admin.php?page=category">Zpět na výpis</a></p>
</div>

<?php 
	} else if (!$controller->getCanDelete()) {
?>

<div class="error below-h2">
	<p>Kategorie nelze smazat. Pravděpodobně není prázdná.</p>
	
	<p><a href="admin.php?page=category">Zpět na výpis</a></p>
</div>

<?php 
	} else {
?>

<h2>Smazání kategorie '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=category&amp;action=delete" method="post">

<p>Chcete skutečně smazat kategorii <strong><?php echo $row->nazev ?></strong>?</p>


<input type="hidden" name="id" value="<?php echo $controller->getObjectId() ?>" />

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Smazat" type="submit">
	<a href="admin.php?page=category" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>