<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."config.php";
	global $KV_SETTINGS;

	include_once $ROOT."controllers/CollectionController.php";
	$controller = new CollectionController();
	
	$row = $controller->getObjectFromUrl();
?>


<div class="wrap">

<h2>Soubor děl '<?php echo $row->nazev ?>' &nbsp;&nbsp;<a href="/katalog/soubor/<?php echo $row->id ?>/" class="button">Detail souboru děl</a></h2>


<?php include_once $ROOT."fw/templates/messages.php"; ?>

<table class="widefat" style="max-width: 500px">
<tbody>
	<tr>
		<th><strong>Souřadnice</strong></th>
		<td><?php echo '<a href="https://maps.google.cz/maps?q='.$row->latitude.','.$row->longitude.'" target="_blank">'.
						$row->latitude.', '.$row->longitude.'</a>'; ?></td>
	</tr>	
	<tr>
		<th><strong>Zpracován text</strong></th>
		<td><?php echo ($row->zpracovano? "Ano" : "Ne") ?></td>
	</tr>
	
</tbody>
</table>

<br />
<?php echo $controller->getGoogleMapPointContent($row->latitude, $row->longitude); ?>

<?php if (strlen($row->popis) > 0) { ?>

<h3>Popis</h3>

<?php echo $row->popis ?>

<?php } ?>

<?php if (strlen($row->obsah) > 0) { ?>

<h3>Obsah</h3>

<div class="content-width">
<?php echo stripslashes($row->obsah) ?>
</div>

<?php } ?>



<?php if (strlen($row->interni) > 1) { ?>

<h3>Interní poznámka</h3>

<div class="content-width">
<?php echo stripslashes($row->interni) ?>
</div>

<?php } ?>

<?php if (count($controller->getObjectsInCollection($row->id)) > 0) { ?>

<h3>Díla v souboru</h3>

<ul>

<?php 

foreach ($controller->getObjectsInCollection($row->id) as $object) {
	printf('<li><a href="admin.php?page=object&amp;action=view&amp;id='.$object->id.'">'.$object->nazev.'</a></li>');
}

?>

</ul>

<?php } ?>


<table class="widefat" style="max-width: 500px; margin-top: 10px">
<tbody>
<?php if (strlen ($row->pridal_autor) > 0) { ?>
	<tr>
		<th><strong>Vytvořil</strong></th>
		<td><?php echo $row->pridal_autor ?> (<?php echo date_format(new DateTime($row->pridal_datum), "d. m. Y") ?>)</td>
	</tr>
<?php } ?>
<?php if (strlen ($row->upravil_autor) > 0) { ?>
	<tr>
		<th><strong>Aktualizace</strong></th>
		<td><?php echo $row->upravil_autor ?> (<?php echo date_format(new DateTime($row->upravil_datum), "d. m. Y") ?>)</td>
	</tr>
<?php } ?>
</tbody>
</table>


<form method="post">
<p class="submit">
	<a href="admin.php?page=collection&amp;action=update&amp;id=<?php echo $row->id ?>" class="button button-primary">Upravit</a>

	<a href="admin.php?page=collection&amp;action=delete&amp;id=<?php echo $row->id ?>" class="button">Smazat</a>
	<a href="admin.php?page=collection" class="button">Zpět na výpis</a>
</p>
</form>

</div>