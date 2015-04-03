<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
	
	$row = $controller->getObjectFromUrl();
	
	if (isset($_POST["submit"])) {
		$controller->manageCollections();
	}
	
	$selectedCollections = $controller->getSelectedCollections();
?>

<div class="wrap">

<h2>Soubory děl pro '<?php echo $row->nazev ?>'</h2>

<p>Zařazení díla do souborů děl.</p>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=object&amp;action=collection&amp;id=<?php printf ($controller->getObjectFromUrl()->id) ?>" method="post" enctype="multipart/form-data">

<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="collection1">Soubor děl č. 1</label></th>
	<td>	
		<select name="collection1">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllCollections() as $collection) { ?>
				<option value="<?php echo $collection->id ?>"
					<?php if ($selectedCollections[0] == $collection->id) { printf('selected="selected"'); } ?>>
					<?php printf($collection->nazev) ?>
				</option>
			<?php } ?>
		</select>	
	</td>
</tr>
<tr>
	<th scope="row"><label for="collection2">Soubor děl č. 2</label></th>
	<td>	
		<select name="collection2">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllCollections() as $collection) { ?>
				<option value="<?php echo $collection->id ?>"
					<?php if ($selectedCollections[1] == $collection->id) { printf('selected="selected"'); } ?>>
					<?php printf($collection->nazev) ?>
				</option>
			<?php } ?>
		</select>	
	</td>
</tr>
<tr>
	<th scope="row"><label for="collection3">Soubor děl č. 3</label></th>
	<td>	
		<select name="collection3">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllCollections() as $author) { ?>
				<option value="<?php echo $collection->id ?>"
					<?php if ($selectedCollections[2] == $collection->id) { printf('selected="selected"'); } ?>>
					<?php printf($collection->nazev) ?>
				</option>
			<?php } ?>
		</select>	
	</td>
</tr>
</tbody>
</table>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Aktualizovat" type="submit">
	<a href="admin.php?page=object&amp;action=view&amp;id=<?php printf($controller->getObjectFromUrl()->id) ?>" class="button">Zpět na detail</a>
</p>

</form>

</div>