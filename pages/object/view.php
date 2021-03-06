<?php
$ROOT = plugin_dir_path(__FILE__) . "../../";
$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);

include_once $ROOT . "controllers/ObjectController.php";
$controller = new ObjectController();

// aktualizace umístění objektu dle městské části apod.
if ($action === "location-update") {
	$controller->updateLocation();
}

// atribut přefocení
if ($action === "newphoto") {
	$controller->needPhoto();
}
if ($action === "nonewphoto") {
	$controller->noNeedPhoto();
}

// schválení objektu
if (isset($_POST["approve"])) {
	$row = $controller->approve();
} else {
	$row = $controller->getObjectFromUrl();
}

// pomocné kolekce
$photos = $controller->getPhotosForObject();
?>


<div class="wrap">

	<h2><?php echo $row->nazev ?> &nbsp;&nbsp;<a href="/katalog/dilo/<?php echo $row->id ?>/" class="button">Detail
			díla</a></h2>

	<?php if ($row->schvaleno == 0) { ?>
		<div class="updated below-h2">
			<p>Tento objekt dosud nebyl schválen. Po kontrole jej můžete schválit níže.</p>
		</div>
	<?php } ?>

	<?php include_once $ROOT . "fw/templates/messages.php"; ?>

	<table class="widefat" style="max-width: 500px">
		<tbody>
		<tr>
			<th><strong>Souřadnice</strong></th>
			<td>
				<?php echo '<a href="https://maps.google.cz/maps?q=' . $row->latitude . ',' . $row->longitude . '" target="_blank">' .
					$row->latitude . ', ' . $row->longitude . '</a>'; ?>

				<?php printf ('<a href="http://ikatastr.cz/#ilat=%s&ilon=%s&zoom=19&lat=%s&lon=%s" title="Pohled na iKatastr.cz"><img src="%s" /></a>',
					$row->latitude, $row->longitude, $row->latitude, $row->longitude, $ROOT_URL."/content/images/ikatastr-16.png") ?>

			</td>
		</tr>
		<tr>
			<th><strong>Kategorie</strong></th>
			<td><?php echo $controller->getCategoryNameForObject($row->kategorie) ?></td>
		</tr>


		<?php if (strlen($controller->getTagsForObjectStr($row->id)) > 0) { ?>
			<tr>
				<th><strong>Štítky</strong></th>
				<td><?php echo $controller->getTagsForObjectStr($row->id) ?></td>
			</tr>
		<?php } ?>
		<?php if (strlen($row->rok_realizace) > 0) { ?>
			<tr>
				<th><strong>Rok realizace</strong></th>
				<td><?php echo $row->rok_realizace ?></td>
			</tr>
		<?php } ?>
		<?php if (strlen($row->rok_vzniku) > 0) { ?>
			<tr>
				<th><strong>Rok odhaleni</strong></th>
				<td><?php echo $row->rok_vzniku ?></td>
			</tr>
		<?php } ?>
		<?php if (strlen($row->rok_zaniku) > 0) { ?>
            <tr>
                <th><strong>Rok zániku</strong></th>
                <td><?php echo $row->rok_zaniku ?></td>
            </tr>
		<?php } ?>
		<?php if (strlen($row->prezdivka) > 0) { ?>
			<tr>
				<th><strong>Přezdívka</strong></th>
				<td><?php echo $row->prezdivka ?></td>
			</tr>
		<?php } ?>
		<?php if (strlen($row->material) > 0) { ?>
			<tr>
				<th><strong>Materiál</strong></th>
				<td><?php echo $row->material ?></td>
			</tr>
		<?php } ?>
		<?php if (strlen($row->pamatkova_ochrana) > 0) { ?>
			<tr>
				<th><strong>Památková ochrana</strong></th>
				<td>
					<a href="http://monumnet.npu.cz/pamfond/list.php?CiRejst=<?php echo $row->pamatkova_ochrana ?>"><?php echo $row->pamatkova_ochrana ?></a>
				</td>
			</tr>
		<?php } ?>
		<?php if (strlen($row->pristupnost) > 0) { ?>
			<tr>
				<th><strong>Přístupnost</strong></th>
				<td><?php echo $row->pristupnost ?></td>
			</tr>
		<?php } ?>
		<?php if (count($controller->getAuthorsForObject()) > 0) { ?>
			<tr>
				<th valign="top"><strong>Autoři</strong></th>
				<td>
					<?php foreach ($controller->getAuthorsForObject() as $author) { ?>
						<a href="admin.php?page=author&action=view&id=<?php echo $author->id ?>">
							<?php echo trim($author->titul_pred . " " . $author->jmeno . " " . $author->prijmeni . " " . $author->titul_za) ?></a>
						<?php if (strlen($author->spoluprace) > 2) {
							printf("(%s)", $author->spoluprace);
						} ?>
						<br/>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>


		<?php
		if (count($controller->getCollectionsForObject()) > 0) { ?>
			<tr>
				<th valign="top"><strong>Soubory děl</strong></th>
				<td>
					<?php foreach ($controller->getCollectionsForObject() as $collection) { ?>
						<a href="admin.php?page=collection&action=view&id=<?php echo $collection->id ?>">
							<?php printf($collection->nazev) ?></a>
						<br/>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
        <tr>
            <th><strong>Identifikátor</strong></th>
            <td><?php print $controller->getIdentifier(); ?></td>
        </tr>
		<tr>
			<th><strong>Zpracován text</strong></th>
			<td><?php echo($row->zpracovano ? "Ano" : "Ne") ?></td>
		</tr>

		<tr>
			<th><strong>Umístěno na <abbr title="OpenStreetMap">OSM</abbr></strong></th>
			<td><?php echo($row->pridano_osm ? "Ano" : "Ne") ?></td>
		</tr>

		<tr>
			<th><strong>Umístěno na <abbr title="Vetřelci a volavky">VV</abbr></strong></th>
			<td><?php echo($row->pridano_vv ? "Ano" : "Ne") ?></td>
		</tr>

		</tbody>
	</table>

	<form method="post">
		<p class="submit">
			<a href="admin.php?page=object&amp;action=update&amp;id=<?php echo $row->id ?>"
			   class="button">Upravit</a>
			<?php if ($row->schvaleno == 0) { ?>
				<input name="approve" id="approve" class="button" value="Schválit" type="submit">
			<?php } ?>
			<a href="admin.php?page=object&amp;action=author&amp;id=<?php echo $row->id ?>" class="button">Správa
				autorů</a>
			<a href="admin.php?page=object&amp;action=source&amp;id=<?php echo $row->id ?>" class="button">Správa
				zdrojů</a>
			<a href="admin.php?page=object&amp;action=collection&amp;id=<?php echo $row->id ?>" class="button">Správa
				souborů děl</a>
			<a href="admin.php?page=object&amp;action=poi-list&amp;id=<?php echo $row->id ?>" class="button">Správa
				bodů</a>
			<a href="admin.php?page=object&amp;action=delete&amp;id=<?php echo $row->id ?>" class="button">Smazat</a>
		</p>
	</form>

	<h3>Umístění</h3>

	<?php echo $controller->getGoogleMapPointContent($row->latitude, $row->longitude); ?>

	<?php if (strlen($row->mestska_cast) > 0 || strlen($row->oblast) > 0 || strlen($row->adresa) > 0) { ?>
		<br /><table class="widefat" style="max-width: 500px">
			<tbody>
			<?php if (strlen($row->mestska_cast) > 0) { ?>
				<tr>
					<th><strong>Městská část</strong></th>
					<td><?php echo $row->mestska_cast ?></td>
				</tr>
			<?php } ?>
			<?php if (strlen($row->oblast) > 0) { ?>
				<tr>
					<th><strong>Čtvrť (oblast)</strong></th>
					<td><?php echo $row->oblast ?></td>
				</tr>
			<?php } ?>
			<?php if (strlen($row->adresa) > 0) { ?>
				<tr>
					<th><strong>Adresa</strong></th>
					<td><?php echo $row->adresa ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<form method="post">
			<p class="submit">
				<a href="admin.php?page=object&amp;action=location-update&amp;id=<?php echo $row->id ?>" class="button">Aktualizovat
					městskou část</a>
			</p>
		</form>
	<?php } else { ?>

		<p>U objektu není dosud nastaveno umístění v městské části/oblasti.</p>

		<form method="post">
			<p class="submit">
				<a href="admin.php?page=object&amp;action=location-update&amp;id=<?php echo $row->id ?>" class="button">Nastavit
					městskou část</a>
			</p>
		</form>

	<?php } ?>

	<?php if (strlen($row->popis) > 0) { ?>

		<h3>Popis</h3>

		<?php echo $row->popis ?>

	<?php } ?>

	<?php if (strlen($row->obsah) > 0) { ?>

		<h3>Obsah</h3>

		<div class="content-width">
			<?php echo stripslashes($row->obsah) ?>
		</div>

	<?php } ?>

	<h3>Fotografie</h3>

	<?php if (count($photos) == 0) { ?>

		<p>K objektu nejsou prozatím nahrány žádné fotografie.</p>

		<p class="submit">
			<a href="admin.php?page=object&amp;action=photo&amp;id=<?php echo $row->id ?>" class="button">Přidat
				fotografie</a>
		</p>

	<?php } else {

		foreach ($photos as $photo) {
			$uploadDir = wp_upload_dir();
			?>
			<span class="photo-detail"><a href="<?php echo $uploadDir["baseurl"] ?><?php echo $photo->img_original ?>"
										  title="Pro zvětšení klepněte">
	<img src="<?php echo upload_images_dir() ?><?php echo $photo->img_thumbnail ?>" alt=""/>
</a></span>

		<?php } ?>

		<p class="submit">
			<a href="admin.php?page=object&amp;action=photo&amp;id=<?php echo $row->id ?>" class="button">Přidat či
				upravit fotografie</a>
            <?php
                if ($row->potreba_foto) {
					printf('<a href="admin.php?page=object&amp;action=nonewphoto&amp;id=%d" 
                        class="button">Už není potřeba přefotit</a>', $row->id);
                } else {
					printf('<a href="admin.php?page=object&amp;action=newphoto&amp;id=%d" 
                        class="button">Potřeba přefotit</a>', $row->id);
                }
            ?>
		</p>

	<?php } ?>

	<?php if (strlen($row->interni) > 1) { ?>

		<h3>Interní poznámka</h3>

		<div class="content-width">
			<?php echo stripslashes($row->interni) ?>
		</div>

	<?php } ?>

	<h3>Historie</h3>

	<table class="widefat" style="max-width: 500px; margin-top: 10px">
		<tbody>
		<?php if (strlen($row->pridal_autor) > 0) { ?>
			<tr>
				<th><strong>Vytvořil</strong></th>
				<td><?php echo $row->pridal_autor ?>
					(<?php echo date_format(new DateTime($row->pridal_datum), "d. m. Y") ?>)
				</td>
			</tr>
		<?php } ?>
		<?php if (strlen($row->upravil_autor) > 0) { ?>
			<tr>
				<th><strong>Aktualizace</strong></th>
				<td><?php echo $row->upravil_autor ?>
					(<?php echo date_format(new DateTime($row->upravil_datum), "d. m. Y") ?>)
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<form method="post">
		<p class="submit">
			<a href="admin.php?page=object" class="button">Zpět na výpis</a>
		</p>
	</form>

</div>
