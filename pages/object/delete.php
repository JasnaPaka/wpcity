<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
	
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
	<p>Objekt byl úspěšně smazán.</p>
	
	<p><a href="admin.php?page=object">Zpět na výpis</a></p>
</div>

<?php 
	} else if ($row == null) {
?>

<div class="error below-h2">
	<p>Objekt nebyl nalezen. </p>
	
	<p><a href="admin.php?page=object">Zpět na výpis</a></p>
</div>

<?php 
	} else {
?>

<h2>Smazání objektu '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=object&amp;action=delete" method="post">

<p>Chcete skutečně smazat objekt <strong><?php echo $row->nazev ?></strong>?</p>


<input type="hidden" name="id" value="<?php echo $controller->getObjectId() ?>" />

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Smazat" type="submit">
	<a href="admin.php?page=object" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>