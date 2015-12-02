<?php
    $ROOT = plugin_dir_path( __FILE__ )."../../";
    $ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

    include_once $ROOT."controllers/ObjectController.php";
    $controller = new ObjectController();
    $row = $controller->getObjectFromUrl();

    if (isset($_POST["submit"])) {
        $poi = $controller->updatePoi();
    } else {
        $poi = $controller->getPoiFromUrl();
    }
?>

<div class="wrap">

<?php 
    if ($poi == null) {
?>

<div class="error below-h2">
    <p>Bod nebyl nalezen. </p>
    <p><a href="admin.php?page=object&amp;action=poi-list&amp;id=<?php print($row->id) ?>">Zpět na výpis bodů</a></p>
</div>

<?php 
    } else {
?>

<h2>Úprava bodu '<?php echo $poi->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=object&amp;action=poi-update&amp;id=<?php print($controller->getObjectFromUrl()->id) ?>&amp;poi=<?php print ($poi->id) ?>" method="post">

<?php include_once $ROOT."/pages/object/i-shared-form-poi.php" ?>

<p class="submit">
    <input name="submit" id="submit" class="button button-primary" value="Upravit" type="submit">
    <a href="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=poi-list" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>