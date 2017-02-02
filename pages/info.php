<?php
    $ROOT = plugin_dir_path( __FILE__ )."../";
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
            <th>Datum přidání</th>
            <th>Autor příspěvku</th>
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
				print '<tr class="alternate">';
				$barva = false;
			} else {
				print '<tr>';
				$barva = true;
			}
			
			print '<td><a href="admin.php?page=object&amp;action=view&amp;id='.$row->id.'"><strong>'.$row->nazev.'</strong></a></td>';

			printf ('<td>%s</td>', date_format(new DateTime($row->pridal_datum), "d. m. Y"));
			printf ('<td>%s</td>', $row->pridal_autor);

			print '</tr>';
					
		}

}  ?>
		
	</thead>

	</tbody>
</table>


