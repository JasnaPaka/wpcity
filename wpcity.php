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
	add_submenu_page( 'wpcity', 'Správa kategorií', 'Kategorie', 'manage_options', 'wpCityCategoryPage', 'wpCityCategoryPageCallback');
}

function wpCityCategoryPageCallback() {
	
	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
	echo '<h2>My Custom Submenu Page</h2>';
	echo '</div>';

}

/** Mapa*/
include "mapa.php";


