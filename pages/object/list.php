<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/ObjectController.php";
	$controller = new ObjectController();
	
	$rows = $controller->getList();
?>

<div class="wrap">

<h2>Objekty <a href="admin.php?page=object&amp;action=create" class="add-new-h2">Přidat nový</a></h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Název</th>
			<th>Latitude</th>
			<th>Longtitude</th>
			<th>Kategorie</th>
			<th>Akce</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if (sizeof ($rows) == 0) {
		?>
		
		<tr class="no-items">
			<td class="colspanchange" colspan="3">
				Nebyl nalezen žádný objekt.
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
					
					echo '<td><strong>'.$row->nazev.'</strong></td>';
					echo '<td>'.$row->latitude.'</td>';
					echo '<td>'.$row->longitude.'</td>';
					echo '<td>'.$controller->getCategoryNameForObject($row->kategorie).'</td>';
					echo '<td><a href="admin.php?page=object&amp;action=update&amp;id='.$row->id.'" title="Upraví objekt">Upravit</a> 
						&middot; <a href="admin.php?page=object&amp;action=delete&amp;id='.$row->id.'" title="Smaže objekt">Smazat</a></td>';
					echo '</tr>';
				}
			} 
		?>
	
	</tbody>
</table>	

</div>