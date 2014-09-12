<?php

function kv_MapaLegenda() {
	global $wpdb;
	$output = '<div>';
	
	$rows = $wpdb->get_results("SELECT * FROM kv_kategorie WHERE deleted = 0 AND systemova = 0 ORDER BY nazev");
	foreach($rows as $row) {
		$count = $wpdb->get_var("SELECT count(*) FROM kv_objekt WHERE deleted = 0 AND schvaleno = 1 AND kategorie = ".$row->id);
		
		$output.= '<img src="'.$row->ikona.'" alt="" />
			<input name="'.$row->url.'" id="kv_category'.$row->id.'" 
			onclick="kv_zmenaViditelnostiSkupiny(\''.$row->id.'\')" '.($row->checked? 'checked="checked"': '').' type="checkbox" />
			<label for="kv_category'.$row->id.'" title="Počet objektů v kategorii: '.$count.'">'.$row->nazev.'</label><br />';
	}
	
	// A nyní systémové
	$rows = $wpdb->get_results("SELECT * FROM kv_kategorie WHERE deleted = 0 AND systemova = 1 ORDER BY nazev");
	if (count($rows) > 0) {
		$output = $output."<hr />";
		foreach($rows as $row) {
			$count = $wpdb->get_var("SELECT count(*) FROM kv_objekt WHERE deleted = 0 AND schvaleno = 1 AND kategorie = ".$row->id);
			
			$output.= '<img src="'.$row->ikona.'" alt="" />
				<input name="'.$row->url.'" id="kv_category'.$row->id.'" 
				onclick="kv_zmenaViditelnostiSkupiny(\''.$row->id.'\')" type="checkbox" />
				<label for="kv_category'.$row->id.'" title="Počet objektů v kategorii: '.$count.'">'.$row->nazev.'</label><br />';
		}
	}
	
	return $output."</div>";
}

function kv_MapaData() {
	global $wpdb;
	
	$uploadDir = wp_upload_dir();
	$siteUrl = site_url();
	$output = "";
	
	$rows = $wpdb->get_results("SELECT kv.*, kv_kategorie.ikona,
		(SELECT img_thumbnail FROM kv_fotografie WHERE objekt = kv.id AND deleted = 0 order by primarni DESC, id LIMIT 1) as img_thumbnail,
		(SELECT img_large FROM kv_fotografie WHERE objekt = kv.id AND deleted = 0 order by primarni DESC, id LIMIT 1) as img_large,
		kv_kategorie.checked
		FROM kv_objekt AS kv INNER JOIN kv_kategorie ON kv.kategorie = kv_kategorie.id WHERE kv.deleted = 0 AND kv.schvaleno = 1
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
		
		// Trvalý odkaz
		$content = $content.'<p><a href="'.$siteUrl.'/?objekt='.$row->id.'" title="Trvalý odkaz na objekty do mapy"><img src="'.$siteUrl.'/wp-content/themes/krizky-vetrelci/images/link-icon.png" alt="" /></a>';
		
		// Pokud je uživatel přihlášen, přidáme odkaz do administrace
		if (is_user_logged_in()) {
			$content = $content.'&nbsp;<a href="wp-admin/admin.php?page=object&action=view&id='.$row->id.'" title="Úprava objektu"><img src="'.$siteUrl.'/wp-content/themes/krizky-vetrelci/images/edit-icon.png" alt="" /></a>';
		}
		
		$content = $content."</p>";
		
		$output.= '[\'<div style="white-space:nowrap; font-family: Verdana">'.$content.'</div>\','.$row->latitude.','.$row->longitude.','.$row->kategorie.',\''.$row->ikona.
		'\',\''.$nazev.'\', '.$row->checked.','.$row->id.']';
	}
		
	return $output;
}

?>