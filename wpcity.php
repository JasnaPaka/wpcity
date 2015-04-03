<?php
/**
 * Plugin Name: WPCity
 * Plugin URI: http://www.jasnapaka.com/
 * Description: Plugin pro WordPress, který umožňuje spravovat objekty ve veřejném prostoru města.
 * Version: 0.1
 * Author: Pavel Cvrcek
 * Author URI: http://www.jasnapaka.com/
 * License: Mozilla Public License 2
 */
 
 /** Akce prováděné ještě před zobrazením stránky */
require_once ("controllers/ExportController.php");
require_once ("controllers/ObjectController.php");
require_once ("config.php");

if (WP_DEBUG && WP_DEBUG_DISPLAY) 
{
   ini_set('error_reporting', E_ALL & ~E_STRICT & ~E_DEPRECATED);
}


add_action('plugins_loaded', 'wpCitySendHeadersCallback');

function wpCitySendHeadersCallback() {
	$controller = new ExportController();		
	if (is_admin()) {

		if (isset($_GET["page"]) && ($_GET["page"]) === "export" && isset($_GET["action"])) {
			$controller = new ExportController();
			$controller->export();
		}

	}
}

function pageScripts() {
	wp_enqueue_script('jQuery', plugin_dir_url(__FILE__). '/content/js/jquery-ui/jquery-1.10.2.js');
	wp_enqueue_script('jQuery UI', plugin_dir_url(__FILE__). '/content/js/jquery-ui/jquery-ui-1.10.4.custom.min.js');
	
	wp_enqueue_style('jQuery UI CSS', plugin_dir_url(__FILE__). '/content/css/jquery-ui/jquery-ui-1.10.4.custom.min.css');	
	wp_enqueue_style('WPCity CSS', plugin_dir_url(__FILE__). '/content/css/wpcity.css');
}

/** Přidání stylů do stránky */
add_action('admin_enqueue_scripts', 'pageScripts');
 
 /** Přidáme hlavní kategorii pro správu objektů */
add_action('admin_menu', 'wpCityMenu');

function wpCityMenu(){
   add_menu_page('Správa objektů', getNeschvalenoTitle('Správa objektů'), 'manage_options', 'wpcity', 'wpCityMenuPageCallback', 'dashicons-location', 90); 
}

function wpCityMenuPageCallback(){
	require_once("pages/info.php");
}

/** Přidáme podnabídku */
function getNeschvalenoTitle($title) {
		
	$controller = new ObjectController();
	$neschvaleno = $controller->getCountKeSchvaleni();
	
	if ($neschvaleno > 0) {
		$title = $title.' <span class="update-plugins count-1"><span class="update-count">'.$neschvaleno.'</span></span>';	
	}	
	
	return $title;	
}


add_action( 'admin_menu', 'wpCityCategoryMenu' );

function wpCityCategoryMenu() {
	global $KV_SETTINGS;
	
	add_submenu_page('wpcity', 'Správa objektů', 'Objekty', 'delete_posts', 'object', 'wpCityObjectPageCallback');
	if (!$KV_SETTINGS["simple"]) {
		add_submenu_page('wpcity', 'Správa autorů', 'Autoři', 'delete_posts', 'author', 'wpCityAuthorPageCallback');
	}
	add_submenu_page('wpcity', 'Správa souborů děl', 'Soubory děl', 'delete_posts', 'collection', 'wpCityCollectionPageCallback');
	add_submenu_page('wpcity', 'Správa kategorií', 'Kategorie', 'delete_posts', 'category', 'wpCityCategoryPageCallback');	
	add_submenu_page('wpcity', 'Správa štítků', 'Štítky', 'delete_posts', 'tag', 'wpCityTagPageCallback');
	
	if (!$KV_SETTINGS["simple"]) {
		add_submenu_page('wpcity', 'Export', 'Export', 'delete_posts', 'export', 'wpCityExportPageCallback');
	}
}


function wpCityObjectPageCallback(){
	if (!isset($_GET["action"])) {
		require_once("pages/object/list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages/object/create.php");
			break;
		case 'update':
			require_once("pages/object/update.php");
			break;
		case 'delete':
			require_once("pages/object/delete.php");
			break;
		case 'list':
			require_once("pages/object/list.php");
			break;
		case 'view':
			require_once("pages/object/view.php");
			break;
		case 'photo':
			require_once("pages/object/photo.php");
			break;
		case 'author':
			require_once("pages/object/author.php");
			break;
		case 'source':
			require_once("pages/object/source.php");
			break;
		case 'collection':
			require_once("pages/object/collection.php");
			break;
		default:
			require_once("pages/object/list.php");
			break;
	}
}


function wpCityCategoryPageCallback() {
	
	if (!isset($_GET["action"])) {
		require_once("pages/category/list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages/category/create.php");
			break;
		case 'update':
			require_once("pages/category/update.php");
			break;
		case 'delete':
			require_once("pages/category/delete.php");
			break;
		case 'list':
			require_once("pages/category/list.php");
			break;
		default:
			require_once("pages/category/list.php");
			break;
	}
}

function wpCityCollectionPageCallback() {
	if (!isset($_GET["action"])) {
		require_once("pages/collection/list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages/collection/create.php");
			break;
		case 'update':
			require_once("pages/collection/update.php");
			break;
		case 'delete':
			require_once("pages/collection/delete.php");
			break;
		case 'list':
			require_once("pages/collection/list.php");
			break;
		case 'view':
			require_once("pages/collection/view.php");
			break;
		default:
			require_once("pages/collection/list.php");
			break;
	}	
}

function wpCityTagPageCallback() {
	
	if (!isset($_GET["action"])) {
		require_once("pages/tag/list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages/tag/create.php");
			break;
		case 'update':
			require_once("pages/tag/update.php");
			break;
		case 'delete':
			require_once("pages/tag/delete.php");
			break;
		case 'list':
			require_once("pages/tag/list.php");
			break;
		default:
			require_once("pages/tag/list.php");
			break;
	}
}



function wpCityAuthorPageCallback() {
	
	if (!isset($_GET["action"])) {
		require_once("pages/author/list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages/author/create.php");
			break;
		case 'update':
			require_once("pages/author/update.php");
			break;
		case 'delete':
			require_once("pages/author/delete.php");
			break;
		case 'view':
			require_once("pages/author/view.php");
			break;
		case 'list':
			require_once("pages/author/list.php");
			break;
		case 'source':
			require_once("pages/author/source.php");
			break;
		default:
			require_once("pages/author/list.php");
			break;
	}
}

function wpCityExportPageCallback() {	
	require_once("pages/export/view.php");
}	

function getKvDbPrefix() {
	global $wpdb;
	
	if (is_multisite()) {
		return "kv_".$wpdb->blogid."_";
	}
	
	return "kv_";
}


/** Mapa*/
include "mapa.php";

/** Snippety do šablon */
include "shortcodes.php";

/** Přepisovací pravidla */
include "rewrites.php";

/** Objekty */
include "object.php";

