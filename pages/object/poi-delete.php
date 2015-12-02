<?php
    $ROOT = plugin_dir_path( __FILE__ )."../../";

    include_once $ROOT."controllers/ObjectController.php";
    $controller = new ObjectController();
    $row = $controller->getObjectFromUrl();

    if (isset($_POST["submit"])) {
        $poi = $controller->deletePoi();
    } else {
        $poi = $controller->getPoiFromUrl();
    }
?>

<div class="wrap">

<?php 
    if ($poi == null && isset($_POST["submit"])) {
?>

<div class="updated below-h2">
    <p>Bod byl úspěšně smazán.</p>

    <a href="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=poi-list">Zpět na výpis</a>
</div>

<?php    
    } else if ($row == null) {
?>

<div class="error below-h2">
    <p>Bod nebyl nalezen. </p>

    <a href="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=poi-list">Zpět na výpis</a>
</div>

<?php 
    } else {
?>

<h2>Smazání souvisejícího bodu k '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=object&amp;action=poi-delete&amp;id=<?php print($controller->getObjectFromUrl()->id) ?>&amp;poi=<?php print ($poi->id) ?>" method="post">

<p>Chcete skutečně smazat bod <strong><?php echo $poi->nazev ?></strong> k objektu <strong><?php echo $row->nazev ?></strong>?</p>


<input type="hidden" name="id" value="<?php print($poi->id) ?>" />

<p class="submit">
    <input name="submit" id="submit" class="button button-primary" value="Smazat" type="submit">
    <a href="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=poi-list" class="button">Zpět na výpis</a>
</p>

</form>

<?php 
	}
?>

</div>

