<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/AuthorController.php";
	$controller = new AuthorController();
	
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
	<p>Autor byl úspěšně smazán.</p>
	
	<p><a href="admin.php?page=author">Zpět na výpis</a></p>
</div>

<?php 
	} else if ($row == null) {
?>

<div class="error below-h2">
	<p>Autor nebyl nalezena. </p>
	
	<p><a href="admin.php?page=author">Zpět na výpis</a></p>
</div>

<?php 
	} else if (!$controller->getCanDelete()) {
?>

<div class="error below-h2">
	<p>Autora nelze smazat. Pravděpodobně je navázán na některý objekt.</p>
	
	<p><a href="admin.php?page=author">Zpět na výpis</a></p>
</div>

<?php 
	} else {
?>

<h2>Smazání autora '<?php echo $row->jmeno ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=author&amp;action=delete" method="post">

<p>Chcete skutečně smazat autora <strong><?php echo $row->nazev ?></strong>?</p>


<input type="hidden" name="id" value="<?php echo $controller->getObjectId() ?>" />

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Smazat" type="submit">
	<a href="admin.php?page=author" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>