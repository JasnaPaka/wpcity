<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/CategoryController.php";
	$controller = new CategoryController();
	
	$rows = $controller->getList();
?>

<div class="wrap">

<h2>Kategorie <a href="admin.php?page=category&amp;action=create" class="add-new-h2">Přidat novou</a></h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Název</th>
			<th class="r">Počet objektů</th>
			<th class="r">Pořadí</th>
			<th>URL</th>
			<th>Ikona</th>
			<th>Výpisy</th>
			<th>Akce</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if (sizeof ($rows) == 0) {
		?>
		
		<tr class="no-items">
			<td class="colspanchange" colspan="3">
				Nebyla nalezena žádná kategorie.
			</td>
		</tr>
		
		<?php
	
			} else {
				$barva = true;
				foreach ($rows as $row) {
					if ($barva) {
						printf('<tr class="alternate">');
						$barva = false;
					} else {
						printf('<tr>');
						$barva = true;
					}
					
					printf('<td><strong>'.$row->nazev.'</strong> '.($row->systemova ? '<em>(systémová)</em>' : '').'</td>');
					printf('<td>'.$controller->getCountObjectsInCategory($row->id).'</td>');
					printf('<td>'.$row->poradi.'</td>');
					printf('<td>'.$row->url.'</td>');
					printf('<td>'.$row->ikona.'</td>');
					
					printf('<td>');
					printf('<a href="/katalog/kategorie/'.$row->id.'/bez-autora/" title="Seznam děl bez autora">Bez autora</a>');
					printf('</td>');
					
					if (!$row->systemova) {
						printf('<td><a href="admin.php?page=category&amp;action=update&amp;id=%d" title="Upraví kategorii">Upravit</a>', $row->id);
						printf(' &middot; <a href="admin.php?page=category&amp;action=delete&amp;id=%d" title="Smaže kategorii">Smazat</a>', $row->id);
						printf('</td>');
					}
					printf('</tr>');
				}
			} 
		?>
	
	</tbody>
</table>


</div>