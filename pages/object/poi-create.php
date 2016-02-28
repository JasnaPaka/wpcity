<?php
    $ROOT = plugin_dir_path( __FILE__ )."../../";
    $ROOT_URL = plugin_dir_url ( __FILE__ )."../../";
    
    include_once $ROOT."controllers/ObjectController.php";
    $controller = new ObjectController();		
    $row = $controller->getObjectFromUrl();

    if (isset($_POST["submit"])) {
        $poi = $controller->addPoi();
    }
?>

<div class="wrap">

<h2>Přidání bodu k '<?php echo $row->nazev ?>'</h2>

<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=poi-create" method="post" enctype="multipart/form-data">

<?php include_once $ROOT."/pages/object/i-shared-form-poi.php" ?>

<p class="submit">
    <input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit">
    <a href="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=poi-list" class="button">Zpět na výpis</a>
</p>

</form>

</div>
