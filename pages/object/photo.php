<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";
	$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
		
	if (isset($_POST["submit"])) {
		$row = $controller->uploadPhotos();
	}
?>

<div class="wrap">

<h2>Přidání fotografií</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=object&amp;action=photo&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post" enctype="multipart/form-data">

<table class="form-table">
<tbody>
<tr>
	<th scope="row">Fotky</th>
	<td>
		<input type="file" id="photo1" name="photo1" /><br />
		<input type="file" id="photo2" name="photo2" /><br />
		<input type="file" id="photo3" name="photo3" />
	</td>
</tr>
</tbody>
</table>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit">
	<a href="admin.php?page=object&amp;action=view&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" class="button">Zpět na detail</a>
</p>

</form>

</div>