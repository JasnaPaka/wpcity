<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/ExportController.php";
	$controller = new ExportController();
	
	$categories = $controller->getCategories();
?>
<div class="wrap">

<h2>Export</h2>

<dt>
	<dl><a href="admin.php?page=export&amp;action=nophotos">Export CSV s objekty bez fotek</a></dl>
	<dd>Provede export CSV s objekty bez fotografií tak, aby pomocí výstupu šla snadno vygenerovat
		mapa pomocí <a href="http://www.gpsvisualizer.com/map_input?form=google">GPSVisualiser</a>.</dd>
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

<table class="wp-list-table widefat fixed posts" style="max-width: 500px;">
	<tbody>
		<?php
			$i = 0;
			foreach ($categories as $category) {
				printf("<tr class=\"%s\">", $i % 2 == 0 ? "alternate" : "");
				printf("<td>%s</td>", $category->nazev);
				printf("<td><a href=\"admin.php?page=export&amp;action=category&amp;id=%d\">Bez zaniklých</a> &middot; 
					<a href=\"admin.php?page=export&amp;action=categoryWithCanceled&amp;id=%d\">Včetně zaniklých</a></td>", $category->id, $category->id);
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