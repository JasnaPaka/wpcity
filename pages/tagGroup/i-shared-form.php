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
	<tr>
		<th scope="row"><label for="barva">Barva</label></th>
		<td><input name="barva" id="barva" class="regular-text" type="text"
				   value="<?php print($row->barva) ?>" maxlength="250" />
			<p class="description">Barva ve formátu, který akceptuje HTML/CSS
				(<a href="http://www.w3schools.com/cssref/css_colors.asp">ukázky</a>).</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="poradi">Pořadí</label></th>
		<td><input name="poradi" id="poradi" class="regular-text" type="text"
				   value="<?php print($row->poradi) ?>" maxlength="250" />
			<p class="description">Číslo, kterým lze určit pořadí skupin. Není povinné, implicitní řazení je dle názvu.
			Řazení je vzestupné.</p>
		</td>
	</tr>


	</tbody>
</table>
