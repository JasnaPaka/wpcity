<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="jmeno">Jméno</label></th>
	<td><input name="jmeno" id="jmeno" class="regular-text" type="text" value="<?php echo $row->jmeno ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="datum_narozeni">Datum narození</label></th>
	<td><input name="datum_narozeni" id="datum_narozeni" class="regular-text" type="text" value="<?php echo $row->datum_narozeni ?>" maxlength="50" /></td>
</tr>
<tr>
	<th scope="row"><label for="datum_umrti">Datum úmrtí</label></th>
	<td><input name="datum_umrti" id="datum_umrti" class="regular-text" type="text" value="<?php echo $row->datum_umrti ?>" maxlength="50" /></td>
</tr>
</tbody>
</table>


<script>
	$(document).ready(function() {
		$("#datum_narozeni").datepicker();
		$("#datum_umrti").datepicker();
	});
</script>