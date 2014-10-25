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
						echo '<tr class="alternate">';
						$barva = false;
					} else {
						echo '<tr>';
						$barva = true;
					}
					
					echo '<td><strong>'.$row->nazev.'</strong> '.($row->systemova ? '<em>(systémová)</em>' : '').'</td>';
					echo '<td>'.$controller->getCountObjectsInCategory($row->id).'</td>';
					echo '<td>'.$row->poradi.'</td>';
					echo '<td>'.$row->url.'</td>';
					echo '<td>'.$row->ikona.'</td>';
					if (!$row->systemova) {
						echo '<td><a href="admin.php?page=category&amp;action=update&amp;id='.$row->id.'" title="Upraví kategorii">Upravit</a>';
						echo ' &middot; <a href="admin.php?page=category&amp;action=delete&amp;id='.$row->id.'" title="Smaže kategorii">Smazat</a>';
						echo '</td>';
					}
					echo '</tr>';
				}
			} 
		?>
	
	</tbody>
</table>


</div>