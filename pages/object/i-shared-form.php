<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAC9G-I3g4tWPbXK-v_Ws_1_dY4V8w6Eew&amp;sensor=false"></script>
<script type="text/javascript">
	var map;
	var marker;
	
	var mapOptions = {
          center: new google.maps.LatLng(49.748398, 13.377652),
          zoom: 13
    };
    
    function placeMarker(location) {
	  if ( marker ) {
	    marker.setPosition(location);
	  } else {
	    marker = new google.maps.Marker({
	      position: location,
	      map: map
	    });
	  }
	}

	function initialize() {
		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
		
		// Skryje výchozí POI
		var styles = [ { featureType: "poi", stylers: [ { visibility: "off" } ] } ];
      	map.setOptions({styles: styles});
      	
      	// Zakáže "ptačí pohled"
      	map.setTilt(0);
		
		google.maps.event.addListener(map, 'click', function(event) {
			$('#latitude').val(event.latLng.lat());
			$('#longitude').val(event.latLng.lng());
			placeMarker(event.latLng);
		});
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);
</script>

<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="nazev">Název</label></th>
	<td><input name="nazev" id="nazev" class="regular-text" type="text" value="<?php echo $row->nazev ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="popis">Popis</label></th>
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
<?php if (!$controller->getIsEdit()) { ?>
<tr>
	<th scope="row"><label for="autor">Autor</label></th>
	<td>
		<select name="autor">
			<option value="0">(nezvoleno)</option>
			<?php foreach ($controller->getAllAuthors() as $author) { ?>
				<option value="<?php echo $author->id ?>">
					<?php echo $author->jmeno ?>
				</option>
			<?php } ?>
		</select>
	</td>
</tr>
<?php } ?>
<tr>
	<th scope="row"><label for="obsah">Text</label></th>
	<td>
		<textarea id="editor" name="editor" rows="30" cols="50"><?php echo $row->obsah ?></textarea>
	</td>
</tr>

<?php if (!$controller->getIsEdit()) { ?>
	<tr>
		<th scope="row">Fotky</th>
		<td>
			<input type="file" id="photo1" name="photo1" /><br />
			<input type="file" id="photo2" name="photo2" /><br />
			<input type="file" id="photo3" name="photo3" />
			<p class="description">První fotografie bude označena jako hlavní a bude se zobrazovat jako výchozí u bodů v mapě.</p>
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
	}

	CKEDITOR.replace('editor');
</script>
