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
</tbody>
</table>
