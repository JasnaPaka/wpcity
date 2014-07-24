<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";
	$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
	$publicForm = 1;
	
	if (isset($_POST["submit"])) {
		$row = $controller->addPublic();
	}
?>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="" method="post">

<?php
	include __DIR__."/i-shared-form.php";
?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit">
	<a href="../" class="button">Zpět na mapu</a>
</p>

</form>