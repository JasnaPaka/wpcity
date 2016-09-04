<table class="form-table">
	<tbody>
	<tr>
		<th scope="row"><label for="nazev">Název</label></th>
		<td><input name="nazev" id="nazev" class="regular-text" type="text"
				   value="<?php print($row->nazev) ?>" maxlength="250" /></td>
	</tr>
	<tr>
		<th scope="row" valign="top"><label for="popis">Popis</label></th>
		<td>
			<textarea id="popis" name="popis" rows="4" cols="40"><?php print($row->popis) ?></textarea>
			<p class="description">Krátký popis skupiny štítků.</p>
		</td>
	</tr>
	</tbody>
</table>
