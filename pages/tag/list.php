<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/TagController.php";
	$controller = new TagController();
	
	$rows = $controller->getList();
?>

<div class="wrap">

<h2>Štítky <a href="admin.php?page=tag&amp;action=create" class="add-new-h2">Přidat nový</a></h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Název</th>
			<th class="r">Počet objektů</th>
			<th>Skupina štítků</th>
			<th>Akce</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if (sizeof ($rows) == 0) {
		?>
		
		<tr class="no-items">
			<td class="colspanchange" colspan="4">
				Nebyl nalezen žádný štítek.
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
					
					printf ('<td><strong>'.$row->nazev.'</strong> '.'</td>');
					printf ('<td><a href="/katalog/stitek/'.$row->id.'/">'.$controller->getCountObjectsWithTag($row->id).'</a></td>');

					if ($row->skupina == null) {
						print("<td></td>");
					} else {
						printf('<td><a href="admin.php?page=tagGroup&amp;action=view&amp;id=%d" 
							title="Zobrazí detail skupiny">%s</a></td>', $row->skupina, $controller->getTagGroup()->nazev);
					}

					printf ('<td><a href="admin.php?page=tag&amp;action=update&amp;id='.$row->id.'" title="Upraví štítek">Upravit</a>');
					printf (' &middot; <a href="admin.php?page=tag&amp;action=delete&amp;id='.$row->id.'" title="Smaže štítek">Smazat</a>');
					printf ('</td>');
					printf ('</tr>');
				}
			} 
		?>
	
	</tbody>
</table>


</div>