<?php
$ROOT = plugin_dir_path( __FILE__ )."../../";

include_once $ROOT."controllers/TagGroupController.php";
$controller = new TagGroupController();

$rows = $controller->getList();
?>

<div class="wrap">

	<h2>Skupiny štítků <a href="admin.php?page=tagGroup&amp;action=create" class="add-new-h2">Přidat nový</a></h2>

	<?php include_once $ROOT."fw/templates/messages.php"; ?>

	<table class="wp-list-table widefat fixed posts">
		<thead>
		<tr>
			<th>Název</th>
			<th>Akce</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (sizeof ($rows) == 0) {
			?>

			<tr class="no-items">
				<td class="colspanchange" colspan="2">
					Nebyla nalezena žádná skupina štítků.
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
				printf ('<td><a href="admin.php?page=tagGroup&amp;action=update&amp;id='.$row->id.'" title="Upraví skupinu štítků">Upravit</a>');
				printf (' &middot; <a href="admin.php?page=tagGroup&amp;action=delete&amp;id='.$row->id.'" title="Smaže skupinu štítků">Smazat</a>');
				printf ('</td>');
				printf ('</tr>');
			}
		}
		?>

		</tbody>
	</table>


</div>