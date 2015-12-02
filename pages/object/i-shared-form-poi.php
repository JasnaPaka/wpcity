<script src="<?php echo $ROOT_URL ?>content/js/ckeditor4.4.2/ckeditor.js"></script>

<?php print($controller->getGoogleMapPointEditContent($poi->latitude, $poi->longitude)); ?>

<table class="form-table">
<tbody>
<tr>
    <th scope="row"><label for="nazev">Název</label></th>
    <td><input name="nazev" id="nazev" class="regular-text" type="text" value="<?php echo $poi->nazev ?>" maxlength="250" /></td>
</tr>
<?php if (!$publicForm) { ?>
<tr>
    <th scope="row" valign="top"><label for="popis">Popis</label></th>
    <td>
        <textarea id="popis" name="popis" rows="4" cols="40"><?php echo $poi->popis ?></textarea>
        <p class="description">Krátký popis bodu.</p>
    </td>
</tr>
<?php } ?>
<tr>
    <td colspan="2">
        <div id="map-canvas"></div>
        <div id="show-map"></div>
    </td>
</tr>
<tr id="lat">
    <th scope="row"><label for="latitude">Latitude</label></th>
    <td><input name="latitude" id="latitude" class="regular-text" type="text" value="<?php echo $poi->latitude ?>" maxlength="20" /></td>
</tr>
<tr id="long">
    <th scope="row"><label for="longitude">Longitude</label></th>
    <td><input name="longitude" id="longitude" class="regular-text" type="text" value="<?php echo $poi->longitude ?>" maxlength="20" /></td>
</tr>
</tbody>
</table>

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