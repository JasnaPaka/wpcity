<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";
    $ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

    include_once $ROOT."controllers/AuthorController.php";
	$controller = new AuthorController();
	
	$rows = $controller->getList();
?>

<div class="wrap">

<h2>
	Autoři <a href="admin.php?page=author&amp;action=create" class="add-new-h2">Přidat nového</a>

	<?php if (strlen($controller->getSearchValue()) > 0) { ?>
		<span class="subtitle">Výsledky vyhledávání pro "<?php echo $controller->getSearchValue() ?>"</span>
		
		<a href="admin.php?page=author&amp;action=list" class="add-new-h2">Zrušit vyhledávání</a>
	<?php } ?>
</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=author&amp;action=list" method="post">

<p class="search-box">
	<input type="search" id="s" name="s" value="<?php echo $controller->getSearchValue() ?>" placeholder="Zadejte jméno" />
	<input type="submit" id="search" name="search" value="Hledat" class="button" />
</p>

</form>

<div class="tablenav top">
	<?php include $ROOT."fw/templates/sort.php"; ?>
	<?php include $ROOT."fw/templates/navigation.php"; ?>
</div>

<table class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>Jméno</th>
			<th class="r" style="text-align: right">Datum narození</th>
			<th class="r" style="text-align: right">Datum úmrtí</th>
			<th class="num r">Počet objektů</th>
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
					
					echo '<td>';
					echo '<a href="admin.php?page=author&amp;action=view&amp;id='.$row->id.'"><strong>'.$row->prijmeni.' '.$row->jmeno.'</strong></a>';
					if (strlen($row->wikipediaUrl) > 0) {
                        echo '&nbsp;<a href="'.$row->wikipediaUrl.'"><img src="'.$ROOT_URL.'content/images/wikipedia-32.png" 
                                alt="" width="16" height="16"/></a>';
                    }
					echo '</td>';
					echo '<td class="r">'.$row->datum_narozeni.'</td>';
					echo '<td class="r">'.$row->datum_umrti.'</td>';
					echo '<td class="num r">'.$controller->getCountObjectsForAuthor($row->id).'</td>';
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

