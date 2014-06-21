<?php
	include_once "category-controller.php";
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
	} else {
?>

<h2>Úprava kategorie '<?php echo $row->nazev ?>'</h2>

<?php include_once "messages.php"; ?>

<form action="admin.php?page=category&amp;action=update&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post">

<?php include_once "category-shared-form.php" ?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Upravit" type="submit">
	<a href="admin.php?page=category" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>