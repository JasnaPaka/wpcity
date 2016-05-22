<?php
    $ROOT = plugin_dir_path( __FILE__ )."../../";

    include_once $ROOT."controllers/CheckController.php";
    $controller = new CheckController();
    
    $rows = $controller->getObjectsNoMaterial();
?>

<div class="wrap">

<h2>Díla bez uvedeného materiálu v kategorii '<?php print ($controller->getCategory()->nazev) ?>'</h2>

<p>Počet děl bez vyplněného materiálu: <?php print ($controller->getCountObjectsNoMaterial()); ?></p>

<table class="wp-list-table widefat fixed posts">
<thead>
    <tr>
        <th>Název</th>
        <th>Akce</th>
    </tr>
</thead>
<tbody>
    <?php
        if (sizeof ($rows) == 0) {
    ?>

    <tr class="no-items">
        <td class="colspanchange" colspan="3">
            Nebyl nalezen žádný objekt.
        </td>
    </tr>

    <?php

        } else {
            $barva = true;
            foreach ($rows as $row) {
                if ($barva) {
                    echo '<tr class="alternate">';
                    $barva = false;
                } else {
                    echo '<tr>';
                    $barva = true;
                }

                echo '<td><a href="admin.php?page=object&amp;action=view&amp;id='.$row->id.'"><strong>'.$row->nazev.'</strong></a></td>';
                echo '<td><a href="admin.php?page=object&amp;action=update&amp;id='.$row->id.'" title="Upraví objekt">Upravit</a> 
                        &middot; <a href="/katalog/dilo/'.$row->id.'/" title="Zobrazí detail na webu">Detail na webu</a></td>';
                echo '</tr>';
            }
        } 
    ?>
</tbody>
</table>

</div>


