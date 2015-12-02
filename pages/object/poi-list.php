<?php
    $ROOT = plugin_dir_path( __FILE__ )."../../";

    include_once $ROOT."config.php";
    global $KV_SETTINGS;

    include_once $ROOT."controllers/ObjectController.php";
    $controller = new ObjectController();
    $row = $controller->getObjectFromUrl();
    $pois = $controller->getPoisForObject();
?>


<div class="wrap">
    
<h2>Související body k '<?php echo $row->nazev ?>' 
        <a href="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=poi-create" class="add-new-h2">Přidat nový</a></h2>    

<table class="wp-list-table widefat fixed posts">
<thead>
    <tr>
        <th>Název</th>        
        <th>Souřadnice</th>
        <th>Akce</th>
    </tr>
</thead>
<tbody>
    <?php
            if (sizeof ($pois) == 0) {
    ?>

    <tr class="no-items">
        <td class="colspanchange" colspan="3">
                Nebyly nalezeny žádné související body.
        </td>
    </tr>

    <?php
        } else {
            $barva = true;
            foreach ($pois as $poi) {
                if ($barva) {
                        echo '<tr class="alternate">';
                        $barva = false;
                } else {
                        echo '<tr>';
                        $barva = true;
                }
                
                printf ('<td>%s</td>', $poi->nazev);
                printf ('<td><a href="https://maps.google.cz/maps?q=%s,%s" target="_blank">%s,%s</a></td>',
                        $poi->latitude, $poi->longitude, $poi->latitude, $poi->longitude);

                printf ('<td><a href="admin.php?page=object&amp;action=poi-update&amp;id=%s&amp;poi=%s" title="Upraví bod">Upravit</a>',
                        $row->id, $poi->id);
                printf (' &middot; <a href="admin.php?page=object&amp;action=poi-delete&amp;id=%s&amp;poi=%s" title="Smaže bod">Smazat</a></td>',
                        $row->id, $poi->id);
                printf ('</tr>');
            }
        } 
    ?>

</tbody>
</table>
    
<p class="submit">
    <a href="admin.php?page=object&amp;id=<?php print ($row->id) ?>&amp;action=view" class="button">Zpět na detail objektu</a>
</p>

</div>