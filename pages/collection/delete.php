<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/CollectionController.php";
	$controller = new CollectionController();
	
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

<div class="updated below-h2">
	<p>Soubor děl byl úspěšně smazán.</p>
	
	<p><a href="admin.php?page=collection">Zpět na výpis</a></p>
</div>

<?php 
	} else if ($row == null) {
?>

<div class="error below-h2">
	<p>Soubor děl nebyl nalezen. </p>
	
	<p><a href="admin.php?page=collection">Zpět na výpis</a></p>
</div>

<?php 
	} else if (!$controller->getCanDelete()) {
?>

<div class="error below-h2">
	<p>Soubor děl nelze smazat. Pravděpodobně jsou do něj přiřazeny některé objekty.</p>
	
	<p><a href="admin.php?page=collection">Zpět na výpis</a></p>
</div>

<?php 
	} else {
?>

<h2>Smazání souboru děl '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=collection&amp;action=delete" method="post">

<p>Chcete skutečně smazat soubor děl <strong><?php echo $row->nazev ?></strong>?</p>


<input type="hidden" name="id" value="<?php echo $controller->getObjectId() ?>" />

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Smazat" type="submit">
	<a href="admin.php?page=collection" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>