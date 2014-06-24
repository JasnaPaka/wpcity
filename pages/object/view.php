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
</tbody>
</table>

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

<p class="submit">
	<a href="admin.php?page=object" class="button button-primary">Zpět na výpis</a>
</p>


</div>