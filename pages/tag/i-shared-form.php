<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="nazev">Název</label></th>
	<td><input name="nazev" id="nazev" class="regular-text" type="text" value="<?php echo $row->nazev ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row" valign="top"><label for="popis">Popis</label></th>
	<td>
		<textarea id="popis" name="popis" rows="4" cols="40"><?php echo $row->popis ?></textarea>
		<p class="description">Krátký popis štítku.</p>
	</td>
</tr>
<tr>
	<th scope="row"><label for="skupina">Skupina štítků</label></th>
	<td>
		<select name="skupina">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllTagGroups() as $tagGroup) { ?>
				<option value="<?php print($tagGroup->id) ?>"
					<?php if ($row->skupina == $tagGroup->id) { print('selected="selected"'); } ?>>
					<?php print($tagGroup->nazev) ?>
				</option>
			<?php } ?>
		</select>
	</td>
</tr>
</tbody>
</table>
