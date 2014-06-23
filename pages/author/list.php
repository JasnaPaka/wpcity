<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/AuthorController.php";
	$controller = new AuthorController();
	
	$rows = $controller->getList();
?>

<div class="wrap">

<h2>Autoři <a href="admin.php?page=author&amp;action=create" class="add-new-h2">Přidat nového</a></h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<div class="tablenav top">
	<?php include $ROOT."fw/templates/navigation.php"; ?>
</div>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Jméno</th>
			<th>Datum narození</th>
			<th>Datum úmrtí</th>
			<th>Akce</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if (sizeof ($rows) == 0) {
		?>
		
		<tr class="no-items">
			<td class="colspanchange" colspan="3">
				Nebyl nalezen žádný autor.
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
					
					echo '<td><strong>'.$row->jmeno.'</strong></td>';
					echo '<td>'.$row->datum_narozeni.'</td>';
					echo '<td>'.$row->datum_umrti.'</td>';
					echo '<td><a href="admin.php?page=author&amp;action=update&amp;id='.$row->id.'" title="Upraví autora">Upravit</a> 
						&middot; <a href="admin.php?page=author&amp;action=delete&amp;id='.$row->id.'" title="Smaže autora">Smazat</a></td>';
					echo '</tr>';
				}
			} 
		?>
	
	</tbody>
</table>

<div class="tablenav bottom">
	<?php include $ROOT."fw/templates/navigation.php"; ?>
</div>

