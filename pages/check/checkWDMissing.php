<?php //
$ROOT = plugin_dir_path( __FILE__ )."../../";
$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

include_once $ROOT."controllers/CheckController.php";
$controller = new CheckController();

$rows = $controller->getWDMissing();
?>

<div class="wrap">

	<h1>Wikidata - chybějící provázání</h1>

	<p>Dohledání objektů, u kterých chybí na jedné straně provázání.</p>

	<?php if (count($rows) === 0) { ?>
		<p>Nebylo nalezeno nic na provázání.</p>
	<?php } else { ?>
		<table class="wp-list-table widefat fixed posts">
			<thead>
			<tr>
				<th>Heslo na Wikidatech</th>
				<th>Název hesla na Wikidatech</th>
				<th>Heslo u nás</th>
				<th>Kde chybí provázání</th>
			</tr>
			</thead>
		<tbody>
			<?php foreach ($rows as $row) { ?>
			<tr>
				<td><a href="https://www.wikidata.org/wiki/<?php print $row->wdIdentifikator ?>">
						<?php print $row->wdIdentifikator ?></a></td>
				<td>
					<?php if (isset($row->wdNazev)) {
						print $row->wdNazev;
					} else {
						print '<span aria-hidden="true">—</span>';
					} ?>
				</td>
				<td>
					<?php if (isset($row->dbNazev)) {
						printf('<a href="/katalog/polozka/%s/">%s</a>', $row->dbIdentifikator, $row->dbNazev);
					} else {
						print '<span aria-hidden="true">—</span>';
					} ?>
				</td>
				<td><?php if ($row->chybiNaWD) { print 'Chybí ve Wikidatech'; } else { print 'Chybí u nás'; } ?></td>
			</tr>
			<?php } ?>
	<?php } ?>

	<p class="submit">
		<a href="admin.php?page=check" class="button">Zpět na kontrolu</a>
	</p>

</div>