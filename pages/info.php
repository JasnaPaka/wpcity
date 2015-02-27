<?php
	$ROOT = plugin_dir_path( __FILE__ )."../";

	include_once $ROOT."config.php";
	global $KV_SETTINGS;

	include_once $ROOT."controllers/GeneralController.php";
	
	$controller = new GeneralController();
	$rows = $controller->getNeschvaleneList();
?>

<h2>Nástěnka pro správu objektů</h2>

<h3>Objekty ke schválení</h3>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Název</th>
		</tr>
		
<?php if ($controller->getCountKeSchvaleni() == 0) { ?>
		
		<tr class="no-items">
			<td class="colspanchange">
				Nebyl nalezen žádný objekt.
			</td>
		</tr>	
		
<?php 	} else {
		$barva = true;
		foreach ($rows as $row) {
			if ($barva) {
				echo '<tr class="alternate">';
				$barva = false;
			} else {
				echo '<tr>';
				$barva = true;
			}
			
			echo '<td><a href="admin.php?page=object&amp;action=view&amp;id='.$row->id.'"><strong>'.$row->nazev.'</strong></a></td>';
			echo '</tr>';
					
		}

}  ?>
		
	</thead>

	</tbody>
</table>


