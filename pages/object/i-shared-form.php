<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>

<table class="form-table">
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
<tr>
	<th scope="row"><label for="rok_vzniku">Rok vzniku</label></th>
	<td><input name="rok_vzniku" id="rok_vzniku" class="regular-text" type="text" value="<?php echo $row->rok_vzniku ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="prezdivka">Přezdívka</label></th>
	<td><input name="prezdivka" id="prezdivka" class="regular-text" type="text" value="<?php echo $row->prezdivka ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="material">Materiál</label></th>
	<td><input name="material" id="material" class="regular-text" type="text" value="<?php echo $row->material ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="pamatkova_ochrana">Památková ochrana</label></th>
	<td><input name="pamatkova_ochrana" id="pamatkova_ochrana" class="regular-text" type="text" value="<?php echo $row->pamatkova_ochrana ?>" maxlength="50" /></td>
</tr>
<tr>
	<th scope="row"><label for="pristupnost">Přístupnost</label></th>
	<td><input name="pristupnost" id="pristupnost" class="regular-text" type="text" value="<?php echo $row->pristupnost ?>" maxlength="250" /></td>
</tr>
</tbody>
</table>

<script>
	CKEDITOR.replace('editor');
</script>
