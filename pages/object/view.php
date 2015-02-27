<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."config.php";
	global $KV_SETTINGS;

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
	
	if (isset($_POST["approve"])) {
		$row = $controller->approve();
	} else {	
		$row = $controller->getObjectFromUrl();
	}
	$photos = $controller->getPhotosForObject();
?>


<div class="wrap">

<h2><?php echo $row->nazev ?> &nbsp;&nbsp;<a href="/katalog/dilo/<?php echo $row->id ?>/" class="button">Detail díla</a></h2>

<?php if ($row->schvaleno == 0) { ?>
	<div class="updated below-h2">
		<p>Tento objekt dosud nebyl schválen. Po kontrole jej můžete schválit níže.</p>
	</div>
<?php } ?>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<table class="widefat" style="max-width: 500px">
<tbody>
	<tr>
		<th><strong>Souřadnice</strong></th>
		<td><?php echo '<a href="https://maps.google.cz/maps?q='.$row->latitude.','.$row->longitude.'" target="_blank">'.
						$row->latitude.', '.$row->longitude.'</a>'; ?></td>
	</tr>
	<tr>
		<th><strong>Kategorie</strong></th>
		<td><?php echo $controller->getCategoryNameForObject($row->kategorie) ?></td>
	</tr>
<?php if (strlen($row->rok_vzniku) > 0) { ?>
	<tr>
		<th><strong>Rok vzniku</strong></th>
		<td><?php echo $row->rok_vzniku ?></td>
	</tr>
<?php } ?>	
<?php if (strlen($row->prezdivka) > 0) { ?>
	<tr>
		<th><strong>Přezdívka</strong></th>
		<td><?php echo $row->prezdivka ?></td>
	</tr>
<?php } ?>
<?php if (strlen($row->material) > 0) { ?>
	<tr>
		<th><strong>Materiál</strong></th>
		<td><?php echo $row->material ?></td>
	</tr>
<?php } ?>
<?php if (strlen($row->pamatkova_ochrana) > 0) { ?>
	<tr>
		<th><strong>Památková ochrana</strong></th>
		<td>
			<a href="http://monumnet.npu.cz/pamfond/list.php?CiRejst=<?php echo $row->pamatkova_ochrana?>"><?php echo $row->pamatkova_ochrana?></a>
		</td>
	</tr>
<?php } ?>
<?php if (strlen($row->pristupnost) > 0) { ?>
	<tr>
		<th><strong>Přístupnost</strong></th>
		<td><?php echo $row->pristupnost ?></td>
	</tr>
<?php } ?>
<?php if (count($controller->getAuthorsForObject()) > 0) { ?>
	<tr>
		<th valign="top"><strong>Autoři</strong></th>
		<td>
			<?php foreach($controller->getAuthorsForObject() as $author) { ?>
				<a href="admin.php?page=author&action=view&id=<?php echo $author->id ?>">
					<?php echo trim($author->titul_pred." ".$author->jmeno." ".$author->prijmeni." ".$author->titul_za) ?></a>
					<br />
			<?php } ?>
		</td>
	</tr>
<?php } ?>
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

<h3>Fotografie</h3>

<?php if (count ($photos) == 0) { ?>

<p>K objektu nejsou prozatím nahrány žádné fotografie.</p>

<?php } else { 

	foreach($photos as $photo) {
		$uploadDir = wp_upload_dir();
?>

<span class="photo-detail"><a href="<?php echo $uploadDir["baseurl"] ?><?php echo $photo->img_original ?>" title="Pro zvětšení klepněte">
	<img src="<?php echo $uploadDir["baseurl"] ?><?php echo $photo->img_thumbnail ?>" alt="" />
</a></span>

<?php 
		}	
	} 
?>

<?php if (strlen($row->interni) > 1) { ?>

<h3>Interní poznámka</h3>

<div class="content-width">
<?php echo stripslashes($row->interni) ?>
</div>

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
	<a href="admin.php?page=object&amp;action=update&amp;id=<?php echo $row->id ?>" class="button button-primary">Upravit</a>
	<?php if ($row->schvaleno == 0) { ?>
		<input name="approve" id="approve" class="button" value="Schválit" type="submit">
	<?php } ?>
	<a href="admin.php?page=object&amp;action=photo&amp;id=<?php echo $row->id ?>" class="button">Správa fotografií</a>
	<?php if (!$KV_SETTINGS["simple"]) { ?>
		<a href="admin.php?page=object&amp;action=author&amp;id=<?php echo $row->id ?>" class="button">Správa autorů</a>
		<a href="admin.php?page=object&amp;action=source&amp;id=<?php echo $row->id ?>" class="button">Správa zdrojů</a>
	<?php } ?>
	<a href="admin.php?page=object&amp;action=delete&amp;id=<?php echo $row->id ?>" class="button">Smazat</a>
	<a href="admin.php?page=object" class="button">Zpět na výpis</a>
</p>
</form>

</div>