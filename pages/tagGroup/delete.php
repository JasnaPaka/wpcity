<?php
$ROOT = plugin_dir_path( __FILE__ )."../../";

include_once $ROOT."controllers/TagGroupController.php";
$controller = new TagGroupController();

if (isset($_POST["submit"])) {
	$row = $controller->delete();
} else {
	$row = $controller->getObjectFromUrl();
}
?>

<div class="wrap">

	<?php
	if ($row == null && isset($_POST["submit"])) {
		?>

		<div class="updated below-h2">
			<p>Skupina štítků byla úspěšně smazána.</p>

			<p><a href="admin.php?page=tagGroup">Zpět na výpis</a></p>
		</div>

		<?php
	} else if ($row == null) {
		?>

		<div class="error below-h2">
			<p>Skupina štítků nebyla nalezena. </p>

			<p><a href="admin.php?page=tagGroup">Zpět na výpis</a></p>
		</div>

		<?php
	} else if (!$controller->getCanDelete()) {
		?>

		<div class="error below-h2">
			<p>Skupina štítků nelze smazat. Pravděpodobně je k ní přiřazen nějaký štítek.</p>

			<p><a href="admin.php?page=tagGroup">Zpět na výpis</a></p>
		</div>

		<?php
	} else {
		?>

		<h2>Smazání skupiny štítků '<?php print($row->nazev) ?>'</h2>

		<?php include_once $ROOT."fw/templates/messages.php"; ?>

		<form action="admin.php?page=tagGroup&amp;action=delete" method="post">

			<p>Chcete skutečně smazat skupinu štítků <strong><?php print($row->nazev) ?></strong>?</p>


			<input type="hidden" name="id" value="<?php print($controller->getObjectId()) ?>" />

			<p class="submit">
				<input name="submit" id="submit" class="button button-primary" value="Smazat" type="submit">
				<a href="admin.php?page=tagGroup" class="button">Zpět na výpis</a>
			</p>

		</form>

		<?php
	}
	?>

</div>