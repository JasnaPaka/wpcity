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
 
 /** Přidáme hlavní kategorii pro správu objektů */
add_action( 'admin_menu', 'wpCityMenu' );

function wpCityMenu(){
   add_menu_page('Správa objektů', 'Správa objektů', 'manage_options', 'wpcity', 'wpCityMenuPageCallback', plugins_url( 'myplugin/images/icon.png' ), 90); 
}

function wpCityMenuPageCallback(){
	echo "";	
}

/** Přidáme podnabídku pro správu kategorií */
add_action( 'admin_menu', 'wpCityCategoryMenu' );

function wpCityCategoryMenu() {
	add_submenu_page('wpcity', 'Správa kategorií', 'Objekty', 'manage_options', 'object', 'wpCityObjectPageCallback');
	add_submenu_page('wpcity', 'Správa kategorií', 'Kategorie', 'manage_options', 'category', 'wpCityCategoryPageCallback');
}

function wpCityObjectPageCallback(){
	if (!isset($_GET["action"])) {
		require_once("pages\object\list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages\object\create.php");
			break;
		case 'update':
			require_once("pages\object\update.php");
			break;
		case 'delete':
			require_once("pages\object\delete.php");
			break;
		case 'list':
			require_once("pages\object\list.php");
			break;
		default:
			require_once("pages\object\list.php");
			break;
	}
}


function wpCityCategoryPageCallback() {
	
	if (!isset($_GET["action"])) {
		require_once("pages\category\list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages\category\create.php");
			break;
		case 'update':
			require_once("pages\category\update.php");
			break;
		case 'delete':
			require_once("pages\category\delete.php");
			break;
		case 'list':
			require_once("pages\category\list.php");
			break;
		default:
			require_once("pages\category\list.php");
			break;
	}
	
}


/** Mapa*/
include "mapa.php";


/** Informace o objektech */
include "objekty.php";
