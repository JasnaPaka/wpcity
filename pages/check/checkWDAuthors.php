<?php //
    $ROOT = plugin_dir_path( __FILE__ )."../../";
    $ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

    include_once $ROOT."controllers/CheckController.php";
    $controller = new CheckController();

    $rows = $controller->getWDDiffAuthors();
?>

<div class="wrap">

    <h1>Wikidata - kontrola shody údajů u autorů</h1>

    <p>Kontrola shodů místa a data narození, resp. místa data úmrtí autorů, které máme provázány s Wikidaty.</p>

    <?php if (sizeof($rows) == 0) { ?>

    <p>Nebyl nalezen žádný rozdíl.</p>

    <?php } else { ?>

    <table class="wp-list-table widefat fixed posts">
        <thead>
        <tr>
            <th>Heslo na Wikidatech</th>
            <th>Autor u nás</th>
            <th>Pole</th>
            <th>Hodnota u nás</th>
            <th>Hodnota ve Wikidatech</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><a href="https://www.wikidata.org/wiki/<?php print $row->identifikator ?>">
                            <?php print $row->identifikator ?></a></td>
                    <td><a href="/katalog/autor/<?php print $row->id ?>/"><?php print ($row->prijmeni." ".$row->jmeno) ?></a></td>
                    <td><?php print $row->popis ?></td>
                    <td>
                        <?php
                            if ($row->ourValue instanceof DateTime) {
                                print $row->ourValue->format("Y-m-d");
                            } else if (strlen(trim($row->ourValue)) > 0) {
								print $row->ourValue;
                            } else {
								print '<span aria-hidden="true">—</span>';
                            }
                        ?>
                    </td>
                    <td>
						<?php
						if ($row->wdValue instanceof DateTime) {
							print $row->wdValue->format("Y-m-d");
						} else if (strlen(trim($row->wdValue)) > 0) {
							print $row->wdValue;
						} else {
							print '<span aria-hidden="true">—</span>';
						}
						?>
                    </td>
                </tr>
			<?php } ?>
        </tbody>
    </table>
	<?php } ?>

    <p class="submit">
        <a href="admin.php?page=check" class="button">Zpět na kontrolu</a>
    </p>


</div>