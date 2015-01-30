<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
	
	$row = $controller->getObjectFromUrl();
	
	if (isset($_POST["submit"])) {
		$controller->manageAuthors();
	}
	
	$selectedAuthors = $controller->getSelectedAuthors();
	$cooperations = $controller->getCooperations();
?>

<div class="wrap">

<h2>Autoři objektu '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=object&amp;action=author&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post" enctype="multipart/form-data">

<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="autor1">Autor č. 1</label></th>
	<td>	
		<select name="autor1">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllAuthors() as $author) { ?>
				<option value="<?php echo $author->id ?>"
					<?php if ($selectedAuthors[0] == $author->id) { echo 'selected="selected"'; } ?>>
					<?php echo $author->prijmeni ?> <?php echo $author->jmeno ?>
				</option>
			<?php } ?>
		</select>	
	</td>
	<th scope="row"><label for="spoluprace1">Forma spolupráce</label></th>
	<td><input name="spoluprace1" id="spoluprace1" class="regular-text" type="text" value="<?php echo $cooperations[0]  ?>" maxlength="255" /></td>
</tr>
<tr>
	<th scope="row"><label for="autor2">Autor č. 2</label></th>
	<td>	
		<select name="autor2">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllAuthors() as $author) { ?>
				<option value="<?php echo $author->id ?>"
					<?php if ($selectedAuthors[1] == $author->id) { echo 'selected="selected"'; } ?>>
					<?php echo $author->prijmeni ?> <?php echo $author->jmeno ?>
				</option>
			<?php } ?>
		</select>	
	</td>
	<th scope="row"><label for="spoluprace2">Forma spolupráce</label></th>
	<td><input name="spoluprace2" id="spoluprace2" class="regular-text" type="text" value="<?php echo $cooperations[1]  ?>" maxlength="255" /></td>
</tr>
<tr>
	<th scope="row"><label for="autor3">Autor č. 3</label></th>
	<td>	
		<select name="autor3">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllAuthors() as $author) { ?>
				<option value="<?php echo $author->id ?>"
					<?php if ($selectedAuthors[2] == $author->id) { echo 'selected="selected"'; } ?>>
					<?php echo $author->prijmeni ?> <?php echo $author->jmeno ?>
				</option>
			<?php } ?>
		</select>	
	</td>
	<th scope="row"><label for="spoluprace3">Forma spolupráce</label></th>
	<td><input name="spoluprace3" id="spoluprace3" class="regular-text" type="text" value="<?php echo $cooperations[2]  ?>" maxlength="255" /></td>
</tr>
</tbody>
</table>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Nastavit" type="submit">
	<a href="admin.php?page=object&amp;action=view&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" class="button">Zpět na detail</a>
</p>


</form>

</div>