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
		<p class="description">Krátký popis díla, který se zobrazuje u bodu v mapě spolu s fotkou.</p>
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
	<th scope="row"><label for="kategorie">Kategorie</label></th>
	<td>
		<select name="kategorie">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllCategories() as $category) { ?>
				<option value="<?php echo $category->id ?>"
					<?php if ($row->kategorie == $category->id) { echo 'selected="selected"'; } ?>>
					<?php echo $category->nazev ?>
				</option>
			<?php } ?>
		</select>
	</td>
</tr>

<?php if (count($controller->getAllTags()) > 0) {  ?> 
<tr>
	<th scope="row"><label for="stitky">Štítky</label></th>
	<td>
		<?php foreach ($controller->getAllTags() as $tag) { ?>
			<input name="tag<?php printf ($tag->id) ?>" id="tag<?php printf ($tag->id) ?>" type="checkbox" <?php if ($controller->getIsTagSelected($tag->id)) echo 'checked="checked"' ?>/>
			<label for="tag<?php printf ($tag->id) ?>"><?php printf ($tag->nazev) ?></label>&nbsp;&nbsp;&nbsp;
		<?php } ?>
	</td>
</tr>
<?php } ?>

<?php if (!$controller->getIsEdit()) { ?>
<tr>
	<th scope="row"><label for="autor">Autor</label></th>
	<td>
		<select name="autor">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllAuthors() as $author) { ?>
				<option value="<?php echo $author->id ?>">
					<?php echo $author->prijmeni ?> <?php echo $author->jmeno ?>
				</option>
			<?php } ?>
		</select>
	</td>
</tr>
<?php } ?>
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

<?php if (!$controller->getIsEdit()) { ?>
	<tr>
		<th scope="row" valign="top">Fotky</th>
		<td>
			<input type="file" id="photo" name="photo[]" multiple="multiple" />
			<?php if (!$publicForm) { ?>
				<p class="description">Tip: Pomocí klávesy CTRL můžete vybrat pro nahrání více fotografií.</p>
			<?php } else { ?>
				<p class="description">Nahrávané fotografie zpřístupňujete pod licencí <a href="https://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0</a>.</p>
			<?php } ?>
		</td>
	</tr>
<?php } ?>
<tr>
	<th scope="row"><label for="rok_vzniku">Rok vzniku</label></th>
	<td><input name="rok_vzniku" id="rok_vzniku" class="regular-text" type="text" value="<?php echo $row->rok_vzniku ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="prezdivka">Přezdívka</label></th>
	<td><input name="prezdivka" id="prezdivka" class="regular-text" type="text" value="<?php echo $row->prezdivka ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="material">Materiál</label></th>
	<td><input name="material" id="material" class="regular-text" type="text" value="<?php echo $row->material ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="pamatkova_ochrana">Památková ochrana</label></th>
	<td>
		<input name="pamatkova_ochrana" id="pamatkova_ochrana" class="regular-text" type="text" value="<?php echo $row->pamatkova_ochrana ?>" maxlength="50" />
		<p class="description">Identifikace v systému <a href="http://monumnet.npu.cz/monumnet.php">MonumNet</a> (např. 20936/4-225)</p>	
	</td>
</tr>
<tr>
	<th scope="row"><label for="pristupnost">Přístupnost</label></th>
	<td>
		<input name="pristupnost" id="pristupnost" class="regular-text" type="text" value="<?php echo $row->pristupnost ?>" maxlength="250" />
		<p class="description">Veřejné, neveřejné, částečně veřejné</p>
	</td>
</tr>
<tr>
	<th scope="row"><label for="zruseno">Objekt již neexistuje</label></th>
	<td>
		<input name="zruseno" id="zruseno" type="checkbox" <?php if ($row->zruseno == 1 || $row->zruseno) echo 'checked="checked"' ?>/>
		<p class="description">Zaškrtněte, pokud již objekt neexistuje (odstraněn, zcizen).</p>
	</td>
</tr>

<tr>
	<th scope="row"><label for="pridano_osm">Přidáno na OSM</label></th>
	<td>
		<input name="pridano_osm" id="pridano_osm" type="checkbox" <?php if ($row->pridano_osm == 1 || $row->pridano_osm) echo 'checked="checked"' ?>/>
		<p class="description">Zda bylo dílo přidáno na OpenStreetMap či se tam již nachází.</p>
	</td>
</tr>

<tr>
	<th scope="row"><label for="pridano_vv">Přidáno na VV</label></th>
	<td>
		<input name="pridano_vv" id="pridano_vv" type="checkbox" <?php if ($row->pridano_vv == 1 || $row->pridano_vv) echo 'checked="checked"' ?>/>
		<p class="description">Zda bylo dílo přidáno na web Vetřelci a volavky či se tam již nachází.</p>
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
