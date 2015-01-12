<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>

<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="jmeno">Jméno</label></th>
	<td><input name="jmeno" id="jmeno" class="regular-text" type="text" value="<?php echo $row->jmeno ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="prijmeni">Příjmení</label></th>
	<td><input name="prijmeni" id="prijmeni" class="regular-text" type="text" value="<?php echo $row->prijmeni ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="titul_pred">Titul před</label></th>
	<td><input name="titul_pred" id="titul_pred" class="regular-text" type="text" value="<?php echo $row->titul_pred ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="titul_za">Titul za</label></th>
	<td><input name="titul_za" id="titul_za" class="regular-text" type="text" value="<?php echo $row->titul_za ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="datum_narozeni">Datum narození</label></th>
	<td><input name="datum_narozeni" id="datum_narozeni" class="regular-text" type="text" value="<?php echo $row->datum_narozeni ?>" maxlength="50" /></td>
</tr>
<tr>
	<th scope="row"><label for="datum_umrti">Datum úmrtí</label></th>
	<td><input name="datum_umrti" id="datum_umrti" class="regular-text" type="text" value="<?php echo $row->datum_umrti ?>" maxlength="50" /></td>
</tr>
<tr>
	<th scope="row"><label for="obsah">Text</label></th>
	<td>
		<textarea id="editor" name="editor" rows="30" cols="50"><?php echo stripslashes($row->obsah) ?></textarea>
	</td>
</tr>
</tbody>
</table>


<script>
	$(document).ready(function() {
		$("#datum_narozeni").datepicker();
		$("#datum_umrti").datepicker();
	});
	
	CKEDITOR.config.entities = false;
	CKEDITOR.config.entities_latin = false;
	CKEDITOR.replace('editor');	
</script>