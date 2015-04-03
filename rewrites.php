<?php

$ROOT = plugin_dir_path( __FILE__ );

include_once $ROOT."controllers/ObjectController.php";
include_once $ROOT."controllers/CategoryController.php";
include_once $ROOT."controllers/AuthorController.php";
include_once $ROOT."controllers/TagController.php";
include_once $ROOT."controllers/CollectionController.php";

add_action('init', 'rules');

function rules() {
	add_rewrite_rule('^katalog/pridat-dilo/?','index.php?pridat=1','top');
	add_rewrite_tag('%pridat%','([^/]*)');
	
	add_rewrite_rule('^katalog/dilo/([^/]*)/?','index.php?objekt=$matches[1]','top');
	add_rewrite_tag('%objekt%','([^/]*)');
	
	add_rewrite_rule('^katalog/autor/([^/]*)/?','index.php?autor=$matches[1]','top');
	add_rewrite_tag('%autor%','([^/]*)');
	
	add_rewrite_rule('^katalog/soubor/([^/]*)/?','index.php?soubor=$matches[1]','top');
	add_rewrite_tag('%soubor%','([^/]*)');	
	
	add_rewrite_rule('^katalog/stitek/([^/]*)/?','index.php?stitek=$matches[1]','top');
	add_rewrite_tag('%stitek%','([^/]*)');	

	add_rewrite_rule('^katalog/autori/?','index.php?autori=1','top');
	add_rewrite_tag('%autori%','([^/]*)');
	add_rewrite_tag('%search%','([^/]*)');
	
	add_rewrite_rule('^katalog/soubory/?','index.php?soubory=1','top');
	add_rewrite_tag('%soubory%','([^/]*)');	

	add_rewrite_rule('^katalog/?','index.php?prehled=1','top');
	add_rewrite_tag('%prehled%','([^/]*)');
	add_rewrite_tag('%stranka%','([^/]*)');
	
	flush_rewrite_rules();
}

add_filter('template_include', 'wpcity_plugin_display', 99);

function wpcity_plugin_display($template) {
	
	$object_id = (int) get_query_var('objekt');
	$author_id = (int) get_query_var('autor');
	$collection_id = (int) get_query_var('soubor');
	$tag_id = (int) get_query_var('stitek');
	$catalog = (int) get_query_var('prehled');
	$autori = (int) get_query_var('autori');
	$soubory = (int) get_query_var('soubory');
	$pridat = (int) get_query_var('pridat');
	
	if ($object_id > 0) {
		$new_template = locate_template( array( 'page-dilo-detail.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}
	
	if ($author_id > 0) {
		$new_template = locate_template( array( 'page-autor-detail.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}

	if ($collection_id > 0) {
		$new_template = locate_template( array( 'page-soubor-detail.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}
		
	
	if ($tag_id > 0) {
		$new_template = locate_template( array( 'page-stitek.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}
	}
	
	if ($catalog > 0 || $tag_id > 0) {
		$new_template = locate_template( array( 'page-katalog.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}	
	}
	
	if ($autori > 0) {
		$new_template = locate_template( array( 'page-autori.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}	
	}
	
	if ($soubory > 0) {
		$new_template = locate_template( array( 'page-soubory.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}	
	}
	
	if ($pridat > 0) {
		$new_template = locate_template( array( 'page-dilo-pridat.php' ) );
		if ( '' != $new_template ) {
			return $new_template ;
		}	
	}
	
	return $template;
}

function kv_object_info() {
	global $wp_query;
	
	$oc = new ObjectController();
	$id = (int) $wp_query->query_vars['objekt'];
	$obj = $oc->getObjectById($id);
	if ($obj == null) {
		return "";	
	}
	
	$obj->kategorie = $oc->getObjectCategory($obj->kategorie);
	$obj->autori = $oc->getAuthorsForObject();
	$obj->fotografiePrim = $oc->getPhotosForObjectMain();
	$obj->fotografieNotPrim = $oc->getPhotosForObjectNotMain();
	$obj->mapa = $oc->getGoogleMapPointContent($obj->latitude, $obj->longitude);
	$obj->zdroje = $oc->getSourcesForObject();
	
	return $obj;
}


function kv_author_info() {
	global $wp_query;
	
	$ac = new AuthorController();
	$id = (int) $wp_query->query_vars['autor'];
	$author = $ac->getObjectById($id);
	if ($author == null) {
		return "";	
	}	
	
	$author->pocet = $ac->getCountObjectsForAuthor($id);
	return $author;
}

function kv_collection_info() {
	global $wp_query;
	
	$cc = new CollectionController();
	$id = (int) $wp_query->query_vars['soubor'];
	$collection = $cc->getObjectById($id);
	if ($collection == null) {
		return "";	
	}	
	
	$collection->pocet = $cc->getCountObjectsInCollection($id);
	return $collection;	
}

function kv_author_objects() {
	global $wp_query;
	
	$ac = new AuthorController();
	return $ac->getListByAuthor();
	
}

function kv_collection_objects() {
	global $wp_query;
	
	$cc = new CollectionController();
	return $cc->getObjectsInCollection($cc->getCollectionId());
		
}

function kv_author_sources() {
	global $wp_query;
	
	$ac = new AuthorController();
	return $ac->getSourcesForAuthor();	
}
	
function kv_autor_seznam() {
	global $wp_query;
	
	$ac = new AuthorController();
	
	$page = (int) $wp_query->query_vars['stranka'];	 
	if ($page == null) {
		$page = 0;	
	}
		
	$authors = $ac->getCatalogPage($page, $ac->getSearchValue());
	foreach($authors as $author) {
		$author->img_512 = $ac->getImgForAuthor($author->id)->img_512;
	}
	
	return $authors;		
}

function kv_soubor_seznam() {
	global $wp_query;
	
	$cc = new CollectionController();
		
	$collections = $cc->getList();
	foreach($collections as $collection) {
		//$author->img_512 = $ac->getImgForAuthor($author->id)->img_512;
	}
	
	return $collections;		
}		
		

function kv_object_seznam() {
	global $wp_query;
	
	$oc = new ObjectController();
	
	$page = (int) $wp_query->query_vars['stranka'];	 
	if ($page == null) {
		$page = 0;	
	}
	
	$objects = $oc->getCatalogPage($page, $oc->getSearchValue());
	foreach($objects as $object) {
		$object->autori = $oc->getAuthorsByObject($object->id);
	}
	
	return $objects;
}

function kv_autor_controller() {
	return new AuthorController();
}

function kv_soubor_controller() {
	return new CollectionController();
}

function kv_object_controller() {
	return new ObjectController();
}

function kv_autor_pages_count() {
	$ac = new AuthorController();
	
	if ($ac->getSearchValue() != null) {
		return 0;	
	}
	
	$count = $ac->getCount();
	$pages = round ($count / 9, 0, PHP_ROUND_HALF_UP);
	
	if ($pages > 0) {
		return $pages-1;
	}
	
	return 0;
}

function kv_object_pages_count() {
	$oc = new ObjectController();
	if ($oc->getSearchValue() != null) {
		return 0;	
	}	
	
	if ($oc->getIsShowedTag()) {
		return 0;	
	}
	
	$count = $oc->getCount();
	$pages = round ($count / 9, 0, PHP_ROUND_HALF_UP);
	
	if ($pages > 0) {
		return $pages-1;
	}
	
	return 0;
}

add_filter( 'wp_title', 'kv_object_title', 99, 2 );

function kv_object_title($title, $sep) {
	global $wp_query;
	
	$object_id = (int) get_query_var('objekt');
	$author_id = (int) get_query_var('autor');
	$collection_id = (int) get_query_var('soubor');
	$tag_id = (int) get_query_var('stitek');
	$autori = (int) get_query_var('autori');
	$soubory = (int) get_query_var('soubory');
	$catalog = (int) get_query_var('prehled');	
	$pridat = (int) get_query_var('pridat');
	
	if ($object_id > 0) {
		$oc = new ObjectController();
		
		$id = (int) $wp_query->query_vars['objekt'];
		$obj = $oc->getObjectById($id);
		if ($obj == null) {
			return "Dílo nebylo nalezeno"." ".$sep." ".$title;
		}
		
		return $obj->nazev." ".$sep." ".$title;
	}
	
	if ($author_id > 0) {
		$ac = new AuthorController();
		$id = (int) $wp_query->query_vars['autor'];
		$autor = $ac->getObjectById($id);
		
		return trim($autor->titul_pred." ".$autor->jmeno." ".$autor->prijmeni." ".$autor->titul_za)." ".$sep." ".$title;
	}

	if ($collection_id > 0) {
		$cc = new CollectionController();
		$id = (int) $wp_query->query_vars['soubor'];
		$collection = $cc->getObjectById($id);
		
		return $collection->nazev." ".$sep." ".$title;
	}
	
	if ($tag_id > 0) {
		$tc = new TagController();
		$id = (int) $wp_query->query_vars['stitek'];
		$tag = $tc->getObjectById($id);
		
		return $tag->nazev." ".$sep." ".$title;
	}	
	
	
	if ($autori > 0) {
		return "Autoři"." ".$sep." ".$title;
	}
	
	if ($soubory > 0) {
		return "Soubory děl"." ".$sep." ".$title;
	}	
	
	if ($catalog > 0) {
		return "Katalog děl"." ".$sep." ".$title;
	}
	
	if ($pridat > 0) {
		return "Přidat dílo"." ".$sep." ".$title;
	}	
	
	return $title;
}


/** Odstranění Yoast SEO u stránek katalogu */
/*function remove_yoast_seo() {
	global $wp_query;
	
	$object_id = (int) get_query_var('objekt');
	$author_id = (int) get_query_var('autor');
	
	if ($object_id > 0 || $author_id > 0) {
		global $wpseo_front;
		remove_action('wp_head', array($wpseo_front,'head'), 1);
	}
}
add_filter('wp_title', 'remove_yoast_seo', 99, 3); */


?>
