<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>

<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="jmeno">Jméno</label></th>
	<td><input name="jmeno" id="jmeno" class="regular-text" type="text" value="<?php if (isset($row)) print($row->jmeno) ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="prijmeni">Příjmení</label></th>
	<td><input name="prijmeni" id="prijmeni" class="regular-text" type="text" value="<?php if (isset($row)) print ($row->prijmeni) ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="titul_pred">Titul před</label></th>
	<td><input name="titul_pred" id="titul_pred" class="regular-text" type="text" value="<?php if (isset($row)) print($row->titul_pred) ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="titul_za">Titul za</label></th>
	<td><input name="titul_za" id="titul_za" class="regular-text" type="text" value="<?php if (isset($row)) print($row->titul_za) ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="datum_narozeni">Datum narození</label></th>
	<td>
		<input name="datum_narozeni" id="datum_narozeni" class="regular-text" type="text" value="<?php if (isset($row)) print($row->datum_narozeni) ?>" maxlength="50" />
		<p class="description">Datum ve formátu "den. měsíc. rok" (např. 5. 6. 1950).</p>
	</td>
</tr>
<tr>
	<th scope="row"><label for="datum_umrti">Datum úmrtí</label></th>
	<td>
		<input name="datum_umrti" id="datum_umrti" class="regular-text" type="text" value="<?php if (isset($row)) print($row->datum_umrti) ?>" maxlength="50" />
		<p class="description">Datum ve formátu "den. měsíc. rok" (např. 5. 6. 1950).</p>
	</td>
</tr>
<tr>
    <th scope="row"><label for="misto_narozeni">Místo narození</label></th>
    <td><input name="misto_narozeni" id="misto_narozeni" class="regular-text" type="text" value="<?php if (isset($row)) print($row->misto_narozeni) ?>" maxlength="250" /></td>
</tr>
<tr>
    <th scope="row"><label for="misto_umrti">Místo úmrtí</label></th>
    <td><input name="misto_umrti" id="misto_umrti" class="regular-text" type="text" value="<?php if (isset($row)) print ($row->misto_umrti) ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="web">Webová stránka</label></th>
	<td><input name="web" id="web" class="regular-text" type="text" value="<?php if (isset($row)) printf($row->web) ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="obsah">Text</label></th>
	<td>
		<textarea id="editor" name="editor" rows="30" cols="50"><?php if (isset($row)) print(stripslashes($row->obsah)) ?></textarea>
	</td>
</tr>
<tr>
    <th scope="row" valign="top"><label for="interni">Interní poznámka</label></th>
    <td>
        <textarea id="interni" name="interni" rows="7" cols="60"><?php echo stripslashes($row->interni) ?></textarea>
    </td>
</tr>
<tr>
	<th scope="row"><label for="zpracovano">Zpracováno</label></th>
	<td>
		<input name="zpracovano" id="zpracovano" type="checkbox" <?php if ((isset($row)) && ($row->zpracovano == 1 || $row->zpracovano)) print('checked="checked"') ?>/>
		<p class="description">Zaškrtněte, pokud je text popisující objekt hotov do podoby, která je určena ke zveřejnění.</p>
	</td>
</tr>
</tbody>
</table>


<script>
	CKEDITOR.config.entities = false;
	CKEDITOR.config.entities_latin = false;
	CKEDITOR.replace('editor');	
</script>