<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="nazev">Název</label></th>
	<td><input name="nazev" id="nazev" class="regular-text" type="text" value="<?php echo $row->nazev ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="url">URL</label></th>
	<td><input name="url" id="url" class="regular-text" type="text" value="<?php echo $row->url ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="ikona">Ikona</label></th>
	<td><input name="ikona" id="ikona" class="regular-text" type="text" value="<?php echo $row->ikona ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="checked">Zaškrtnuto</label></th>
	<td>
		<input name="checked" id="checked" type="checkbox" <?php if (!isset($row->checked) || $row->checked == 1 || $row->checked) echo 'checked="checked"' ?>/>
		<p class="description">Zaškrtněte, pokud mají být objekty této kategorie po zobrazení viditelné v mapě.</p>
	</td>
</tr>
</tbody>
</table>
