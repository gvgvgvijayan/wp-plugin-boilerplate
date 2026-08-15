<?php
/**
 * Plugin Name:       <plugin-name>
 * Plugin URI:        https://www.example.com/
 * Description:       <Short description of what this plugin does.>
 * Version:           0.1.0
 * Requires at least: <wp-version>
 * Requires PHP:      <php-version>
 * Author:            <author-name>
 * Author URI:        https://www.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       <text-domain>
 * Domain Path:       /languages
 *
 * @package           <namespace>
 */

/*
 * This is the MAIN PLUGIN FILE — the single entry point WordPress loads when
 * this plugin is activated.
 *
 * ---------------------------------------------------------------------------
 * ⚠️  IMPORTANT — CUSTOMIZATION REQUIRED
 * ---------------------------------------------------------------------------
 * Everything below that is wrapped in angle brackets (<placeholder>) MUST be
 * replaced with values that are unique to YOUR plugin before you ship it:
 *
 *   <plugin-name>   → e.g. "My Awesome Plugin"            (shown in Plugins list)
 *   <text-domain>   → e.g. "my-awesome-plugin"            (i18n string domain)
 *   <namespace>     → e.g. "My\Awesome_Plugin"            (PHP root namespace)
 *   <prefix>        → e.g. "MY_AWESOME_PLUGIN" or "map_"  (constants / hooks)
 *   <wp-version>    → e.g. "6.5"                          (Requires at least)
 *   <php-version>   → e.g. "7.4"                          (Requires PHP)
 *
 * Do NOT delete this file. It is the bootstrap that WordPress expects; without
 * it the plugin folder is "dead" and cannot be activated.
 *
 * Replacements are intentionally left as angle-bracketed placeholders so you
 * (or an automated scaffolder) can find and swap them without missing one.
 * ---------------------------------------------------------------------------
 */

namespace <namespace>\Plugin_Root;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -------------------------------------------------------------------------
// Plugin constants (guarded so they can never be redefined twice).
// -------------------------------------------------------------------------

if ( ! defined( '<prefix>_VERSION' ) ) {
	define( '<prefix>_VERSION', '0.1.0' );
}
if ( ! defined( '<prefix>_PLUGIN_DIR' ) ) {
	define( '<prefix>_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( '<prefix>_PLUGIN_URL' ) ) {
	define( '<prefix>_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( '<prefix>_PLUGIN_FILE' ) ) {
	define( '<prefix>_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( '<prefix>_TEXT_DOMAIN' ) ) {
	define( '<prefix>_TEXT_DOMAIN', '<text-domain>' );
}
// Optional: a dedicated REST namespace, e.g. '<slug>/v1'.
if ( ! defined( '<prefix>_REST_NAMESPACE' ) ) {
	define( '<prefix>_REST_NAMESPACE', '<slug>/v1' );
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
function <slug>_load_plugin() {
	$autoloader = <prefix>_PLUGIN_DIR . 'vendor/autoload.php';

	if ( ! file_exists( $autoloader ) ) {
		if ( is_admin() ) {
			add_action(
				'admin_notices',
				function () {
					printf(
						'<div class="notice notice-error"><p>%1$s %2$s %3$s</p></div>',
						esc_html__( '<plugin-name> Plugin Error: Composer dependencies not found. Please run', '<text-domain>' ),
						'<code>composer install</code>',
						esc_html__( 'in the plugin directory.', '<text-domain>' )
					);
				}
			);
		}
		return; // Stop loading if dependencies are missing.
	}

	require_once $autoloader;

	$loader_file = <prefix>_PLUGIN_DIR . 'includes/Loader.php';
	if ( ! file_exists( $loader_file ) ) {
		if ( is_admin() ) {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-error"><p>' .
						esc_html__( '<plugin-name> Plugin Error: Loader file missing.', '<text-domain>' ) .
						'</p></div>';
				}
			);
		}
		return; // Stop loading if the Loader file is missing.
	}

	require_once $loader_file;

	// Bail if the Loader class is not present.
	if ( ! class_exists( '\<namespace>\Loader' ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( '<plugin-name> Plugin Error: Loader class \'\\<namespace>\\Loader\' not found.' );
		}
		return;
	}

	try {
		$loader = new \<namespace>\Loader();
		$loader->init();

		/**
		 * Fires once the plugin's core components are loaded.
		 *
		 * Lets themes and other plugins hook in after initialization, and lets
		 * add-on plugins wait for the core plugin's "I'm ready" signal.
		 *
		 * @since 0.1.0
		 */
		do_action( '<prefix>_loaded' );

	} catch ( \Exception $e ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( '<plugin-name> Plugin Initialization Error: ' . $e->getMessage() );
		}
		if ( is_admin() ) {
			add_action(
				'admin_notices',
				function () use ( $e ) {
					printf(
						'<div class="notice notice-error"><p>%s %s</p></div>',
						esc_html__( '<plugin-name> Plugin critical error during initialization: ', '<text-domain>' ),
						esc_html( $e->getMessage() )
					);
				}
			);
		}
	}
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\<slug>_load_plugin' );

/**
 * Load plugin textdomain.
 *
 * @return void
 */
function <slug>_load_textdomain() {
	load_plugin_textdomain(
		'<text-domain>',
		false,
		dirname( plugin_basename( <prefix>_PLUGIN_FILE ) ) . '/languages'
	);
}
add_action( 'init', __NAMESPACE__ . '\\<slug>_load_textdomain' );

/**
 * Activation handler.
 *
 * Runs the installer to create DB tables / seed options. The Installer file is
 * required directly because the Composer autoloader (and the Loader that
 * registers it) has not necessarily run during activation.
 *
 * @return void
 */
function <slug>_activate_plugin() {
	$installer_file = <prefix>_PLUGIN_DIR . 'includes/DB/Installer.php';

	if ( ! file_exists( $installer_file ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( '<plugin-name> Activation Error: Installer file missing at ' . $installer_file );
		}
		return;
	}

	require_once $installer_file;

	if ( class_exists( '\<namespace>\DB\Installer' ) ) {
		try {
			$installer = new \<namespace>\DB\Installer();
			$installer->install();
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( '<plugin-name> Activation Error: ' . $e->getMessage() );
			}
		}
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( '<plugin-name> Activation Error: Installer class not found after requiring file.' );
	}
}
register_activation_hook( <prefix>_PLUGIN_FILE, __NAMESPACE__ . '\\<slug>_activate_plugin' );

// Deactivation hook intentionally omitted (add one here if your plugin needs
// deactivation-time cleanup, e.g. clearing scheduled cron events).
