<?php
/**
 * PHPUnit bootstrap.
 *
 * Boots the WordPress core test library (if available) and loads the plugin.
 * For unit tests that do not need a live WordPress install, this file can
 * load just the Composer autoloader.
 *
 * @package VG\Plugin_Boilerplate
 */

namespace VG\Plugin_Boilerplate\Tests;

if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
	define( 'WP_DEBUG_LOG', true );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
	define( 'WP_DEBUG_DISPLAY', false );
}

// Load the Composer autoloader (polyfills + project classes).
require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

// Locate the WordPress core testing library.
// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// If the WP test library is present, bootstrap the full WP environment.
if ( file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	require_once $_tests_dir . '/includes/functions.php';

	/**
	 * Manually load the plugin under test.
	 *
	 * Hooked into muplugins_loaded so tests run against the real bootstrap.
	 * Uses tests_add_filter() (provided by the WP test suite) because
	 * add_filter() is not yet available at this point.
	 */
	function _manually_load_plugin() {
		$plugin_file = dirname( __DIR__, 2 ) . '/wp-plugin-boilerplate.php';
		if ( file_exists( $plugin_file ) ) {
			require_once $plugin_file;
		}
	}

	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
	tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\_manually_load_plugin' );

	require_once $_tests_dir . '/includes/bootstrap.php';
}

// NOTE: For pure unit tests (no WP functions needed), nothing else is required.
// If you add pure unit tests that reference only plugin classes, they will run
// with just the autoloader above and do not need the WP test library installed.
