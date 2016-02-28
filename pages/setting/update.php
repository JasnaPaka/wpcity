<?php
    $ROOT = plugin_dir_path( __FILE__ )."../../";
    $ROOT_URL = plugin_dir_url ( __FILE__ )."../../";

    include_once $ROOT."controllers/SettingController.php";
    $controller = new SettingController();

    if (isset($_POST["submit"])) {
        $row = $controller->update();
    } else {
        $row = $controller->getRow();
    }
?>

<div class="wrap">
    
<h2>Nastavení</h2>
    
<?php include_once $ROOT."fw/templates/messages.php"; ?>

<form action="admin.php?page=setting" method="post">

<table class="form-table">
<tbody>
<tr>
    <th scope="row" valign="top"><label for="popisProjektu">Popis</label></th>
    <td>
        <textarea id="popisProjektu" name="popisProjektu" rows="4" cols="40"><?php print($row->popisProjektu) ?></textarea>
        <p class="description">Popis webu, který se zobrazuje na titulní stránce webu.</p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="obrazekProjektu">Obrázek</label></th>
    <td>
        <input name="obrazekProjektu" id="obrazekProjektu" class="regular-text" type="text" 
               value="<?php print($row->obrazekProjektu) ?>" maxlength="250" />
        <p class="description">Cesta k náhledovému obrázku na titulní stránce webu. 
            Cesta je absolutní či relativní k adresáři s tématem webu.</p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="profilFacebook">Profil na Facebooku</label></th>
    <td>
        <input name="profilFacebook" id="profilFacebook" class="regular-text" type="text" 
               value="<?php print($row->profilFacebook) ?>" maxlength="250" />
        <p class="description">Cesta k profilu projektu na Facebooku (nepovinné).</p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="gmKey">API Key ke Google Maps</label></th>
    <td>
        <input name="gmKey" id="gmKey" class="regular-text" type="text" 
               value="<?php print($row->gmKey) ?>" maxlength="250" />
        <p class="description">
            Klíč k API Google Maps <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">lze
                získat zde</a>.
        </p>
    </td>
</tr>

<tr>
    <th scope="row"><label for="gmLat">Výchozí souřadnice Google Maps (latitude)</label></th>
    <td>
        <input name="gmLat" id="gmLat" class="regular-text" type="text" 
               value="<?php print($row->gmLat) ?>" maxlength="250" />
    </td>
</tr>

<tr>
    <th scope="row"><label for="gmLon">Výchozí souřadnice Google Maps (longitude)</label></th>
    <td>
        <input name="gmLon" id="gmLon" class="regular-text" type="text" 
               value="<?php print($row->gmLon) ?>" maxlength="250" />
    </td>
</tr>

<tr>
    <th scope="row"><label for="gmZoom">Výchozí přiblížení mapy (zoom)</label></th>
    <td>
        <input name="gmZoom" id="gmZoom" class="regular-text" type="text" 
               value="<?php print($row->gmZoom) ?>" maxlength="250" />
    </td>
</tr>

</tbody>
</table>

<p class="submit">
    <input name="submit" id="submit" class="button button-primary" value="Upravit" type="submit">
</p>

</form>    
    
</div>