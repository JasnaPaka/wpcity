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
    add_menu_page( 'Správa objektů', 'Správa objektů', 'manage_options', 'wpcity', 'wpCityMenuPageCallback', plugins_url( 'myplugin/images/icon.png' ), 90); 
}

function wpCityMenuPageCallback(){
    echo "<h2>Správa objektů</h2>";	
}


/** Přidáme podnabídku pro správu kategorií */
add_action( 'admin_menu', 'wpCityCategoryMenu' );

function wpCityCategoryMenu() {
	add_submenu_page('wpcity', 'Správa kategorií', 'Kategorie', 'manage_options', 'category', 'wpCityCategoryPageListCallback');
	//add_submenu_page('kategorie_seznam', 'Přidání kategorie', 'Přidání kategorie', 'manage_options', 'category-create', 'wpCityCategoryPageCreateCallback');
}

function wpCityCategoryPageListCallback() {
	
	if (!isset($_GET["action"])) {
		require_once("category-list.php");	
	}
	
	$action = filter_input (INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("category-create.php");
			break;
		case 'update':
			require_once("category-update.php");
			break;
		case 'delete':
			require_once("category-delete.php");
			break;
		case 'list':
			require_once("category-list.php");
			break;
		default:
			require_once("category-list.php");
			break;
	}
	
}

function wpCityCategoryPageCreateCallback() {
	require_once("category-create.php");
}

/** Mapa*/
include "mapa.php";


/** Informace o objektech */
include "objekty.php";
