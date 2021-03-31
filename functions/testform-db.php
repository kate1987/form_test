<?php

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