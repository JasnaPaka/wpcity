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
	
	$uploadDir = wp_upload_dir();
	$output = "";
	
	$wpdb->show_errors();	
	$rows = $wpdb->get_results("SELECT kv.*, kv_kategorie.ikona,
		(SELECT img_thumbnail FROM kv_fotografie WHERE objekt = kv.id order by primarni, id) as img_thumbnail,
		(SELECT img_large FROM kv_fotografie WHERE objekt = kv.id order by primarni, id) as img_large
		FROM kv_objekt AS kv INNER JOIN kv_kategorie ON kv.kategorie = kv_kategorie.id 
		ORDER BY kategorie, nazev");
		
	foreach($rows as $row) {		
		if (strlen($output) > 0) {
			$output.=",";
		}
		
		$nazev = str_replace("'", "\'", $row->nazev);
		
		$content = "<p><strong>".$nazev."</strong></p>";
		if ($row->img_thumbnail != null) {
			$content = $content.'<div><a href="'.$uploadDir["baseurl"].$row->img_large.'"><img src="'.$uploadDir["baseurl"].$row->img_thumbnail.'" alt="" /"></a>';
		}
		
		$output.= '[\'<div style="white-space:nowrap; font-family: Verdana">'.$content.'</div>\','.$row->latitude.','.$row->longitude.','.$row->kategorie.',\''.$row->ikona.
		'\',\''.$nazev.'\']';
	}
		
	return $output;
}

?>