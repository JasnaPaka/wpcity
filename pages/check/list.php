<?php //
    $ROOT = plugin_dir_path( __FILE__ )."../../";
    $ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

    include_once $ROOT."controllers/CheckController.php";
    $controller = new CheckController();
    
    $rows = $controller->getList();
?>

<div class="wrap">

<h1>Kontrola údajů u objektů</h1>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Název</th>
			<th>Kontroly</th>
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
					printf('<td>');
					printf('<a href="admin.php?page=check&amp;action=accessibility&amp;category='.$row->id.'" title="Seznam děl bez vyplněné přístupnosti">Bez přístupnosti</a>');
					printf('</td>');
					
					printf('</tr>');
				}
			} 
		?>
	
	</tbody>
</table>


</div>