<?php
	include_once "category-controller.php";
	$controller = new CategoryController();
		
	if (isset($_POST["submit"])) {
		$row = $controller->add();
	}
?>

<div class="wrap">

<h2>Přidání kategorie</h2>

<?php include_once "messages.php"; ?>

<form action="admin.php?page=category&amp;action=create" method="post">

<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="nazev">Název</label></th>
	<td><input name="nazev" id="nazev" class="regular-text" type="text" value="<?php echo $row->nazev ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="url">URL</label></th>
	<td><input name="url" id="url" class="regular-text" type="text" value="<?php echo $row->url ?>" maxlength="250" /></td>
</tr>
<tr>
	<th scope="row"><label for="ikona">Ikona</label></th>
	<td><input name="ikona" id="ikona" class="regular-text" type="text" value="<?php echo $row->ikona ?>" maxlength="250" /></td>
</tr>

</table>

<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Přidat" type="submit"></p>

</tbody>
</form>

</div>