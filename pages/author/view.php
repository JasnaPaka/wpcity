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
			<th><strong>Datum narození</strong></th>
			<td><?php echo date_format(new DateTime($row->datum_narozeni), "d. m. Y") ?></td>
		</tr>
	<?php } ?>
	<?php if (strlen ($row->datum_umrti) > 0) { ?>
		<tr>
			<th><strong>Datum úmrtí</strong></th>
			<td><?php echo date_format(new DateTime($row->datum_umrti), "d. m. Y") ?></td>
		</tr>
	<?php } ?>
	<?php if (strlen ($row->web) > 0) { ?>
		<tr>
			<th><strong>Webová stránka</strong></th>
			<td><a href="<?php printf ($row->web) ?>"><?php printf ($row->web) ?></a></td>
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

<h3>Díla</h3>

<?php if (count($controller->getListByAuthor()) == 0) { ?>
	<p>Pro autora nejsou evidována žádná díla.</p>
<?php } else { ?>


<ul>
<?php foreach ($controller->getListByAuthor() as $object) { ?>
	<li><a href="admin.php?page=object&amp;action=view&amp;id=<?php echo $object->id ?>"><?php echo $object->nazev ?></a></li>
<?php } ?>
</ul>
	
<?php } ?>

<p class="submit">
	<a href="admin.php?page=author&amp;action=update&amp;id=<?php echo $row->id ?>" class="button button-primary">Upravit</a>
	<?php if (!$KV_SETTINGS["simple"]) { ?>
		<a href="admin.php?page=author&amp;action=source&amp;id=<?php echo $row->id ?>" class="button">Správa zdrojů</a>
	<?php } ?>
	<a href="admin.php?page=author&amp;action=delete&amp;id=<?php echo $row->id ?>" class="button">Smazat</a>
	<a href="admin.php?page=author" class="button">Zpět na výpis</a>
</p>

</div>