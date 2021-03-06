<?php

$ROOT = plugin_dir_path(__FILE__);

include_once $ROOT . "controllers/DownloadController.php";
include_once $ROOT . "controllers/ObjectController.php";
include_once $ROOT . "controllers/AuthorController.php";
include_once $ROOT . "controllers/CategoryController.php";
include_once $ROOT . "controllers/AuthorController.php";
include_once $ROOT . "controllers/TagController.php";
include_once $ROOT . "controllers/CollectionController.php";

include_once $ROOT . "utils/WikidataIdentifier.php";

add_action('init', 'rules');

function rules()
{
	add_rewrite_rule('^mapa-webu/?', 'index.php?mapawebu=1', 'top');
	add_rewrite_tag('%mapawebu%', '([^/]*)');

	add_rewrite_rule('^katalog/pridat-dilo/?', 'index.php?pridat=1', 'top');
	add_rewrite_tag('%pridat%', '([^/]*)');

	add_rewrite_rule('^katalog/dilo/([^/]*)/?', 'index.php?objekt=$matches[1]', 'top');
	add_rewrite_tag('%objekt%', '([^/]*)');
	add_rewrite_tag('%zobrazeni%', '([^/]*)');

	add_rewrite_rule('^katalog/autor/([^/]*)/?', 'index.php?autor=$matches[1]', 'top');
	add_rewrite_tag('%autor%', '([^/]*)');
	add_rewrite_tag('%znak%', '([^/]*)');

	add_rewrite_rule('^katalog/soubor/([^/]*)/?', 'index.php?soubor=$matches[1]', 'top');
	add_rewrite_tag('%soubor%', '([^/]*)');

	add_rewrite_rule('^katalog/stitek/([^/]*)/?', 'index.php?stitek=$matches[1]', 'top');
	add_rewrite_tag('%stitek%', '([^/]*)');

	add_rewrite_rule('^katalog/kategorie/([^/]*)/bez-autora/?', 'index.php?kategorie=$matches[1]&bezautora=1', 'top');
	add_rewrite_tag('%kategorie%', '([^/]*)');
	add_rewrite_tag('%bezautora%', '([^/]*)');

	add_rewrite_rule('^katalog/kategorie/([^/]*)/?', 'index.php?kategorie=$matches[1]', 'top');
	add_rewrite_tag('%kategorie%', '([^/]*)');

	add_rewrite_rule('^katalog/autori/?', 'index.php?autori=1', 'top');
	add_rewrite_tag('%autori%', '([^/]*)');
	add_rewrite_tag('%search%', '([^/]*)');

	add_rewrite_rule('^katalog/soubory/?', 'index.php?soubory=1', 'top');
	add_rewrite_tag('%soubory%', '([^/]*)');

	add_rewrite_rule('^katalog/polozka/([^/]*)', 'index.php?polozka=$matches[1]', 'top');
	add_rewrite_tag('%polozka%', '([^/]*)');

	add_rewrite_rule('^katalog/?', 'index.php?prehled=1', 'top');
	add_rewrite_tag('%prehled%', '([^/]*)');
	add_rewrite_tag('%stranka%', '([^/]*)');

	add_rewrite_rule('^stahnout/kategorie/([^/]*)/?', 'index.php?stahnout=$matches[1]', 'top');
	add_rewrite_tag('%stahnout%', '([^/]*)');
	add_rewrite_tag('%filtr%', '([^/]*)');

	add_rewrite_rule('^stahnout/stitek/([^/]*)/?', 'index.php?stahnoutstitek=$matches[1]', 'top');
	add_rewrite_tag('%stahnoutstitek%', '([^/]*)');
	add_rewrite_tag('%filtr%', '([^/]*)');

	add_rewrite_rule('^stahnout/vse/?', 'index.php?stahnoutvse=1', 'top');
	add_rewrite_tag('%stahnoutvse%', '([^/]*)');
	add_rewrite_tag('%filtr%', '([^/]*)');

	flush_rewrite_rules();
}

add_filter('template_include', 'wpcity_plugin_display', 99);

function wpcity_plugin_display($template)
{

	$object_id = (int)get_query_var('objekt');
	$author_id = (int)get_query_var('autor');
	$collection_id = (int)get_query_var('soubor');
	$tag_id = (int)get_query_var('stitek');
	$catalog = (int)get_query_var('prehled');
	$autori = (int)get_query_var('autori');
	$soubory = (int)get_query_var('soubory');
	$polozka = (int)get_query_var('polozka');
	$pridat = (int)get_query_var('pridat');
	$mapawebu = (int)get_query_var('mapawebu');
	$category_id = (int)get_query_var('kategorie');
	$download_id = (int)get_query_var('stahnout');
	$download_tag = (int)get_query_var('stahnoutstitek');
	$download__all_id = (int)get_query_var('stahnoutvse');

	if ($download_id > 0 || $download__all_id > 0 || $download_tag > 0) {
		$new_template = locate_template(array('page-stahnout.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($object_id > 0) {
		$new_template = locate_template(array('page-dilo-detail.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($author_id > 0) {
		$new_template = locate_template(array('page-autor-detail.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($collection_id > 0) {
		$new_template = locate_template(array('page-soubor-detail.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($tag_id > 0) {
		$new_template = locate_template(array('page-stitek.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($catalog > 0 || $tag_id > 0 || $category_id > 0) {
		$new_template = locate_template(array('page-katalog.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($autori > 0) {
		$new_template = locate_template(array('page-autori.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($soubory > 0) {
		$new_template = locate_template(array('page-soubory.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($polozka > 0) {
		redirectToObject();
	}

	if ($pridat > 0) {
		$new_template = locate_template(array('page-dilo-pridat.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	if ($mapawebu > 0) {
		$new_template = locate_template(array('page-mapa-webu.php'));
		if ('' != $new_template) {
			return $new_template;
		}
	}

	return $template;
}

function redirectToObject() {
	global $wp_query;

	$id = (int) $wp_query->query_vars['polozka'];
	if (!WikidataIdentifier::getIsValidIdentifier($id)) {
		$wp_query->set_404();
		status_header( 404 );
		get_template_part( 404 );
		exit();
	}

	wp_redirect(WikidataIdentifier::getURLForRedirect($id));
}

function kv_object_info()
{
	global $wp_query;

	$oc = new ObjectController();
	$id = (int)$wp_query->query_vars['objekt'];
	$obj = $oc->getObjectById($id);
	if ($obj == null) {
		return "";
	}

	$obj->kategorie = $oc->getObjectCategory($obj->kategorie);
	$obj->autori = $oc->getAuthorsForObject();
	$obj->fotografiePrim = $oc->getPhotosForObjectMain();
	$obj->fotografieNotPrim = $oc->getPhotosForObjectNotMain();
	$obj->mapa = $oc->getGoogleMapPointContent($obj->latitude, $obj->longitude);
	foreach ($oc->getSystemSourcesForObject() as $source) {
		if ($source->typ == SourceTypes::CODE_MONUMNET) {
			continue;
		}

		$obj->zdroje[] = $source;
	}
	foreach ($oc->getSourcesForObject() as $source) {
		$obj->zdroje[] = $source;
	}

	return $obj;
}


function kv_author_info()
{
	global $wp_query;

	$ac = new AuthorController();
	$id = (int)$wp_query->query_vars['autor'];
	$author = $ac->getObjectById($id);
	if ($author == null) {
		return "";
	}

	$author->pocet = $ac->getCountObjectsForAuthor($id);
	$author->img_512 = $ac->getImgForAuthor($author->id)->img_512;

	return $author;
}

function kv_collection_info()
{
	global $wp_query;

	$cc = new CollectionController();
	$id = (int)$wp_query->query_vars['soubor'];
	$collection = $cc->getObjectById($id);
	if ($collection == null) {
		return "";
	}

	$collection->pocet = $cc->getCountObjectsInCollection($id);
	$collection->img_512 = $cc->getImgForCollection($id);
	return $collection;
}

function kv_collection_sources()
{
	$cc = new CollectionController();
	$sources = array();
	foreach ($cc->getSystemSourcesForCollection() as $source) {
		if ($source->typ == SourceTypes::CODE_MONUMNET) {
			continue;
		}

		$sources[] = $source;
	}
	foreach ($cc->getSourcesForCollection() as $source) {
		$sources[] = $source;
	}

	return $sources;
}

function kv_author_objects()
{
	$ac = new AuthorController();
	return $ac->getListByAuthor();
}

function kv_collection_objects()
{
	$cc = new CollectionController();
	return $cc->getObjectsInCollection($cc->getCollectionId());
}

function kv_author_sources()
{
	$ac = new AuthorController();
	$sources = array();
	foreach ($ac->getSystemSourcesForAuthor() as $source) {
		if ($source->typ == SourceTypes::CODE_MONUMNET) {
			continue;
		}

		$sources[] = $source;
	}

	foreach ($ac->getSourcesForAuthor() as $source) {
		$sources[] = $source;
	}

	return $sources;
}

function kv_autor_seznam()
{
	global $wp_query;

	$ac = new AuthorController();

	$ch = $wp_query->query_vars['znak'];
	if ($ch != null && strlen($ch) > 0) {
		$authors = $ac->getCatalogByChar($ch);
	} else {
		$page = (int)$wp_query->query_vars['stranka'];
		if ($page == null) {
			$page = 0;
		}
		$authors = $ac->getCatalogPage($page, $ac->getSearchValue());
	}

	foreach ($authors as $author) {
		$author->img_512 = $ac->getImgForAuthor($author->id)->img_512;
	}

	return $authors;
}

function kv_soubor_seznam()
{
	$cc = new CollectionController();

	$collections = $cc->getList();
	foreach ($collections as $collection) {
		$collection->img_512 = $cc->getImgForCollection($collection->id);
	}

	return $collections;
}


function kv_object_seznam()
{
	global $wp_query;

	$oc = new ObjectController();

	$page = (int)$wp_query->query_vars['stranka'];
	if ($page == null) {
		$page = 0;
	}

	$objects = $oc->getCatalogPage($page, $oc->getSearchValue());
	foreach ($objects as $object) {
		$object->autori = $oc->getAuthorsByObject($object->id);
	}

	return $objects;
}

function kv_autor_controller()
{
	return new AuthorController();
}

function kv_soubor_controller()
{
	return new CollectionController();
}

function kv_source_controller() {
	return new CollectionController();
}

function kv_object_controller()
{
	return new ObjectController();
}

function kv_author_controller()
{
	return new AuthorController();
}

function kv_download_controller()
{
	return new DownloadController();
}

function kv_autor_pages_count()
{
	$ac = new AuthorController();

	if ($ac->getSearchValue() != null) {
		return 0;
	}

	if ($ac->getSearchFirstChar() != null) {
		return 0;
	}

	$count = $ac->getCount();
	$pages = round($count / 9, 0, PHP_ROUND_HALF_UP);

	if ($pages > 0) {
		return $pages - 1;
	}

	return 0;
}

function kv_object_pages_count()
{
	$oc = new ObjectController();
	if ($oc->getSearchValue() != null) {
		return 0;
	}

	if ($oc->getIsShowedTag()) {
		return 0;
	}

	if ($oc->getIShowedBezAutora()) {
		return 0;
	}

	$count = $oc->getCount();
	$pages = round($count / 9, 0, PHP_ROUND_HALF_UP);

	if ($pages > 0) {
		return $pages - 1;
	}

	return 0;
}

add_filter('wp_title', 'kv_object_title', 99, 2);

function kv_object_title($title, $sep)
{
	global $wp_query;

	$object_id = (int)get_query_var('objekt');
	$author_id = (int)get_query_var('autor');
	$collection_id = (int)get_query_var('soubor');
	$tag_id = (int)get_query_var('stitek');
	$autori = (int)get_query_var('autori');
	$soubory = (int)get_query_var('soubory');
	$catalog = (int)get_query_var('prehled');
	$pridat = (int)get_query_var('pridat');
	$mapawebu = (int)get_query_var('mapawebu');
	$bezautora = (int)get_query_var('bezautora');
	$category_id = (int)get_query_var('kategorie');

	if ($object_id > 0) {
		$oc = new ObjectController();

		$id = (int)$wp_query->query_vars['objekt'];
		$obj = $oc->getObjectById($id);
		if ($obj == null) {
			return "Dílo nebylo nalezeno" . " " . $sep . " " . $title;
		}

		return $obj->nazev . " " . $sep . " " . $title;
	}

	if ($category_id > 0) {
		$cc = new CategoryController();

		$id = (int)$wp_query->query_vars['kategorie'];
		$cat = $cc->getObjectById($id);
		if ($cat == null) {
			return "Kategorie nebyla nalezena" . " " . $sep . " " . $title;
		}
		if ($bezautora > 0) {
			return "Kategorie děl" . " " . $cat->nazev . " (díla bez autora) " . $sep . " " . $title;
		}

		return "Kategorie děl" . " " . $cat->nazev . " " . $sep . " " . $title;
	}

	if ($author_id > 0) {
		$ac = new AuthorController();
		$id = (int)$wp_query->query_vars['autor'];
		$autor = $ac->getObjectById($id);

		return trim($autor->titul_pred . " " . $autor->jmeno . " " . $autor->prijmeni . " " . $autor->titul_za) . " " . $sep . " " . $title;
	}

	if ($collection_id > 0) {
		$cc = new CollectionController();
		$id = (int)$wp_query->query_vars['soubor'];
		$collection = $cc->getObjectById($id);

		return $collection->nazev . " " . $sep . " " . $title;
	}

	if ($tag_id > 0) {
		$tc = new TagController();
		$id = (int)$wp_query->query_vars['stitek'];
		$tag = $tc->getObjectById($id);

		return $tag->nazev . " " . $sep . " " . $title;
	}


	if ($autori > 0) {
		return "Autoři" . " " . $sep . " " . $title;
	}

	if ($soubory > 0) {
		return "Soubory děl" . " " . $sep . " " . $title;
	}

	if ($catalog > 0) {
		return "Katalog děl" . " " . $sep . " " . $title;
	}

	if ($pridat > 0) {
		return "Přidat dílo" . " " . $sep . " " . $title;
	}

	if ($mapawebu > 0) {
		return "Mapa webu" . " " . $sep . " " . $title;
	}

	return $title;
}
