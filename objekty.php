<?php

function kv_ObjektPocet() {
	global $wpdb;
	
	return $wpdb->get_var("SELECT count(*) FROM kv_objekt WHERE deleted = 0");
}

add_shortcode('kv-objekt-pocet', 'kv_ObjektPocet');


?>