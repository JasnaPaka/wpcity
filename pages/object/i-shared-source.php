<table class="form-table">
<thead>
	<tr>
		<th>Název (citace zdroje)</th>
		<th>URL</th>
		<th>ISBN</th>
		<th>Čerpáno ze zdroje?</th>
		<th>Smazat?</th>
	</tr>
</thead>
<tbody>
<?php
	$i = 0;
 
	foreach ($selectedSources as $source) { 
		$i--;
		
		if (isset($source->id)) {
			$id = $source->id;	
		} else {
			$id = $i;	
		}
?>
	<tr>
		<td><input type="text" id="nazev<?php echo $id ?>" name="nazev<?php echo $id ?>" value="<?php echo $source->nazev ?>" maxlength="255" size="30" /></td>
		<td><input type="text" id="url<?php echo $id ?>" name="url<?php echo $id ?>" value="<?php echo $source->url ?>" maxlength="255" size="30" /></td>
		<td><input type="text" id="isbn<?php echo $id ?>" name="isbn<?php echo $id ?>" value="<?php echo $source->isbn ?>" maxlength="255" /></td>
		<td><input name="cerpano<?php echo $id ?>" id="cerpano<?php echo $id ?>" type="checkbox" <?php if ($source->cerpano == 1 || $source->cerpano) echo 'checked="checked"' ?>/></td>
		<td>
			<input name="deleted<?php echo $id ?>" id="deleted<?php echo $id ?>" type="checkbox" <?php if ($source->deleted == 1 || $source->deleted) echo 'checked="checked"' ?>/>
			<input type="hidden" id="zdroj<?php echo $id ?>" name="zdroj<?php echo $id ?>" value="<?php echo $id ?>" />
		</td>
	</tr>
<?php } ?>
</tbody>
</table>

