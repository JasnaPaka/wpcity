<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>

<table class="form-table" enctype="multipart/form-data" method="post">
<tbody>
<tr>
	<th scope="row"><label for="nazev">Název</label></th>
	<td><input name="nazev" id="nazev" class="regular-text" type="text" value="<?php echo $row->nazev ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="latitude">Latitude</label></th>
	<td><input name="latitude" id="latitude" class="regular-text" type="text" value="<?php echo $row->latitude ?>" maxlength="20" /></td>
</tr>
<tr>
	<th scope="row"><label for="longitude">Longitude</label></th>
	<td><input name="longitude" id="longitude" class="regular-text" type="text" value="<?php echo $row->longitude ?>" maxlength="20" /></td>
</tr>
<tr>
	<th scope="row"><label for="kategorie">Kategorie</label></th>
	<td>
		<select name="kategorie">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllCategories() as $category) { ?>
				<option value="<?php echo $category->id ?>"
					<?php if ($row->kategorie == $category->id) { echo 'selected="selected"'; } ?>>
					<?php echo $category->nazev ?>
				</option>
			<?php } ?>
		</select>
	</td>
</tr>
<tr>
	<th scope="row"><label for="obsah">Text</label></th>
	<td>
		<textarea id="editor" name="editor" rows="30" cols="50"><?php echo $row->obsah ?></textarea>
	</td>
</tr>

<?php if (!$controller->getIsEdit()) { ?>
	<tr>
		<th scope="row">Fotky</th>
		<td>
			<input type="file" id="photo1" name="photo1" /><br />
			<input type="file" id="photo2" name="photo2" /><br />
			<input type="file" id="photo3" name="photo3" />
		</td>
	</tr>
<?php } ?>
</tbody>
</table>

<script>
	CKEDITOR.replace('editor');
</script>
