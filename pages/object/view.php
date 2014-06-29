<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
	
	$row = $controller->getObjectFromUrl();
	$photos = $controller->getPhotosForObject();
?>


<div class="wrap">

<h2><?php echo $row->nazev ?></h2>

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
		<td><?php echo $row->pamatkova_ochrana ?></td>
	</tr>
<?php } ?>
<?php if (strlen($row->pristupnost) > 0) { ?>
	<tr>
		<th><strong>Přístupnost</strong></th>
		<td><?php echo $row->pristupnost ?></td>
	</tr>
<?php } ?>
	
	
</tbody>
</table>

<?php if (strlen($row->obsah) > 0) { ?>

<h3>Obsah</h3>

<?php echo $row->obsah ?>

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

<p class="submit">
	<a href="admin.php?page=object&amp;action=update&amp;id=<?php echo $row->id ?>" class="button button-primary">Upravit</a>
	<a href="admin.php?page=object&amp;action=photo&amp;id=<?php echo $row->id ?>" class="button">Přidat fotografie</a>
	<a href="admin.php?page=object" class="button">Zpět na výpis</a>
</p>


</div>