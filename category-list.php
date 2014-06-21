<?php
	include_once "category-controller.php";
	$controller = new CategoryController();
	
	$rows = $controller->getList();
?>

<div class="wrap">

<h2>Kategorie <a href="admin.php?page=category&amp;action=create" class="add-new-h2">Přidat novou</a></h2>


<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Název</th>
			<th>URL</th>
			<th>Ikona</th>
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
					
					echo '<td><strong><a href="">'.$row->nazev.'</a></strong></td>';
					echo '<td>'.$row->url.'</td>';
					echo '<td>'.$row->ikona.'</td>';
					echo '</tr>';
				}
			} 
		?>
	
	</tbody>
</table>


</div>