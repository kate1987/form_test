<?php
/*
Plugin Name: Test form
description: Form Plugin with database entry
Version: 1.0
Author: Kate Gerbeda
Author URI: http://gerbeda.kl.com.ua
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$testform_plugin_version = '1.0.0';
$plugin_file = plugin_basename(__FILE__);							// plugin file for reference
define('TEST_FORM_PLUGIN_PATH', plugin_dir_path(__FILE__));	// define the absolute plugin path for includes
define('TEST_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));		// define the plugin url for use in enqueue

/**
 * Includes - keeping it modular
 */
include(TEST_FORM_PLUGIN_PATH . 'admin/class-testform-list-table.php');
include(TEST_FORM_PLUGIN_PATH . 'admin/testform-functions.php');
include(TEST_FORM_PLUGIN_PATH . 'functions/testform-front.php');

function formdb_install()
{
	global $wpdb, $formdb_db_version;

	$table_name = $wpdb->prefix . 'testform';
	$charset_collate = $wpdb->get_charset_collate();

	if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE `" . $table_name . "` ( ";
		$sql .= "  `id` int(11) NOT NULL auto_increment, ";
		$sql .= "  `time` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, ";
		$sql .= "  `form_email`  text  NOT NULL, ";
		$sql .= "  `form_ip`  text  NOT NULL, ";
		$sql .= "  `form_browser`  text  NOT NULL, ";
		$sql .= "  PRIMARY KEY `order_id` (`id`) ";
		$sql .= ") $charset_collate; ";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	add_option('formdb_db_version', $formdb_db_version);
}

register_activation_hook(__FILE__, 'formdb_install');

add_action('wp_enqueue_scripts', 'tf_test_form_scripts');
function tf_test_form_scripts()
{
	wp_enqueue_style('test-form-css', plugins_url('/css/test_form_style.css', __FILE__));
	wp_enqueue_script('test-form-js', plugins_url('/js/test_form_script.js', __FILE__));
	wp_localize_script( 'test-form-js', 'testform_ajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);
}

