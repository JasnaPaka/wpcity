<?php

include __DIR__."/controllers/CategoryController.php";

function kv_AddForm($atts) {
	include __DIR__."/pages/object/add-form.php";
	//return "abc";
}

add_shortcode('kv-add-form', 'kv_AddForm');


function kv_category_list($atts) {
	$cc = new CategoryController();
	
	$output .= '<dl>';
	
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



?>