<?php

function kv_MapaLegenda() {
	global $wpdb;
	$output = '<div>';
	
	$rows = $wpdb->get_results("SELECT * FROM kv_kategorie ORDER BY nazev");
	foreach($rows as $row) {
		$output.= '<img src="'.$row->ikona.'" alt="" />
			<input name="'.$row->url.'" id="kv_category'.$row->id.'" 
			onclick="kv_zmenaViditelnostiSkupiny(\''.$row->id.'\')" checked="checked" type="checkbox" />
			<label for="kv_category'.$row->id.'">'.$row->nazev.'</label><br />';
	}
	
	return $output."</div>";
}

function kv_MapaData() {
	global $wpdb;
	$output = "";
	
	$rows = $wpdb->get_results("SELECT kv_objekt.*, kv_kategorie.ikona FROM kv_objekt INNER JOIN kv_kategorie ON kv_objekt.kategorie = kv_kategorie.id ORDER BY kategorie, nazev");
	foreach($rows as $row) {
		if (strlen($output) > 0) {
			$output.=",";
		}
		
		$nazev = str_replace("'", "\'", $row->nazev);
		
		$output.= '[\'<div style="white-space:nowrap; font-family: Verdana">'.$nazev.'</div>\','.$row->latitude.','.$row->longitude.','.$row->kategorie.',\''.$row->ikona.'\']';
	}
		
	return $output;
}

?>