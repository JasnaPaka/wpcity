<?php

include __DIR__."/controllers/CategoryController.php";
include __DIR__."/controllers/DownloadController.php";

function kv_AddForm($atts) {
	include __DIR__."/pages/object/add-form.php";
	//return "abc";
}

add_shortcode('kv-add-form', 'kv_AddForm');


function kv_category_list($atts) {
	$cc = new CategoryController();
	
	$output = '<dl>';
	
	$i = 0;
	foreach($cc->getList() as $category) {
		$i++;
		
		$output .= '<dt><a href="'.get_site_url().'/katalog/kategorie/'.$category->id.'/">'.$category->nazev.'</a> ('.$cc->getCountObjectsInCategory($category->id).')</dt>';
		$output .= '<dd>'.$category->popis.'<dd>';
	}
	
	$output .= '</dl>';
	
	return $output;
}

add_shortcode('kv-category-list', 'kv_category_list');

function kv_download_box($atts) {
	$cc = new CategoryController();
	$dc = new DownloadController();

	$output = "<table id='stahnout-gpx'><thead><tr><th>Kategorie</th><th>&nbsp;</th></tr></thead><tbody>";

	foreach ($cc->getAllCategories() as $category) {
		$output .= sprintf("<tr><th>%s</th><td><a href='/stahnout/kategorie/%d/'>Všechna díla</a> 
				&dot; <a href='/stahnout/kategorie/%d/?filtr=existujici'>Pouze existující</a></td></tr>",
				$category->nazev, $category->id, $category->id);
	}

	$output .= "</tbody></table>";

	return $output;
}

add_shortcode('kv-download-box', 'kv_download_box');