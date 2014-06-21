<?php
	include_once $ROOT."fw/JPMessages.php";
	
	$errors = $controller->getErrorMessages();
	$infos = $controller->getInfoMessages();
?>

<?php 
	// errors
	if (count($errors) > 0) { 
		foreach ($errors as $error) {
?>

<div class="error below-h2">
	<p><?php echo $error->getMessage(); ?></p>
</div>

<?php 
		}
	} 
?>

<?php 
	// infos
	if (count($infos) > 0) { 
		foreach ($infos as $info) {
?>

<div class="updated below-h2">
	<p><?php echo $info->getMessage(); ?></p>
</div>

<?php 
		}
	} 
?>