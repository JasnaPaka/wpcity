<?php

function kv_ObjektPocet() {
	global $wpdb;
	
	return $wpdb->get_var("SELECT count(*) FROM kv_objekt");
}

?>