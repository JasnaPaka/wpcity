<?php

function kv_MapCategories() {
	global $wpdb;
	$categories = array();
	
	$rows = $wpdb->get_results("SELECT * FROM ".getKvDbPrefix()."kategorie WHERE deleted = 0 ORDER BY nazev");
	foreach($rows as $row) {
		$count = $wpdb->get_var("SELECT count(*) FROM ".getKvDbPrefix()."objekt WHERE deleted = 0 AND schvaleno = 1 AND kategorie = ".$row->id);
		
		$category = new stdClass();
		$category->id = $row->id;
		$category->nazev = $row->nazev;
		$category->ikona = $row->ikona;
		$category->barva = strlen($row->barva) > 0 ? $row->barva : "white";
		$category->pocet = $count;
		$category->zaskrtnuto = $row->checked ? "active" : "";
		
		array_push($categories, $category);
	}
	
	return $categories;
}

function kv_MapaData() {
	global $wpdb;
	
	$uploadDir = wp_upload_dir();
	$siteUrl = site_url();
	$themeUrl = get_template_directory_uri()."-child-krizkyavetrelci";
	
	$rows = $wpdb->get_results("SELECT kv.*, ".getKvDbPrefix()."kategorie.ikona,
		(SELECT img_thumbnail FROM ".getKvDbPrefix()."fotografie WHERE objekt = kv.id AND deleted = 0 order by primarni DESC, id LIMIT 1) as img_thumbnail,
		(SELECT img_large FROM ".getKvDbPrefix()."fotografie WHERE objekt = kv.id AND deleted = 0 order by primarni DESC, id LIMIT 1) as img_large,
		(SELECT GROUP_CONCAT(CONCAT(ka.jmeno, ' ',ka.prijmeni) SEPARATOR ', ') FROM ".getKvDbPrefix()."autor ka INNER JOIN ".getKvDbPrefix()."objekt2autor o2a ON o2a.autor = ka.id WHERE o2a.objekt = kv.id AND ka.deleted = 0 AND o2a.deleted = 0 ORDER BY o2a.id) as autori,
		".getKvDbPrefix()."kategorie.checked,
		".getKvDbPrefix()."kategorie.zoom
		FROM ".getKvDbPrefix()."objekt AS kv INNER JOIN ".getKvDbPrefix()."kategorie ON kv.kategorie = ".getKvDbPrefix()."kategorie.id WHERE kv.deleted = 0 AND kv.schvaleno = 1
		ORDER BY kategorie, nazev");
		
	foreach($rows as $row) {		
		if (strlen($output) > 0) {
			$output.=",";
		}
		
		$nazev = str_replace("'", "\'", $row->nazev);
		
		$content = "<p style=\"font-weight:bold\"><a href=\"".$siteUrl."/katalog/dilo/".$row->id."/\">".$nazev."</a></p>";
		
		// Pokud existuje obrázek, přidáme jeho náhled
		if ($row->img_thumbnail != null) {
			$content = $content.'<div><a href="'.$siteUrl.'/katalog/dilo/'.$row->id.'/"><img src="'.$uploadDir["baseurl"].$row->img_thumbnail.'" alt="" /></a>';
		}
		
		// Pokud je uživatel přihlášen, přidáme odkaz do administrace
		//if (is_user_logged_in()) {
			$content = $content."<p style=\"margin-top: 10px\">";
			$content = $content.'<a class="buttonGreen" style="color:white;" href="/katalog/dilo/'.$row->id.'/">Více o díle</a>';
			//$content = $content.'&nbsp;<a href="/wp-admin/admin.php?page=object&action=view&id='.$row->id.'" title="Úprava objektu"><img src="'.$themeUrl.'/images/edit-icon.png" alt="" /></a>';
			$content = $content."</p>";
		//}
		
		
		
		// <div style="white-space:nowrap; font-family: Verdana">'.$content.'</div>\
		$output.= '[\'<div class="scrollFix">'.$content.'</div>\','.$row->latitude.','.$row->longitude.','.$row->kategorie.',\''.$row->ikona.
		'\',\''.$nazev.'\', '.$row->checked.','.$row->id.', '.$row->zoom.', '.$row->zruseno.', \''.($row->img_thumbnail != null ? $row->img_thumbnail : "NENI").'\']';
	}
		
	return $output;
}

function kv_ObjektPocet() {
	global $wpdb;
	
	return $wpdb->get_var("SELECT count(*) FROM ".getKvDbPrefix()."objekt WHERE deleted = 0 AND schvaleno = 1");
}


?>