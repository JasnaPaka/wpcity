<?php //
$ROOT = plugin_dir_path( __FILE__ )."../../";
$ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

include_once $ROOT."controllers/CheckController.php";
$controller = new CheckController();

$controller->updateWD();
?>

<div class="wrap">

    <h1>Wikidata - aktualizace provázání</h1>

    <p>Aktualizace byla provedena.</p>

    <p class="submit">
        <a href="admin.php?page=check" class="button">Zpět na kontrolu</a>
    </p>

</div>
