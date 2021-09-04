<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>
<script>
    function getGeocode() {
        var latitude = document.getElementById("latitude").value;
        var longitude = document.getElementById("longitude").value;

        if (latitude && longitude) {
            var latlng = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'latLng': latlng},
                function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var address = results[0].formatted_address;
                        document.getElementById("adresa").value = address.split(",")[0];
                    } else {
                        alert("Získání adresy se nezdařilo (" + status + ").");
                    }
                });
        }
    }

    function showGM() {
        var adresa = document.getElementById("adresa").value;
        if (adresa && adresa.length > 3) {
            window.open("https://www.google.cz/maps?q=" + encodeURIComponent(adresa + ", Plzeň"),"_blank");
        }
    }

</script>

<?php
    if (isset($row)) {
        echo $controller->getGoogleMapPointEditContent($row->latitude, $row->longitude);
    } else {
        echo $controller->getGoogleMapPointEditContent(null, null);
    }
?>

<table class="form-table">
<tbody>
<tr>
    <th scope="row"><label for="nazev">Název</label></th>
    <td><input name="nazev" id="nazev" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->nazev ?>" maxlength="250" /></td>
</tr>
<tr>
    <th scope="row" valign="top"><label for="popis">Popis</label></th>
    <td>
        <textarea id="popis" name="popis" rows="4" cols="40"><?php if (isset($row)) echo $row->popis ?></textarea>
        <p class="description">Krátký popis díla, který se zobrazuje u bodu v mapě spolu s fotkou či jako úvodní odstavec na detailu díla.</p>
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
    <td><input name="latitude" id="latitude" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->latitude ?>" maxlength="20" /></td>
</tr>
<tr id="long">
    <th scope="row"><label for="longitude">Longitude</label></th>
    <td><input name="longitude" id="longitude" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->longitude ?>" maxlength="20" /></td>
</tr>

<tr>
    <th scope="row"><label for="mestska_cast">Městská část</label></th>
    <td>
        <input name="mestska_cast" id="mestska_cast" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->mestska_cast ?>" maxlength="250" />
        <p class="description">Městská část, kde se dílo nachází (např. Plzeň 3).</p>
    </td>
</tr>
<tr>
    <th scope="row"><label for="oblast">Čtvrť (oblast)</label></th>
    <td>
        <input name="oblast" id="oblast" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->oblast ?>" maxlength="250" />
        <p class="description">Část městského obvodu, kde se dílo nachází (např. Skvrňany, Bory apod.)</p>
    </td>
</tr>
<tr>
    <th scope="row"><label for="adresa">Adresa</label></th>
    <td>
        <input name="adresa" id="adresa" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->adresa ?>" maxlength="250" />
        <a href="#" onclick="getGeocode()" title="Navrhne adresu na základě GPS souřadnic" class="button">Navrhnout</a>
        <a href="#" onclick="showGM()" title="Zobrazí adresu v mapě" class="button">Zobrazit v mapě</a>
        <p class="description">Konkrétní adresa místa, kde se dílo nachází.</p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="kategorie">Kategorie</label></th>
    <td>
        <select name="kategorie">
            <option value="0">(nezvoleno)</option>
            <?php foreach ($controller->getAllCategories() as $category) { ?>
                    <option value="<?php echo $category->id ?>"
                            <?php if (isset($row) && ($row->kategorie == $category->id)) { echo 'selected="selected"'; } ?>>
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
        <textarea id="editor" name="editor" rows="30" cols="50"><?php if (isset($row)) echo stripslashes($row->obsah) ?></textarea>
    </td>
</tr>

<tr>
    <th scope="row" valign="top"><label for="interni">Interní poznámka</label></th>
    <td>
        <textarea id="interni" name="interni" rows="7" cols="60"><?php if (isset($row)) echo stripslashes($row->interni) ?></textarea>
    </td>
</tr>

<tr>
    <th scope="row"><label for="zpracovano">Zpracováno</label></th>
    <td>
        <input name="zpracovano" id="zpracovano" type="checkbox" <?php if ((isset($row)) && ($row->zpracovano == 1 || $row->zpracovano)) echo 'checked="checked"' ?>/>
        <p class="description">Zaškrtněte, pokud je text popisující objekt hotov do podoby, která je určena ke zveřejnění.</p>
    </td>
</tr>

<?php if (!$controller->getIsEdit()) { ?>
    <tr>
        <th scope="row" valign="top">Fotky</th>
        <td>
            <input type="file" id="photo" name="photo[]" multiple="multiple" />
            <?php if (!isset($publicForm) || !$publicForm) { ?>
                <p class="description">Tip: Pomocí klávesy CTRL můžete vybrat pro nahrání více fotografií.</p>
            <?php } else { ?>
                <p class="description">Nahrávané fotografie zpřístupňujete pod licencí <a href="https://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0</a>.</p>
            <?php } ?>
        </td>
    </tr>
<?php } ?>
<tr>
    <th scope="row"><label for="rok_realizace">Rok realizace</label></th>
    <td>
        <input name="rok_realizace" id="rok_realizace" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->rok_realizace ?>" maxlength="250" />
        <p class="description">Rok či roky, kdy bylo dílo realizováno ("vyráběno").</p>
    </td>
</tr>
<tr>
    <th scope="row"><label for="rok_vzniku">Rok odhalení</label></th>
    <td>
        <input name="rok_vzniku" id="rok_vzniku" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->rok_vzniku ?>" maxlength="250" />
        <p class="description">Rok, kdy bylo dílo osazeno/odhaleno.</p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="rok_zaniku">Rok zániku</label></th>
    <td>
        <input name="rok_zaniku" id="rok_zaniku" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->rok_zaniku ?>" maxlength="250" />
        <p class="description">Rok, kdy dílo zaniklo.</p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="prezdivka">Přezdívka</label></th>
    <td><input name="prezdivka" id="prezdivka" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->prezdivka ?>" maxlength="250" /></td>
</tr>
<tr>
    <th scope="row"><label for="material">Materiál</label></th>
    <td><input name="material" id="material" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->material ?>" maxlength="250" /></td>
</tr>
<tr>
    <th scope="row"><label for="pristupnost">Přístupnost</label></th>
    <td>
        <input name="pristupnost" id="pristupnost" class="regular-text" type="text" value="<?php if (isset($row)) echo $row->pristupnost ?>" maxlength="250" />
        <p class="description">Veřejné, neveřejné, částečně veřejné</p>
    </td>
</tr>
<tr>
    <th scope="row"><label for="zruseno">Objekt již neexistuje</label></th>
    <td>
        <input name="zruseno" id="zruseno" type="checkbox"
            <?php if (isset($row) && ($row->zruseno == 1 || $row->zruseno)) echo 'checked="checked"' ?>/>
        <p class="description">Zaškrtněte, pokud již objekt neexistuje (odstraněn, zcizen).</p>
    </td>
</tr>
</tbody>
</table>

<h3>Evidence</h3>

<table class="form-table">
<tbody>

<tr>
    <th scope="row"><label for="pridano_osm">Přidáno na OSM</label></th>
    <td>
        <input name="pridano_osm" id="pridano_osm" type="checkbox"
            <?php if (isset($row) && ($row->pridano_osm == 1 || $row->pridano_osm)) echo 'checked="checked"' ?>/>
        <p class="description">Zda bylo dílo přidáno na OpenStreetMap či se tam již nachází.</p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="pridano_vv">Přidáno na VV</label></th>
    <td>
        <input name="pridano_vv" id="pridano_vv" type="checkbox"
            <?php if ((isset($row) && ($row->pridano_vv == 1 || $row->pridano_vv))) echo 'checked="checked"' ?>/>
        <p class="description">Zda bylo dílo přidáno na web Vetřelci a volavky či se tam již nachází.</p>
    </td>
</tr>

</tbody>
</table>

<?php if ($controller->getIsEdit()) { ?>
	<script type="text/javascript">
		// U editace implicitně skrýváme změnu souřadnic a umístění
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
