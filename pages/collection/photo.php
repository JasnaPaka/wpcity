<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";
	$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

	include_once $ROOT."controllers/CollectionController.php";
	$controller = new CollectionController();
		
	if (isset($_POST["submit"])) {
		$photos = $controller->managePhotos();
	} else {
		$photos = $controller->getPhotosForCollection();
	}
	
	$row = $controller->getObjectFromUrl();
?>

<div class="wrap">

<h2>Správa fotografií pro '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=collection&amp;action=photo&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" method="post" enctype="multipart/form-data">

<?php if (count($photos) > 0) { ?>

<h3>Nahrané fotografie</h3>
	
<table>	
<?php foreach ($photos as $photo) { 
	$uploadDir = wp_upload_dir(); ?>
	
	<tr>
		<td valign="top"><span class="photo-detail"><a href="<?php echo $uploadDir["baseurl"] ?>
			<?php echo $photo->img_original ?>" title="Pro zvětšení klepněte">
		<img src="<?php echo upload_images_dir() ?><?php echo $photo->img_thumbnail ?>" alt="" /></a></span></td>
		
		<td>
			<table>
			<tr>
				<td valign="top">
					<label for="autor<?php echo $photo->id ?>">Autor</label>
				</td>
				<td>
					<input name="autor<?php echo $photo->id ?>" id="autor<?php echo $photo->id ?>" 
						class="regular-text" type="text" value="<?php echo $photo->autor ?>" maxlength="250" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					<label for="popis<?php echo $photo->id ?>">Popis</label>
				</td>
				<td>
					<textarea name="popis<?php echo $photo->id ?>" id="popis<?php echo $photo->id ?>" 
						rows="4" cols="40"><?php echo $photo->popis ?></textarea>
				</td>
			</tr>
			
			<tr>
				<td valign="top">
					<label for="rok<?php echo $photo->id ?>">Rok</label>
				</td>
				<td>
					<input name="rok<?php echo $photo->id ?>" id="rok<?php echo $photo->id ?>" class="regular-text" 
						type="text" value="<?php echo $photo->rok ?>" maxlength="250" />
						<a onclick="document.getElementById('rok<?php echo $photo->id ?>').value='<?php echo date("Y");?>'" 
							class="button">Aktuální rok</a>
				</td>
			</tr>

			<tr>
				<td valign="top">
					<label for="url<?php echo $photo->id ?>">URL</label>
				</td>
				<td>
					<input name="url<?php echo $photo->id ?>" id="url<?php echo $photo->id ?>" class="regular-text" 
						type="text" value="<?php echo $photo->url ?>" maxlength="250" />
				</td>
			</tr>
			
			<tr>
				<td valign="top">
					&nbsp;
				</td>
				<td>
                                    <table>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="primarni<?php echo $photo->id ?>" 
                                                            id="primarni<?php echo $photo->id ?>" <?php if ($photo->primarni) { print('checked="checked"'); } ?> 
                                                            onclick="zmenaPrimarni('primarni<?php echo $photo->id ?>')" />
                                                            <label for="primarni<?php echo $photo->id ?>">Hlavní fotografie</label>
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="soukroma<?php echo $photo->id ?>" 
                                                            id="soukroma<?php echo $photo->id ?>" <?php if ($photo->soukroma) { print('checked="checked"'); } ?> />
                                                            <label for="soukroma<?php echo $photo->id ?>">Soukromá fotografie</label>
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="skryta<?php echo $photo->id ?>" 
                                                            id="skryta<?php echo $photo->id ?>" <?php if ($photo->skryta) { print('checked="checked"'); } ?> />
                                                            <label for="skryta<?php echo $photo->id ?>">Skryta na webu</label>
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="delete<?php echo $photo->id ?>" 
                                                            id="delete<?php echo $photo->id ?>"
                                                            onclick="deletePhoto('delete<?php echo $photo->id ?>')" />
                                                            <label for="delete<?php echo $photo->id ?>">Smazat fotografii?</label>
                                                </td>
                                            </tr>
                                    </table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
<?php } ?>
</table>
<?php } ?>

<table class="form-table">
<tbody>
<tr>
	<th scope="row">Nové fotografie</th>
	<td>
            <input type="file" id="photo" name="photo[]" multiple="multiple" /><br />
            <?php if (count($photos) === 0) { ?>
                <p class="description">První vybraná fotografie bude označena jako hlavní a bude se zobrazovat jako výchozí u bodů v mapě.</p>
            <?php } else { ?>
                <p class="description">Tip: Pomocí klávesy CTRL můžete vybrat pro nahrání více fotografií.</p>
            <?php } ?>
	</td>
</tr>
</tbody>
</table>

<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Uložit" type="submit">
	<a href="admin.php?page=collection&amp;action=view&amp;id=<?php echo $controller->getObjectFromUrl()->id ?>" class="button">Zpět na detail</a>
</p>

</form>

<script type="text/javascript">
	function zmenaPrimarni(id) {
		result = $('#' + id).is(':checked');
		
		if (result) {
			$('[name*="primarni"]').each(function() {
				$(this).prop('checked', false);
			});
			$('#' + id).prop('checked', true);
		}
	}
	
	function deletePhoto(id) {
		result = $('#' + id).is(':checked');
		if (result) {
			alert("Fotografie bude po uložení smazána. Pro zrušení této akce zrušte zaškrtnutí.");
		}
	}

</script>

</div>