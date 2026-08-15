<?php
/**
 * Plugin Name:       WP Plugin Boilerplate
 * Plugin URI:        https://www.example.com/
 * Description:       A WordPress plugin boilerplate — customize to build your plugin.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Your Name
 * Author URI:        https://www.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vg-plugin-boilerplate
 * Domain Path:       /languages
 *
 * @package           VG\Plugin_Boilerplate
 */

/*
 * This is the MAIN PLUGIN FILE — the single entry point WordPress loads when
 * this plugin is activated.
 *
 * ---------------------------------------------------------------------------
 * CUSTOMIZATION REQUIRED
 * ---------------------------------------------------------------------------
 * This file ships with working generic defaults so the plugin boots and passes
 * CI out of the box. When you start a real project, replace these values to
 * match YOUR plugin (keep them consistent across the codebase):
 *
 *   Namespace : VG\Plugin_Boilerplate   →  e.g. My\Awesome_Plugin
 *   Prefix    : vg_plugin_boilerplate_  →  e.g. my_awesome_plugin_
 *   Const     : VG_PLUGIN_BOILERPLATE_  →  e.g. MY_AWESOME_PLUGIN_
 *   Text domain: vg-plugin-boilerplate  →  e.g. my-awesome-plugin
 *
 * Do NOT delete this file. It is the bootstrap WordPress expects; without it
 * the plugin folder is "dead" and cannot be activated.
 * ---------------------------------------------------------------------------
 */

namespace VG\Plugin_Boilerplate\Plugin_Root;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -------------------------------------------------------------------------
// Plugin constants (guarded so they can never be redefined twice).
// -------------------------------------------------------------------------

if ( ! defined( 'VG_PLUGIN_BOILERPLATE_VERSION' ) ) {
	define( 'VG_PLUGIN_BOILERPLATE_VERSION', '0.1.0' );
}
if ( ! defined( 'VG_PLUGIN_BOILERPLATE_PLUGIN_DIR' ) ) {
	define( 'VG_PLUGIN_BOILERPLATE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'VG_PLUGIN_BOILERPLATE_PLUGIN_URL' ) ) {
	define( 'VG_PLUGIN_BOILERPLATE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'VG_PLUGIN_BOILERPLATE_PLUGIN_FILE' ) ) {
	define( 'VG_PLUGIN_BOILERPLATE_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'VG_PLUGIN_BOILERPLATE_TEXT_DOMAIN' ) ) {
	define( 'VG_PLUGIN_BOILERPLATE_TEXT_DOMAIN', 'vg-plugin-boilerplate' );
}
// Optional: a dedicated REST namespace.
if ( ! defined( 'VG_PLUGIN_BOILERPLATE_REST_NAMESPACE' ) ) {
	define( 'VG_PLUGIN_BOILERPLATE_REST_NAMESPACE', 'vg-plugin-boilerplate/v1' );
}

/**
 * Load Composer autoloader and bootstrap the plugin.
 *
 * Verifies the autoloader and the Loader file exist before doing any work so a
 * half-installed plugin degrades gracefully with a clear admin notice instead
 * of a fatal error.
 *
 * @return void
 */
function vg_plugin_boilerplate_load_plugin() {
	$autoloader = VG_PLUGIN_BOILERPLATE_PLUGIN_DIR . 'vendor/autoload.php';

	if ( ! file_exists( $autoloader ) ) {
		if ( is_admin() ) {
			add_action(
				'admin_notices',
				function () {
					printf(
						'<div class="notice notice-error"><p>%1$s %2$s %3$s</p></div>',
						esc_html__( 'WP Plugin Boilerplate Error: Composer dependencies not found. Please run', 'vg-plugin-boilerplate' ),
						wp_kses( '<code>composer install</code>', array( 'code' => array() ) ),
						esc_html__( 'in the plugin directory.', 'vg-plugin-boilerplate' )
					);
				}
			);
		}
		return; // Stop loading if dependencies are missing.
	}

	require_once $autoloader;

	$loader_file = VG_PLUGIN_BOILERPLATE_PLUGIN_DIR . 'includes/Loader.php';
	if ( ! file_exists( $loader_file ) ) {
		if ( is_admin() ) {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-error"><p>' .
						esc_html__( 'WP Plugin Boilerplate Error: Loader file missing.', 'vg-plugin-boilerplate' ) .
						'</p></div>';
				}
			);
		}
		return; // Stop loading if the Loader file is missing.
	}

	require_once $loader_file;

	// Bail if the Loader class is not present.
	if ( ! class_exists( '\VG\Plugin_Boilerplate\Loader' ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WP Plugin Boilerplate Error: Loader class not found.' );
		}
		return;
	}

	try {
		$loader = new \VG\Plugin_Boilerplate\Loader();
		$loader->init();

		/**
		 * Fires once the plugin's core components are loaded.
		 *
		 * Lets themes and other plugins hook in after initialization, and lets
		 * add-on plugins wait for the core plugin's "I'm ready" signal.
		 *
		 * @since 0.1.0
		 */
		do_action( 'vg_plugin_boilerplate_loaded' );

	} catch ( \Exception $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WP Plugin Boilerplate Plugin Initialization Error: ' . $e->getMessage() );
		}
		if ( is_admin() ) {
			add_action(
				'admin_notices',
				function () use ( $e ) {
					printf(
						'<div class="notice notice-error"><p>%s %s</p></div>',
						esc_html__( 'WP Plugin Boilerplate critical error during initialization: ', 'vg-plugin-boilerplate' ),
						esc_html( $e->getMessage() )
					);
				}
			);
		}
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\vg_plugin_boilerplate_load_plugin' );

/**
 * Load plugin textdomain.
 *
 * @return void
 */
function vg_plugin_boilerplate_load_textdomain() {
	load_plugin_textdomain(
		'vg-plugin-boilerplate',
		false,
		dirname( plugin_basename( VG_PLUGIN_BOILERPLATE_PLUGIN_FILE ) ) . '/languages'
	);
}
add_action( 'init', __NAMESPACE__ . '\vg_plugin_boilerplate_load_textdomain' );

/**
 * Activation handler.
 *
 * Runs the installer to create DB tables / seed options. The Installer file is
 * required directly because the Composer autoloader (and the Loader that
 * registers it) has not necessarily run during activation.
 *
 * @return void
 */
function vg_plugin_boilerplate_activate_plugin() {
	$installer_file = VG_PLUGIN_BOILERPLATE_PLUGIN_DIR . 'includes/DB/Installer.php';

	if ( ! file_exists( $installer_file ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WP Plugin Boilerplate Activation Error: Installer file missing at ' . $installer_file );
		}
		return;
	}

	require_once $installer_file;

	if ( class_exists( '\VG\Plugin_Boilerplate\DB\Installer' ) ) {
		try {
			$installer = new \VG\Plugin_Boilerplate\DB\Installer();
			$installer->install();
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'WP Plugin Boilerplate Activation Error: ' . $e->getMessage() );
			}
		}
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'WP Plugin Boilerplate Activation Error: Installer class not found after requiring file.' );
	}
}
register_activation_hook( VG_PLUGIN_BOILERPLATE_PLUGIN_FILE, __NAMESPACE__ . '\vg_plugin_boilerplate_activate_plugin' );

// Deactivation hook intentionally omitted (add one here if your plugin needs
// deactivation-time cleanup, e.g. clearing scheduled cron events).
