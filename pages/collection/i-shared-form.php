<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>

<?php echo $controller->getGoogleMapPointEditContent($row->latitude, $row->longitude); ?>

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
		<p class="description">Krátký popis souboru děl, který se zobrazuje u bodu v mapě spolu s fotkou.</p>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div id="map-canvas"></div>
		<?php if ($controller->getIsEdit()) { ?>
			<div id="show-map"><a href="#" onclick="zobrazitZmenuSouradnic()">Změnit umístění objektu</a></div>
		<?php } ?>
	</td>
</tr>
<tr id="lat">
	<th scope="row"><label for="latitude">Latitude</label></th>
	<td><input name="latitude" id="latitude" class="regular-text" type="text" value="<?php echo $row->latitude ?>" maxlength="20" /></td>
</tr>
<tr id="long">
	<th scope="row"><label for="longitude">Longitude</label></th>
	<td><input name="longitude" id="longitude" class="regular-text" type="text" value="<?php echo $row->longitude ?>" maxlength="20" /></td>
</tr>

<tr>
	<th scope="row" valign="top"><label for="obsah">Text</label></th>
	<td>
		<textarea id="editor" name="editor" rows="30" cols="50"><?php echo stripslashes($row->obsah) ?></textarea>
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
		<input name="zpracovano" id="zpracovano" type="checkbox" <?php if ($row->zpracovano == 1 || $row->zpracovano) echo 'checked="checked"' ?>/>
		<p class="description">Zaškrtněte, pokud je text popisující objekt hotov do podoby, která je určena ke zveřejnění.</p>
	</td>
</tr>

<tr>
	<th scope="row"><label for="zruseno">Objekt již neexistuje</label></th>
	<td>
		<input name="zruseno" id="zruseno" type="checkbox" <?php if ($row->zruseno == 1 || $row->zruseno) echo 'checked="checked"' ?>/>
		<p class="description">Zaškrtněte, pokud již objekt neexistuje (odstraněn, zcizen).</p>
	</td>
</tr>
</tbody>
</table>

<?php if ($controller->getIsEdit()) { ?>
	<script type="text/javascript">
		// U editace implicitně skrýváme změnu souřadnic
		$('#map-canvas').hide();
		$('#lat').hide();
		$('#long').hide();
	</script>
<?php } ?>

<script type="text/javascript">
	// Akce na zobrazení prvků (mapy, inputů apod.) pro změnu souřadnic.
	function zobrazitZmenuSouradnic() {
		$('#map-canvas').show();
		$('#show-map').hide();
		$('#lat').show();
		$('#long').show();
		
		google.maps.event.trigger(map, 'resize');
		map.panTo(marker.position);
	}

	CKEDITOR.config.entities = false;
	CKEDITOR.config.entities_latin = false;
	CKEDITOR.replace('editor');
</script>
