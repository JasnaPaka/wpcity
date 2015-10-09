<?php

// Typ stránky 
function yoast_change_opengraph_type($type) {	
	global $wp_query;

	if (isset($wp_query->query_vars['objekt']) || isset($wp_query->query_vars['autor'])) {
    	return 'article';
	}
	
	return $type;
}
add_filter('wpseo_opengraph_type', 'yoast_change_opengraph_type', 10, 1 );

// Nadpis stránky
function yoast_change_opengraph_title($title) {	
	global $wp_query;

	// objekt
	if (isset($wp_query->query_vars['objekt'])) {
		$obj = kv_object_info();
		if ($obj != null) {
			return $obj->nazev." &raquo; ".$title;
		}
	}
	// autor
	if (isset($wp_query->query_vars['autor'])) {
		$au = kv_author_info();
		if ($au != null) {
			return $au->jmeno." ".$au->prijmeni." &raquo; ".$title;
		}
	}	
	// soubor děl
	if (isset($wp_query->query_vars['soubor'])) {
		$so = kv_collection_info();
		if ($so != null) {
			return $so->nazev." &raquo; ".$title;
		}		
	}
	
	return $title;
}
add_filter('wpseo_opengraph_title', 'yoast_change_opengraph_title', 10, 1 );

// Popis stránky
function yoast_change_opengraph_description($description) {	
	global $wp_query;

	// objekt
	if (isset($wp_query->query_vars['objekt'])) {
		$obj = kv_object_info();
		if ($obj != null) {
			return $obj->popis;
		}
	}
	// autor
	if (isset($wp_query->query_vars['autor'])) {
		return "Informace o autorovi děl.";
	}
	// soubor děl
	if (isset($wp_query->query_vars['soubor'])) {
		$so = kv_collection_info();
		if ($so != null) {
			return $so->popis;
		}		
	}
	
	return $description;
}
add_filter('wpseo_opengraph_description', 'yoast_change_opengraph_description', 10, 1 );
add_filter('wpseo_metadesc', 'yoast_change_opengraph_description', 10, 1 );

// Fotka
function yoast_change_opengraph_image($image) {	
	global $wp_query;

	// objekt
	if (isset($wp_query->query_vars['objekt'])) {
		$obj = kv_object_info();
		if ($obj != null) {
			if ($obj->fotografiePrim->img_512 != null) {
				$upload_dir = wp_upload_dir();
				return $upload_dir['baseurl'].$obj->fotografiePrim->img_512;
			}
		}
	}
	// autor
	if (isset($wp_query->query_vars['autor'])) {
		$au = kv_author_info();
		if ($au != null && $au->img_512 != null) {
			$upload_dir = wp_upload_dir();
			return $upload_dir['baseurl'].$au->img_512;
		}
	}
	// soubor děl
	if (isset($wp_query->query_vars['soubor'])) {
		$so = kv_collection_info();
		if ($so != null) {
			$upload_dir = wp_upload_dir();
			return $upload_dir['baseurl'].$so->img_512;
		}		
	}	
	
	return $image;
}
add_filter('wpseo_opengraph_image', 'yoast_change_opengraph_image', 10, 1 );

// URL
function yoast_change_opengraph_url($url) {	
	global $wp_query;
	
	return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}
add_filter('wpseo_opengraph_url', 'yoast_change_opengraph_url', 10, 1 );


?>