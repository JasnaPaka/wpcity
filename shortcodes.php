<?php

include __DIR__."/controllers/CategoryController.php";

function kv_AddForm($atts) {
	include __DIR__."/pages/object/add-form.php";
	//return "abc";
}

add_shortcode('kv-add-form', 'kv_AddForm');


function kv_category_list($atts) {
	$cc = new CategoryController();
	
	$output .= '<table class="list"><thead><tr><th>Název kategorie</th></thead><tbody>';
	
	$i = 0;
	foreach($cc->getList() as $category) {
		$i++;
		
		if ($i % 2 == 0) {
			$output .= '<tr><td><a href="'.get_site_url().'/katalog/kategorie/'.$category->id.'/">'.$category->nazev.'</a></td></tr>';
		} else {
			$output .= '<tr class="odd"><td><a href="'.get_site_url().'/katalog/kategorie/'.$category->id.'/">'.$category->nazev.'</a></td></tr>';
		}
	}
	
	$output .= '</tbody></table>';
	
	return $output;
}

add_shortcode('kv-category-list', 'kv_category_list');

?>