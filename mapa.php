<?php

function kv_MapaLegenda() {
	global $wpdb;
	$output = '<div>';
	
	$rows = $wpdb->get_results("SELECT * FROM kv_kategorie WHERE deleted = 0 ORDER BY nazev");
	foreach($rows as $row) {
		$count = $wpdb->get_var("SELECT count(*) FROM kv_objekt WHERE deleted = 0 AND kategorie = ".$row->id);
		
		$output.= '<img src="'.$row->ikona.'" alt="" />
			<input name="'.$row->url.'" id="kv_category'.$row->id.'" 
			onclick="kv_zmenaViditelnostiSkupiny(\''.$row->id.'\')" checked="checked" type="checkbox" />
			<label for="kv_category'.$row->id.'" title="Počet objektů v kategorii: '.$count.'">'.$row->nazev.'</label><br />';
	}
	
	return $output."</div>";
}

function kv_MapaData() {
	global $wpdb;
	
	$uploadDir = wp_upload_dir();
	$output = "";
	
	$rows = $wpdb->get_results("SELECT kv.*, kv_kategorie.ikona,
		(SELECT img_thumbnail FROM kv_fotografie WHERE objekt = kv.id AND deleted = 0 order by primarni DESC, id LIMIT 1) as img_thumbnail,
		(SELECT img_large FROM kv_fotografie WHERE objekt = kv.id AND deleted = 0 order by primarni DESC, id LIMIT 1) as img_large
		FROM kv_objekt AS kv INNER JOIN kv_kategorie ON kv.kategorie = kv_kategorie.id WHERE kv.deleted = 0 
		ORDER BY kategorie, nazev");
		
	foreach($rows as $row) {		
		if (strlen($output) > 0) {
			$output.=",";
		}
		
		$nazev = str_replace("'", "\'", $row->nazev);
		
		$content = "<p style=\"font-weight:bold\">".$nazev."</p>";
		
		// Pokud existuje obrázek, přidáme jeho náhled
		if ($row->img_thumbnail != null) {
			$content = $content.'<div><a href="'.$uploadDir["baseurl"].$row->img_large.'" target="_blank"><img src="'.$uploadDir["baseurl"].$row->img_thumbnail.'" alt="" /"></a>';
		}
		
		// Pokud je uživatel přihlášen, přidáme odkaz do administrace
		if (is_user_logged_in()) {
			$content = $content.'<p><a href="wp-admin/admin.php?page=object&action=view&id='.$row->id.'">Správa objektu</a></p>';
		}
		
		
		$output.= '[\'<div style="white-space:nowrap; font-family: Verdana">'.$content.'</div>\','.$row->latitude.','.$row->longitude.','.$row->kategorie.',\''.$row->ikona.
		'\',\''.$nazev.'\']';
	}
		
	return $output;
}

?>