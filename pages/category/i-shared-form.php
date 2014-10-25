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
	<th scope="row" valign="top"><label for="popis">Popis</label></th>
	<td>
		<textarea id="popis" name="popis" rows="4" cols="40"><?php echo $row->popis ?></textarea>
		<p class="description">Krátký popis kategorie.</p>
	</td>
</tr>
<tr>
	<th scope="row"><label for="checked">Zaškrtnuto</label></th>
	<td>
		<input name="checked" id="checked" type="checkbox" <?php if (!isset($row->checked) || $row->checked == 1 || $row->checked) echo 'checked="checked"' ?>/>
		<p class="description">Zaškrtněte, pokud mají být objekty této kategorie po zobrazení viditelné v mapě (kategorie zašrktnuta).</p>
	</td>
</tr>
<tr>
	<th scope="row"><label for="poradi">Pořadí</label></th>
	<td>
		<input name="poradi" id="poradi" class="regular-text" type="text" value="<?php echo $row->poradi ?>" maxlength="250" />
		<p class="description">Číslo udává pořadí kategorie. Řazení je od největší hodnoty po nejmenší. Výchozí je 0.</p>
	</td>
</tr>
<tr>
	<th scope="row"><label for="zoom">Zobrazitelné při přiblížení</label></th>
	<td>
		<input name="zoom" id="zoom" class="regular-text" type="text" value="<?php if ($row->zoom > 0) echo $row->zoom ?>" maxlength="250" />
		<p class="description">Číslo přiblížení mapy, při kterém se poprvé ikony z kategorie zobrazí (1 - 18). 1 - nejmenší přiblížení, 18 největší.</p>
	</td>
</tr>
</tbody>
</table>
