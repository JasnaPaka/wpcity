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
require_once ("config.php");

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
 
/** Vytvoříme výchozí data */ 
function initWpCityData() {
	global $wpdb;
	
	// tabulka pro kategorie
	$table_name = $wpdb->prefix . "kv_category";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
			id int NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			url tinytext NOT NULL,
			description text NOT NULL
		);";
		
    	//reference to upgrade.php file
    	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    	dbDelta($sql);
	}
	
	update_option( "wpCityDbVersion", 1);
}

register_activation_hook( __FILE__, 'initWpCityData');

function pageScripts() {
	wp_enqueue_script('jQuery', plugin_dir_url(__FILE__). '/content/js/jquery-ui/jquery-1.10.2.js');
	wp_enqueue_script('jQuery UI', plugin_dir_url(__FILE__). '/content/js/jquery-ui/jquery-ui-1.10.4.custom.min.js');
	//wp_enqueue_script('jQuery UI dt cs', plugin_dir_url(__FILE__). '/content/js/jquery-ui/jquery.ui.datepicker-cs.js');
	
	wp_enqueue_style('jQuery UI CSS', plugin_dir_url(__FILE__). '/content/css/jquery-ui/jquery-ui-1.10.4.custom.min.css');	
	wp_enqueue_style('WPCity CSS', plugin_dir_url(__FILE__). '/content/css/wpcity.css');
}

/** Přidání stylů do stránky */
add_action('admin_enqueue_scripts', 'pageScripts');
 
 /** Přidáme hlavní kategorii pro správu objektů */
add_action('admin_menu', 'wpCityMenu');

function wpCityMenu(){
   add_menu_page('Správa objektů', 'Správa objektů', 'manage_options', 'wpcity', 'wpCityMenuPageCallback', plugins_url( 'myplugin/images/icon.png' ), 90); 
}

function wpCityMenuPageCallback(){
	require_once("pages/info.php");
}

/** Přidáme podnabídku */
add_action( 'admin_menu', 'wpCityCategoryMenu' );

function wpCityCategoryMenu() {
	global $KV_SETTINGS;
	
	add_submenu_page('wpcity', 'Správa objektů', 'Objekty', 'delete_posts', 'object', 'wpCityObjectPageCallback');
	add_submenu_page('wpcity', 'Správa kategorií', 'Kategorie', 'delete_posts', 'category', 'wpCityCategoryPageCallback');
	if (!$KV_SETTINGS["simple"]) {
		add_submenu_page('wpcity', 'Správa autorů', 'Autoři', 'delete_posts', 'author', 'wpCityAuthorPageCallback');
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


/** Mapa*/
include "mapa.php";

/** Informace o objektech */
include "objekty.php";

/** Snippety do šablon */
include "shortcodes.php";

/** Přepisovací pravidla */
include "rewrites.php";

