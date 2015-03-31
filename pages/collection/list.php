<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/CollectionController.php";
	$controller = new CollectionController();
	
	$rows = $controller->getList();
?>

<div class="wrap">

<h2>Soubory děl <a href="admin.php?page=collection&amp;action=create" class="add-new-h2">Přidat nový</a></h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Název</th>
			<th class="r">Počet objektů</th>
			<th>Akce</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if (sizeof ($rows) == 0) {
		?>
		
		<tr class="no-items">
			<td class="colspanchange" colspan="3">
				Nebyl nalezen žádný soubor děl.
			</td>
		</tr>
		
		<?php
	
			} else {
				$barva = true;
				foreach ($rows as $row) {
					if ($barva) {
						echo '<tr class="alternate">';
						$barva = false;
					} else {
						echo '<tr>';
						$barva = true;
					}
					
					printf ('<td><strong><a href="admin.php?page=collection&amp;action=view&amp;id='.$row->id.'" title="Zobrazí detail souboru děl">'.$row->nazev.'</a></strong> '.'</td>');
					printf ('<td><a href="/katalog/soubor/'.$row->id.'/">'.$controller->getCountObjectsInCollection($row->id).'</a></td>');
					printf ('<td><a href="admin.php?page=collection&amp;action=update&amp;id='.$row->id.'" title="Upraví soubor děl">Upravit</a>');
					printf (' &middot; <a href="admin.php?page=collection&amp;action=delete&amp;id='.$row->id.'" title="Smaže soubor děl">Smazat</a>');
					printf ('</td>');
					printf ('</tr>');
				}
			} 
		?>
	
	</tbody>
</table>


</div>