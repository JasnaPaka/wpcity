<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";
    $ROOT_URL = plugin_dir_url ( __FILE__ )."../../";


include_once $ROOT."controllers/AuthorController.php";
	$controller = new AuthorController();
	
	$row = $controller->getObjectFromUrl();
	
	if (isset($_POST["submit"])) {
		$selectedSources = $controller->manageSources();
	} else {
		$selectedSources = $controller->getSelectedSources();
		$selectedSystemSources = $controller->getSystemSourcesForAuthor();

	}
?>

<div class="wrap">

<script type="application/javascript" src="<?php print $ROOT_URL ?>content/js/objectSource.js">
</script>

<h2>Zdroje pro '<?php echo $controller->getFullname() ?>'</h2>

<p>Přehled souvisejících odkazů a zdrojů pro zvoleného autora.</p>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=author&amp;action=source&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post" enctype="multipart/form-data">

<?php	
	include_once $ROOT."pages/object/i-shared-source.php"; 
?>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Aktualizovat" type="submit">
	<a href="admin.php?page=author&amp;action=view&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" class="button">Zpět na detail</a>
</p>

</form>

</div>