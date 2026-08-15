<?php
/**
 * Uninstall handler.
 *
 * Runs only when the plugin is deleted (not deactivated). Cleans up roles,
 * options, and transient/cron data that the plugin created.
 *
 * @package VG\Plugin_Boilerplate
 */

// Exit if WordPress uninstall is not being invoked.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
 * Example cleanup steps — uncomment only what matches what this plugin
 * actually created:
 *
 * remove_role( 'my_custom_role' );
 * delete_option( 'vg_plugin_boilerplate_db_version' );
 * delete_option( 'vg_plugin_boilerplate_installed' );
 * delete_transient( 'vg_plugin_boilerplate_upgrading' );
 *
 * global $wpdb;
 * $wpdb->delete( $wpdb->usermeta, array( 'meta_key' => '_my_plugin_user_meta' ) );
 */


/*
 * NOTE: By default this file performs no destructive actions. It documents
 * the cleanup hooks the plugin may need. Uncomment the lines that match what
 * your plugin actually created so a delete truly removes plugin traces.
 *
 * Be deliberate: never drop shared data, and only remove things this plugin
 * owns. If your plugin creates database tables, drop them here too (they are
 * plugin-owned), but confirm with $wpdb->query and table-name prefix checks
 * before running DDL during uninstall.
 */
