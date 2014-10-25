<?php

$ROOT = plugin_dir_path( __FILE__ );

include_once $ROOT."controllers/ObjectController.php";
include_once $ROOT."controllers/CategoryController.php";
include_once $ROOT."controllers/AuthorController.php";

add_action('init', 'rules');

function rules() {
	add_rewrite_rule('^katalog/objekt/([^/]*)/?','index.php?objekt=$matches[1]','top');
	add_rewrite_tag('%objekt%','([^/]*)');
	
	add_rewrite_rule('^katalog/kategorie/([^/]*)/?','index.php?kategorie=$matches[1]','top');
	add_rewrite_tag('%kategorie%','([^/]*)');
	
	add_rewrite_rule('^katalog/autor/([^/]*)/?','index.php?autor=$matches[1]','top');
	add_rewrite_tag('%autor%','([^/]*)');
	
	flush_rewrite_rules();
}

function kv_is_object() {
	global $wp_query;
	return isset($wp_query->query_vars['objekt']);
}

function kv_object_title() {
	global $wp_query;
	$oc = new ObjectController();
	
	$id = (int) $wp_query->query_vars['objekt'];
	$obj = $oc->getObjectById($id);
	if ($obj == null) {
		return "";	
	}
	
	return $obj->nazev;
}

function kv_object() {
	global $wp_query;
	$output = "";
	$oc = new ObjectController();
	
	$id = (int) $wp_query->query_vars['objekt'];
	$obj = $oc->getObjectById($id);
	if ($obj == null) {
		$output.= '<h2>Objekt nebyl nalezen</h2>';
		
		return $output;	
	}
	
	
	$output.= '<h2>'.$obj->nazev.'</h2>';
	
	if (strlen ($obj->popis) > 3) {
		$output.= '<p>'.stripslashes($obj->popis).'</p>';
	}
	
	// fotky
	$photos = $oc->getPhotosForObject();
	$upload_dir = wp_upload_dir();
	
	if (count($photos) > 0) {
		$output .= '<div class="gallery">';
		foreach ($photos as $photo) {
			$caption = "";
			if (strlen($photo->autor) > 0) {
				$caption = "Autor: ".$photo->autor;
			}
				
			
			$output .= '<a href="'. $upload_dir['baseurl'].$photo->img_large.'" data-lightbox="gallery" data-title="'.$caption.'"><img src="'. $upload_dir['baseurl'].$photo->img_thumbnail.'" class="gallery-img" /></a>';
		}
		$output .= '</div>';
	}
	
	
	// obsah
	if ($obj->zpracovano) {
		$output .= '<div>'.stripslashes($obj->obsah).'</div>';
	} else {
		$output .= '<p><em>K památce dosud nebyl zpracován text.</em></p>';	
	}
	
	// Mapa
	$output .= '<h3>Umístění</h3>';
	$output .= '<p><strong>GPS</strong>: '.$obj->latitude.', '.$obj->longitude;
	$output .= ' (<a href="https://maps.google.cz/maps?q='.$obj->latitude.','.$obj->longitude.'">Google</a>';
	$output .= ', <a href="http://www.mapy.cz?q='.$obj->latitude.','.$obj->longitude.'">Mapy.cz</a>';
	$output .= ', <a href="http://www.openstreetmap.org/search?query='.$obj->latitude.','.$obj->longitude.'">OpenStreetMap</a>)</p>';
	
	$output .= $oc->getGoogleMapPointContent($obj->latitude, $obj->longitude);
	
	
	return $output;
	
}

function kv_object_infobox() {
	global $wp_query;
	$oc = new ObjectController();
	
	$id = (int) $wp_query->query_vars['objekt'];
	$obj = $oc->getObjectById($id);
	if ($obj == null) {
		return "";	
	}
	
	$infobox = '<div id="infobox">';
	
	// kategorie
	$infobox .= '<p><strong>Kategorie:</strong><br /><a href="'.get_site_url().'/katalog/kategorie/'.$obj->kategorie.'/">'.$oc->getCategoryNameForObject($obj->kategorie).'</a></p>';
	
	// autoři
	if (count($oc->getAuthorsForObject()) > 0) {
		$infobox .= '<p><strong>Autor:</strong><br />';
		foreach($oc->getAuthorsForObject() as $author) {
			$infobox .= '<a href="'.get_site_url().'/katalog/autor/'.$author->id.'/"">'.$author->jmeno.'</a><br />';	
		}
		$infobox .= '</p>';
	}
	
	// Vznik
	if (strlen($obj->rok_vzniku) > 0) {
		$infobox .= '<p><strong>Datum vzniku:</strong><br />'.$obj->rok_vzniku.'</p>';
	}

	// Přístupnost
	if (strlen($obj->pristupnost) > 0) {
		$infobox .= '<p><strong>Přístupnost:</strong><br />'.$obj->pristupnost.'</p>';
	}
	
	// Materiál
	if (strlen($obj->material) > 0) {
		$infobox .= '<p><strong>Materiál:</strong><br />'.$obj->material.'</p>';
	}
	
	if (strlen($obj->prezdivka) > 0) {
		$infobox .= '<p><strong>Přezdívka:</strong><br />'.$obj->prezdivka.'</p>';
	}
		
	if (strlen($obj->pamatkova_ochrana) > 0) {
		$infobox .= '<p><strong>Památková ochrana:</strong><br />'.$obj->pamatkova_ochrana.'</p>';
	}
	
	$infobox .= '</div>';
	
	return $infobox;
}

/* Kategorie */

function kv_is_category() {
	global $wp_query;
	return isset($wp_query->query_vars['kategorie']);
}

function kv_category_title() {
	global $wp_query;
	$oc = new CategoryController();
	
	$id = (int) $wp_query->query_vars['kategorie'];
	$obj = $oc->getObjectById($id);
	if ($obj == null) {
		return "";
	}
	
	return $obj->nazev;
}

function kv_category() {
	global $wp_query;
	$cc = new CategoryController();
	
	$id = (int) $wp_query->query_vars['kategorie'];
	$obj = $cc->getObjectById($id);
	if ($obj == null) {
		$output = "<h2>Kategorie nebyla nalezena</h2>";
		
		$output.= '<p>Kategorii nebylo možno nalézt. Buď byla zrušena nebo nikdy neexistovala.
					Přejděte zpět na <a href="'.get_site_url().'/katalog/">seznam kategorií</a>.';
		
		return $output;
	}
	
	$output = '<h2>'.$obj->nazev.'</h2>';
	
	$objects = $cc->getObjectsInCategory($id);
	if (count($objects) == 0) {
		$output.='<p>V kategorii není žádný objekt.</p>';	
	} else {
		$output .= '<p>'.$obj->popis.'</p>';
		
		$output .= '<p>Počet objektů v kategorii: '.count($objects).'</p>';	
		
		$output .= '<table class="list"><thead><tr><th>Název</th></thead><tbody>';
		
		$i = 0;
		foreach($objects as $object) {
			$i++;
			
			if ($i % 2 == 0) {
				$output .= '<tr><td><a href="'.get_site_url().'/katalog/objekt/'.$object->id.'/">'.$object->nazev.'</a></td></tr>';
			} else {
				$output .= '<tr class="odd"><td><a href="'.get_site_url().'/katalog/objekt/'.$object->id.'/">'.$object->nazev.'</a></td></tr>';
			}
		}
		$output .= '</tbody></table>';
	} 
	
	return $output;
}


function kv_is_author() {
	global $wp_query;
	return isset($wp_query->query_vars['autor']);
}


function kv_author() {
	global $wp_query;
	$cc = new AuthorController();
	
	$id = (int) $wp_query->query_vars['autor'];
	$obj = $cc->getObjectById($id);
	if ($obj == null) {
		$output = "<h2>Autor nebyl nalezen</h2>";
		
		$output.= '<p>Autora nebylo možné nalézt. Buď byl zrušen nebo nikdy neexistoval.
					Přejděte zpět na <a href="'.get_site_url().'/katalog/">seznam kategorií</a>.';
		
		return $output;
	}

	$output.= '<h2>'.$obj->jmeno.'</h2>';
	
	$output.= '<h3>Realizace</h3>';
	
	if (count($cc->getListByAuthor()) == 0) {
		$output.= '<p>Prozatím nejsou u autora evidovány žádné realizace.</p>';
	} else {
		$output.= '<ul>';
		foreach ($cc->getListByAuthor() as $object) {
			$output.= '<li><a href="'.get_site_url().'/katalog/objekt/'.$object->id.'/">'.$object->nazev.'</a></li>';
		}
		$output.= '</ul>';
	}
	
	return $output;
}
	
function kv_author_title() {
	global $wp_query;
	$oc = new AuthorController();
	
	$id = (int) $wp_query->query_vars['autor'];
	$obj = $oc->getObjectById($id);
	if ($obj == null) {
		return "";
	}
	
	return $obj->jmeno;
}

?>