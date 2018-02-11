<table class="form-table">
<thead>
	<tr>
        <th>Typ</th>
        <th>Identifikátor <img src="<?php print ($ROOT_URL)?>content/images/question-16.png"
                               title="Číslo (identifikátor), pod kterým je objekt dle typu veden. Např. ISBN u knihy či identifikátor u Wikidat." alt=""></th>
        <th>Název (citace zdroje)</th>
		<th>URL</th>
		<th>Čerpáno ze zdroje?</th>
		<th>Smazat?</th>
	</tr>
</thead>
<tbody>
<?php
	$i = 0;
 
	foreach ($selectedSources as $source) { 
		$i--;
		
		if (isset($source->id)) {
			$id = $source->id;
		} else {
			$id = $i;
		}
?>
	<tr>
        <td>
            <select id="typ<?php echo $id ?>" name="typ<?php echo $id ?>"
                    onchange="changeSourceType(<?php echo $id ?>, this)">
                <option value="">nezvoleno</option>
                <?php
                    foreach ($controller->getAllSourceTypes() as $type) {
                        print (sprintf('<option value="%s" %s>%s</option>',
                            $type->getCode(),
                            $type->getCode() === $source->typ ? 'selected="selected"' : "",
                            $type->getName()));
                    }
                ?>
            </select>
        </td>
        <td>
            <input type="text" id="identifikator<?php print $id ?>" name="identifikator<?php print $id ?>"
                   value="<?php print (isset($source->identifikator) ? $source->identifikator : "") ?>"
                   maxlength="255" size="30" />
        </td>
		<td>
            <input type="text" id="nazev<?php print $id ?>" name="nazev<?php print $id ?>"
                   value="<?php print (isset($source->nazev) ? $source->nazev : "") ?>"
                   maxlength="255" size="30" />
        </td>
		<td>
            <input type="text" id="url<?php print $id ?>" name="url<?php print $id ?>"
                   value="<?php print (isset($source->url) ? $source->url : "") ?>"
                   maxlength="255" size="30" />
        </td>
		<td><input name="cerpano<?php print $id ?>" id="cerpano<?php print $id ?>"
                   type="checkbox" <?php if (isset($source->cerpano) && ($source->cerpano == 1 || $source->cerpano))
                       echo 'checked="checked"' ?>/></td>
		<td>
			<input name="deleted<?php print $id ?>" id="deleted<?php print $id ?>"
                   type="checkbox" <?php if (isset($source->deleted) && ($source->deleted == 1 || $source->deleted))
                       echo 'checked="checked"' ?>/>
			<input type="hidden" id="zdroj<?php print $id ?>" name="zdroj<?php print $id ?>" value="<?php print $id ?>" />
		</td>

        <script type="application/javascript">
            changeSourceType(<?php print $id ?>, document.getElementById("typ" + "<?php print $id ?>"));
        </script>
	</tr>
<?php } ?>
</tbody>
</table>

<?php if (sizeof($selectedSystemSources) > 0) { ?>
    <h2>Automatické zdroje</h2>

    <p>Níže uvedený výčet zdrojů byl zjištěn automaticky (např. z Wikidat) a je i automaticky spravován.</p>

    <table class="form-table">
        <thead>
            <tr>
                <th>Služba</th>
                <th>Identifikátor</th>
                <th>URL</th>
                <th>Zdroj</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($selectedSystemSources as $source) { ?>
                <tr>
                    <td><?php print $controller->getSourceType($source->typ)->getName() ?></td>
                    <td><?php print $source->identifikator ?></td>
                    <td><a href="<?php print $source->url ?>"><?php print $source->url ?></a></td>
                    <td><?php print $controller->getSourceType($source->system_zdroj)->getName() ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

