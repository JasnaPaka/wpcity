<?php
	$ROOT = plugin_dir_path( __FILE__ )."../../";

	include_once $ROOT."controllers/AuthorController.php";
	$controller = new AuthorController();
	
	$row = $controller->getObjectFromUrl();
?>

<div class="wrap">

<h2><?php echo $controller->getFullname() ?></h2>

<?php if ($row->datum_narozeni != null || $row->datum_umrti != null) { ?>
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
	</tbody>
	</table>
<?php } ?>

<?php if (strlen($row->obsah) > 0) { ?>

<h3>Obsah</h3>

<?php echo stripslashes($row->obsah) ?>

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
	<a href="admin.php?page=author&amp;action=delete&amp;id=<?php echo $row->id ?>" class="button">Smazat</a>
	<a href="admin.php?page=author" class="button">Zpět na výpis</a>
</p>

</div>