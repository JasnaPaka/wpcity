<?php
    $ROOT = plugin_dir_path( __FILE__ )."../../";

    include_once $ROOT."config.php";
    global $KV_SETTINGS;

    include_once $ROOT."controllers/ObjectController.php";
    $controller = new ObjectController();
    $row = $controller->getObjectFromUrl();
    $hists = $controller->getHistoryForObject();
?>


<div class="wrap">
    
<h2>Historie úprav k '<?php echo $row->nazev ?>'</h2>    

<table class="wp-list-table widefat fixed posts">
<thead>
    <tr>
        <th>Kdy</th>        
        <th>Kdo</th>
        <th>Z</th>
        <th>Na</th>
        <th>Popis</th>
    </tr>
</thead>
<tbody>
    <?php
            if (sizeof ($hists) == 0) {
    ?>

    <tr class="no-items">
        <td class="colspanchange" colspan="3">
            Nebyly nalezeny žádné změny.
        </td>
    </tr>

    <?php
        } else {
            $barva = true;
            foreach ($hists as $hist) {
                if ($barva) {
                        echo '<tr class="alternate">';
                        $barva = false;
                } else {
                        echo '<tr>';
                        $barva = true;
                }
                
                printf ('<td>%s</td>', $hist->datum);
                printf ('<td>%s</td>', $hist->kdo);
                printf ('<td>%s</td>', $hist->pred);
                printf ('<td>%s</td>', $hist->po);
                printf ('<td>%s</td>', $hist->popis);
                
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
