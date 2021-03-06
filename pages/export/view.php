<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/ExportController.php";
	$controller = new ExportController();

	$categories = $controller->getCategories();
?>
<div class="wrap">

<h2>Export</h2>

    <p>Exporty ve formátu CSV lze snadno vizualizovat pomocí <a href="http://www.gpsvisualizer.com/map_input?form=google">GPSVisualiser</a>.
    </p>

<dt>
	<dl>
        <a href="admin.php?page=export&amp;action=nophotos">Export CSV s objekty bez fotek</a>
        (<a href="admin.php?page=export&amp;action=nophotosPublic">pouze veřejné</a>)
    </dl>
    <dd>Provede export CSV s objekty bez fotografií.</dd>

    <dl>
        <a href="admin.php?page=export&amp;action=newPhotoRequired">Export CSV s díly focenými na výšku</a>
    </dl>
    <dd>Provede export CSV s objekty s fotografiemi, které jsou na výšku a je potřeba je přefotit.</dd>

    <dl>
        <a href="admin.php?page=export&amp;action=newPhotoRequired2">Export CSV s díly na přefocení</a>
    </dl>
    <dd>Provede export CSV s objekty s fotografiemi, které nemají odpovídající kvalitu a je potřeba je přefotit.</dd>

    <dl>
        <a href="admin.php?page=export&amp;action=importadres">Doplnění městských obvodů</a>
    </dl>
    <dd>Provede doplnění informace o městském obvodu a části obce u děl, kde tento údaj chybí.</dd>

    <dl>
        <a href="admin.php?page=export&amp;action=exportAuthorsWikidata">Export autorů pro Wikidata</a>
    </dl>
    <dd>Provede export autorů děl, kteří mají 3 a více děl v databázi a nemáme nastaveno provázání na Wikidata.</dd>

    <dl>
        <a href="admin.php?page=export&amp;action=exportDilaCentrum">Export děl pro UKR</a>
    </dl>
    <dd>Jednorázový export děl ve veřejném prostoru v centrum pro UKR.</dd>

    </dt>



<h3>Export kategorií do CSV</h3>

<?php
	if (sizeof($categories) == 0) {
?>

<p>Dosud nebyla přidána žádná kategorie.</p>

<?php
	} else {
?>

<p>Provede export CSV s objekty kategorie, aby pomocí výstupu šla snadno vygenerovat
mapa pomocí <a href="http://www.gpsvisualizer.com/map_input?form=google">GPSVisualiser</a></p>

<table class="wp-list-table widefat fixed posts" style="max-width: 650px;">
	<tbody>
		<?php
			$i = 0;
			foreach ($categories as $category) {
				printf("<tr class=\"%s\">", $i % 2 == 0 ? "alternate" : "");
				printf("<td>%s</td>", $category->nazev);
				printf('<td><a href="admin.php?page=export&amp;action=category&amp;id=%d">Bez zaniklých</a> &middot;
					<a href="admin.php?page=export&amp;action=categoryWithCanceled&amp;id=%d">Včetně zaniklých</a></td>', $category->id, $category->id);
				printf('<td><a href="admin.php?page=export&amp;action=categoryNoAuthors&amp;id=%d">Bez vyplněného autora</a></td>', $category->id);
				printf("<tr>");

				$i++;
			}
		?>
	</tbody>
</table>

<?php
	}
?>

</div>
