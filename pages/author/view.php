<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/AuthorController.php";
	$controller = new AuthorController();
	
	$row = $controller->getObjectFromUrl();
?>

<div class="wrap">

<h2><?php echo $controller->getFullname() ?> &nbsp;&nbsp;<a href="/katalog/autor/<?php echo $row->id ?>/" class="button">Detail autora</a></h2>

<?php if ($row->datum_narozeni != null || $row->datum_umrti != null || strlen($row->web) > 3) { ?>
	<table class="widefat" style="max-width: 500px; margin-top: 10px">
	<tbody>
	<?php if (strlen ($row->datum_narozeni) > 0) { ?>
		<tr>
			<th><strong>Narození</strong></th>
			<td><?php
                print date_format(new DateTime($row->datum_narozeni), "d. m. Y");
                if (strlen($row->misto_narozeni)) {
                    printf (', %s', $row->misto_narozeni);
                }
                ?></td>
		</tr>
	<?php } ?>
	<?php if (strlen ($row->datum_umrti) > 0) { ?>
		<tr>
			<th><strong>Úmrtí</strong></th>
			<td><?php
                print date_format(new DateTime($row->datum_umrti), "d. m. Y");
				if (strlen($row->misto_umrti)) {
					printf (', %s', $row->misto_umrti);
				}
                ?></td>
		</tr>
	<?php } ?>
	<?php if (strlen ($row->web) > 0) { ?>
		<tr>
			<th><strong>Webová stránka</strong></th>
			<td><a href="<?php print ($row->web) ?>"><?php print ($row->web) ?></a></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
<?php } ?>

<?php if (strlen($row->obsah) > 0) { ?>

<h3>Obsah</h3>

<div class="content-width">
    <?php echo stripslashes($row->obsah) ?>
</div>

<?php } ?>

<?php if (strlen($row->interni) > 1) { ?>

    <h3>Interní poznámka</h3>

    <div class="content-width">
        <?php echo stripslashes($row->interni) ?>
    </div>

<?php } ?>

<h3>Díla</h3>

<table class="wp-list-table widefat fixed posts">
<thead>
    <tr>
        <th>Název</th>
        <th>Rok osazení</th>
        <th>Kategorie</th>
        <th>Štítky</th>
    </tr>
</thead>
<tbody>
    <?php
        $objects = $controller->getListByAuthor();
        if (sizeof ($objects) == 0) {
    ?>

    <tr class="no-items">
        <td class="colspanchange" colspan="3">
            Pro autora nejsou evidována žádná díla.
        </td>
    </tr>

    <?php    
        } else {
            $barva = true;
            foreach ($objects as $object) {                
                if ($barva) {
                    print ('<tr class="alternate">');
                    $barva = false;
                } else {
                    print ('<tr>');
                    $barva = true;
                }
                
                print ('<td><a href="admin.php?page=object&amp;action=view&amp;id='.$object->id.'"><strong>'.$object->nazev.'</strong></a></td>');
                printf ('<td>%s</td>', $object->rok_vzniku);
                print ('<td>'.$controller->getCategoryNameForObject($object->kategorie).'</td>');
                print ('<td>'.$controller->getTagsForObjectStr($object->id).'</td>');
            }
        }
    ?>
</tbody>
</table>

<p class="submit">
	<a href="admin.php?page=author&amp;action=update&amp;id=<?php echo $row->id ?>" class="button button-primary">Upravit</a>
    <a href="admin.php?page=author&amp;action=source&amp;id=<?php echo $row->id ?>" class="button">Správa zdrojů</a>
	<a href="admin.php?page=author&amp;action=delete&amp;id=<?php echo $row->id ?>" class="button">Smazat</a>
	<a href="admin.php?page=author" class="button">Zpět na výpis</a>
</p>

</div>