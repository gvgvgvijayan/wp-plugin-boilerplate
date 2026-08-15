<?php
/**
 * Database Installer.
 *
 * Creates and upgrades plugin database tables using a stored version option
 * so schema changes can be applied incrementally across plugin updates.
 *
 * @package VG\Plugin_Boilerplate
 */

namespace VG\Plugin_Boilerplate\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Installer
 *
 * Responsibilities:
 *  - `install()`   : run on plugin activation to create the initial schema.
 *  - `maybe_update()` : run on every admin request to apply any pending
 *                       schema/migration steps when the stored version is
 *                       lower than the current DB version.
 *
 * The pattern is simple and idempotent: track a `db_version` option, compare
 * it to the target, and apply only the migrations whose version is newer.
 */
class Installer {

	/**
	 * The option key that stores the current database schema version.
	 *
	 * @var string
	 */
	const DB_VERSION_KEY = 'vg_plugin_boilerplate_db_version';

	/**
	 * The current schema version this plugin expects.
	 *
	 * Bump this (e.g. 1.1.0, 1.2.0) whenever you add a migration below.
	 *
	 * @var string
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Table prefix used by dbDelta (kept distinct from the option key).
	 *
	 * @var string
	 */
	const TABLE_PREFIX = 'vg_plugin_boilerplate_';

	/**
	 * Run the full installation.
	 *
	 * Called on plugin activation. Creates tables and seeds initial data by
	 * delegating to maybe_update(), which applies every migration step from a
	 * clean state rather than stamping the final version directly. This keeps
	 * fresh installs and upgrades on the exact same code path.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function install() {
		$this->maybe_update();

		// Seed any default options here:
		// if ( false === get_option( 'vg_plugin_boilerplate_installed', false ) ) {
		//     add_option( 'vg_plugin_boilerplate_installed', 1 );
		// }
	}

	/**
	 * Apply pending migrations.
	 *
	 * Hooked to `admin_init` by the Loader so any admin request catches up on
	 * schema changes introduced by a plugin update. A short-lived transient
	 * lock prevents two concurrent requests from both firing DDL.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function maybe_update() {
		$current = get_option( self::DB_VERSION_KEY, '0.0.0' );

		// Nothing to do if already on the current schema.
		if ( version_compare( $current, self::DB_VERSION, '>=' ) ) {
			return;
		}

		// Guard against concurrent migration runs.
		$lock = 'vg_plugin_boilerplate_upgrading';
		if ( get_transient( $lock ) ) {
			return;
		}
		set_transient( $lock, 1, MINUTE_IN_SECONDS );

		// Run one-off migrations here as the schema evolves, e.g.:
		// if ( version_compare( $current, '1.1.0', '<' ) ) {
		//     $this->add_some_column();
		// }

		// Always recreate tables for a canonical schema.
		$this->create_tables();

		update_option( self::DB_VERSION_KEY, self::DB_VERSION );
		delete_transient( $lock );
	}

	/**
	 * Create (or update) the plugin's database tables.
	 *
	 * Uses wpdb->dbDelta() which is idempotent and only applies the diff.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function create_tables() {
		global $wpdb;

		$table_name = $wpdb->prefix . self::TABLE_PREFIX . 'example';
		$charset    = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			title varchar(255) NOT NULL DEFAULT '',
			status varchar(20) NOT NULL DEFAULT 'active',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY status (status)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
