<?php
/**
 * Plugin Name: WPCity
 * Plugin URI: http://www.jasnapaka.com/
 * Description: Plugin pro WordPress, který umožňuje spravovat objekty ve veřejném prostoru města.
 * Version: 1.2
 * Author: Pavel Cvrcek
 * Author URI: http://www.jasnapaka.com/
 * License: Mozilla Public License 2
 */

/** Akce prováděné ještě před zobrazením stránky */
require_once("controllers/ExportController.php");
require_once("controllers/ObjectController.php");
require_once("DatabaseSchemeUpdater.php");

if (WP_DEBUG && WP_DEBUG_DISPLAY) {
	ini_set('error_reporting', E_ALL & ~E_STRICT & ~E_DEPRECATED);
}

add_action('plugins_loaded', 'wpCitySendHeadersCallback');

function wpCitySendHeadersCallback()
{
	$controller = new ExportController();
	if (is_admin()) {
		if (isset($_GET["page"]) && ($_GET["page"]) === "export" && isset($_GET["action"])) {
			$controller = new ExportController();
			$controller->export();
		}
	}
}

function pageScripts()
{
	wp_enqueue_script('jQuery', plugin_dir_url(__FILE__) . '/content/js/jquery-ui/jquery-3.7.1.min.js');
	wp_enqueue_script('jQuery UI', plugin_dir_url(__FILE__) . '/content/js/jquery-ui/jquery-ui.min-1.14.1.js');

	wp_enqueue_style('jQuery UI CSS', plugin_dir_url(__FILE__) . '/content/css/jquery-ui/jquery-ui-1.11.4.css');
	wp_enqueue_style('WPCity CSS', plugin_dir_url(__FILE__) . '/content/css/wpcity.css');
}

/** Přidání stylů do stránky */
add_action('admin_enqueue_scripts', 'pageScripts');

/** Přidáme hlavní kategorii pro správu objektů */
add_action('admin_menu', 'wpCityMenu');

function wpCityMenu()
{
	add_menu_page('Správa objektů', getNeschvalenoTitle('Správa objektů'), 'delete_posts', 'wpcity', 'wpCityMenuPageCallback', 'dashicons-location', 90);
}

function wpCityMenuPageCallback()
{
	require_once("pages/info.php");
}

function wpCityChangesPageCallback()
{
	require_once("pages/changes.php");
}

/** Přidáme podnabídku */
function getNeschvalenoTitle($title)
{

	$controller = new ObjectController();
	$neschvaleno = $controller->getCountKeSchvaleni();

	if ($neschvaleno > 0) {
		$title = $title . ' <span class="update-plugins count-1"><span class="update-count">' . $neschvaleno . '</span></span>';
	}

	return $title;
}


add_action('admin_menu', 'wpCityCategoryMenu');

function wpCityCategoryMenu()
{
	add_submenu_page('wpcity', 'Správa objektů', 'Objekty', 'delete_posts', 'object', 'wpCityObjectPageCallback');
	add_submenu_page('wpcity', 'Správa autorů', 'Autoři', 'delete_posts', 'author', 'wpCityAuthorPageCallback');
	add_submenu_page('wpcity', 'Správa souborů děl', 'Soubory děl', 'delete_posts', 'collection', 'wpCityCollectionPageCallback');
	add_submenu_page('wpcity', 'Správa kategorií', 'Kategorie', 'delete_posts', 'category', 'wpCityCategoryPageCallback');
	add_submenu_page('wpcity', 'Správa štítků', 'Štítky', 'delete_posts', 'tag', 'wpCityTagPageCallback');
	add_submenu_page('wpcity', 'Správa skupin štítků', 'Skupiny štítků', 'delete_posts', 'tagGroup', 'wpCityTagGroupPageCallback');
	add_submenu_page('wpcity', 'Kontrola', 'Kontrola', 'delete_posts', 'check', 'wpCityCheckPageCallback');
	add_submenu_page('wpcity', 'Export', 'Export', 'delete_posts', 'export', 'wpCityExportPageCallback');
	add_submenu_page('wpcity', 'Nastavení', 'Nastavení', 'delete_posts', 'setting', 'wpCitySettingPageCallback');
	add_submenu_page('wpcity', 'Změny', 'Změny', 'delete_posts', 'changes', 'wpCityChangesPageCallback');

}


function wpCityObjectPageCallback()
{
	if (!isset($_GET["action"])) {
		require_once("pages/object/list.php");
	}

	$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);

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
		case 'history':
			require_once("pages/object/history.php");
			break;
		case 'poi-list':
			require_once("pages/object/poi-list.php");
			break;
		case 'poi-create':
			require_once("pages/object/poi-create.php");
			break;
		case 'poi-update':
			require_once("pages/object/poi-update.php");
			break;
		case 'poi-delete':
			require_once("pages/object/poi-delete.php");
			break;
		case 'location-update':
		case 'newphoto':
		case 'nonewphoto':
			require_once("pages/object/view.php");
			break;

		default:
			require_once("pages/object/list.php");
			break;
	}
}


function wpCityCategoryPageCallback()
{

	if (!isset($_GET["action"])) {
		require_once("pages/category/list.php");
	}

	$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
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

function wpCityCollectionPageCallback()
{
	if (!isset($_GET["action"])) {
		require_once("pages/collection/list.php");
	}

	$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
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
		case 'source':
			require_once("pages/collection/source.php");
			break;
		case 'photo':
			require_once("pages/collection/photo.php");
			break;
		default:
			require_once("pages/collection/list.php");
			break;
	}
}

function wpCityTagPageCallback()
{

	if (!isset($_GET["action"])) {
		require_once("pages/tag/list.php");
	}

	$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
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

function wpCityTagGroupPageCallback()
{

	if (!isset($_GET["action"])) {
		require_once("pages/tagGroup/list.php");
	}

	$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case 'create':
			require_once("pages/tagGroup/create.php");
			break;
		case 'update':
			require_once("pages/tagGroup/update.php");
			break;
		case 'delete':
			require_once("pages/tagGroup/delete.php");
			break;
		case 'list':
			require_once("pages/tagGroup/list.php");
			break;
		default:
			require_once("pages/tagGroup/list.php");
			break;
	}
}


function wpCityAuthorPageCallback()
{

	if (!isset($_GET["action"])) {
		require_once("pages/author/list.php");
	}

	$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
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
		case 'photo':
			require_once("pages/author/photo.php");
			break;
		default:
			require_once("pages/author/list.php");
			break;
	}
}

function wpCityCheckPageCallback()
{

	if (!isset($_GET["action"])) {
		require_once("pages/check/list.php");
	}

	$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
	switch ($action) {
		case "accessibility":
			require_once("pages/check/accessibility.php");
			break;
		case "material":
			require_once("pages/check/material.php");
			break;
		case "monuments":
			require_once("pages/check/monuments.php");
			break;
        case "checkWDAuthors":
            require_once("pages/check/checkWDAuthors.php");
            break;
		case "checkWDMissing":
			require_once("pages/check/checkWDMissing.php");
			break;
        case "checkWDUpdate":
            require_once("pages/check/checkWDUpdate.php");
            break;
		default:
			require_once("pages/check/list.php");
			break;
	}
}

function wpCityExportPageCallback()
{
	require_once("pages/export/view.php");
}

function wpCitySettingPageCallback()
{
	require_once("pages/setting/update.php");
}

function getKvDbPrefix()
{
	global $wpdb;

	if (is_multisite()) {
		return "kv_" . $wpdb->blogid . "_";
	}

	return "kv_";
}

add_action('admin_init', 'initDatabase');

function initDatabase()
{
	global $wpdb;

	$prefix = "kv_";
	if (is_multisite()) {
		$prefix = "kv_" . $wpdb->blogid . "_";
	}

	new DatabaseSchemeUpdater($prefix);
}

function upload_images_dir() {
	$uploadDir = wp_upload_dir();
	if (is_ssl()) {
		return str_replace("http:", "https:", $uploadDir["baseurl"]);
	}

	return $uploadDir["baseurl"];
}

/** Mapa*/
include "mapa.php";

/** Snippety do šablon */
include "shortcodes.php";

/** Přepisovací pravidla */
include "rewrites.php";

/** Objekty */
include "object.php";

/** OpenGraph */
include "opengraph.php";
