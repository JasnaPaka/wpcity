<?php

function kv_AddForm($atts) {
	include __DIR__."/pages/object/add-form.php";
	//return "abc";
}

add_shortcode('kv-add-form', 'kv_AddForm');

?>