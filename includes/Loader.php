<?php
/**
 * Plugin Loader.
 *
 * Central bootstrap that wires up the plugin's services and registers
 * WordPress hooks.
 *
 * @package VG\Plugin_Boilerplate
 */

namespace VG\Plugin_Boilerplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use VG\Plugin_Boilerplate\DB\Installer;
use VG\Plugin_Boilerplate\ThirdParty\Psr\Container\ContainerInterface;

/**
 * Class Loader
 *
 * ---------------------------------------------------------------------------
 * HOW MUCH SHOULD THIS CLASS DO?
 * ---------------------------------------------------------------------------
 * This is intentionally the most flexible file in the plugin. It can be:
 *
 *   A) A single `init()` that directly instantiates services (no container):
 *
 *        public function init() {
 *            add_action( 'admin_init', array( new Installer(), 'maybe_update' ) );
 *            add_action( 'init', array( $this, 'register_blocks' ) );
 *            add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
 *        }
 *
 *   B) A wider, container-driven bootstrap (the one shipped below) that
 *      resolves each service from the DI container and registers hooks.
 *
 * Which one you pick is a judgement call, not a class-count rule:
 *   - If your services are few and wiring is trivial, prefer (A) — it is
 *     simpler and more transparent to read and debug.
 *   - If the service graph is large, interdependent, or likely to grow, the
 *     container (B) pays off. PHP-DI's compiled-container mode removes the
 *     reflection/autowiring overhead in production, so this is a
 *     maintainability choice, NOT a performance one.
 *
 * Whichever you choose, keep the public entry point consistent: the main
 * plugin file calls `$loader->init()`. If you use the simple form, you can
 * remove the container plumbing below.
 */
class Loader {

	/**
	 * Dependency Injection Container.
	 *
	 * @since 0.1.0
	 * @var ContainerInterface|null The DI container instance.
	 */
	private $container;

	/**
	 * Constructor.
	 *
	 * Builds the DI container.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->build_container();
	}

	/**
	 * Build the PHP-DI container using the scoped ContainerBuilder.
	 *
	 * The container classes live under the scoped `ThirdParty` namespace so
	 * they never collide with another copy of PHP-DI loaded by core or another
	 * plugin. If the scoped builder is missing (e.g. php-scoper/composer were
	 * never run), we degrade gracefully instead of fatally.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function build_container() {
		$scoped_builder_class = '\VG\Plugin_Boilerplate\ThirdParty\DI\ContainerBuilder';

		if ( ! class_exists( $scoped_builder_class ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Loader: scoped DI\ContainerBuilder not found. Run composer install + php-scoper.' );
			}
			$this->container = null;
			return;
		}

		try {
			// @phpstan-ignore-next-line
			$builder         = new $scoped_builder_class();
			$this->container = $builder->build();
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Loader: DI container build failed: ' . $e->getMessage() );
			}
			$this->container = null;
		}
	}

	/**
	 * Initialize the plugin.
	 *
	 * Called by the main plugin file. Registers all hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		$this->register_hooks();
	}

	/**
	 * Register WordPress hooks for the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function register_hooks() {
		// Database installer/updater.
		add_action( 'admin_init', array( $this, 'maybe_update_db' ) );

		// Blocks and REST routes.
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Admin page for the Data Views skeleton.
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Block editor assets (block styles + slotfills).
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );

		// Register any services that expose hooks.
		$this->initialize_services();
	}

	/**
	 * Run the database updater (table creation / migrations).
	 *
	 * Wired to `admin_init` so updates run on any admin request.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function maybe_update_db() {
		try {
			if ( $this->container ) {
				$installer = $this->container->get( Installer::class );
			} elseif ( class_exists( Installer::class ) ) {
				// Fallback for when the container is unavailable but the
				// Installer exists (e.g. the simple direct-invocation path).
				$installer = new Installer();
			} else {
				return; // Installer not yet implemented — nothing to update.
			}
			$installer->maybe_update();
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log( 'Loader: DB update failed: ' . $e->getMessage() );
			}
		}
	}

	/**
	 * Register custom block types.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_blocks() {
		/*
		 * Blocks are discovered from block.json via wp-scripts. Register each
		 * block by pointing to its build directory. Add more as you create
		 * them, e.g.:
		 *
		 * register_block_type( __DIR__ . '/../build/blocks/another-block' );
		 */
		$sample_block = VG_PLUGIN_BOILERPLATE_PLUGIN_DIR . 'build/blocks/sample-block';

		if ( file_exists( $sample_block . '/block.json' ) ) {
			register_block_type( $sample_block );
		}
	}

	/**
	 * Register REST API routes.
	 *
	 * Resolve each controller from the container and call register_routes().
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		if ( ! $this->container ) {
			return;
		}

		/*
		 * Example — replace with your own controllers:
		 *
		 * try {
		 *     $this->container->get( Some_Controller::class )->register_routes();
		 * } catch ( \Throwable $e ) {
		 *     if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		 *         error_log( 'Loader: REST route registration failed: ' . $e->getMessage() );
		 *     }
		 * }
		 */
	}

	/**
	 * Register the admin page that hosts the Data Views skeleton.
	 *
	 * Adds a top-level menu item. Adjust capability and position per project.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_admin_page() {
		add_menu_page(
			__( 'Appointments', 'vg-plugin-boilerplate' ),
			__( 'Appointments', 'vg-plugin-boilerplate' ),
			'manage_options',
			'vg-plugin-boilerplate-appointments',
			array( $this, 'render_admin_page' ),
			'dashicons-calendar-alt',
			26
		);
	}

	/**
	 * Render the admin page container that the React app mounts to.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function render_admin_page() {
		echo '<div id="vg-plugin-boilerplate-appointments" class="wrap"></div>';
	}

	/**
	 * Enqueue admin-only assets (Data Views skeleton).
	 *
	 * Only loads on the plugin's own admin page to avoid polluting other screens.
	 *
	 * @since 0.1.0
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Only enqueue on our own admin page.
		if ( 'toplevel_page_vg-plugin-boilerplate-appointments' !== $hook_suffix ) {
			return;
		}

		$asset_file = VG_PLUGIN_BOILERPLATE_PLUGIN_DIR . 'build/admin-appointments.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = include $asset_file;

		wp_enqueue_script(
			'vg-plugin-boilerplate-admin-appointments',
			VG_PLUGIN_BOILERPLATE_PLUGIN_URL . 'build/admin-appointments.js',
			$asset['dependencies'] ?? array(),
			$asset['version'] ?? VG_PLUGIN_BOILERPLATE_VERSION,
			true
		);

		wp_enqueue_style(
			'vg-plugin-boilerplate-admin-appointments',
			VG_PLUGIN_BOILERPLATE_PLUGIN_URL . 'build/style-admin-appointments.css',
			array(),
			$asset['version'] ?? VG_PLUGIN_BOILERPLATE_VERSION
		);
	}

	/**
	 * Enqueue block editor assets (block styles + slotfills).
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		$handle     = 'vg-plugin-boilerplate-block-styles';
		$asset_file = VG_PLUGIN_BOILERPLATE_PLUGIN_DIR . 'build/block-styles.asset.php';

		if ( file_exists( $asset_file ) ) {
			$asset = include $asset_file;

			wp_enqueue_script(
				$handle,
				VG_PLUGIN_BOILERPLATE_PLUGIN_URL . 'build/block-styles.js',
				$asset['dependencies'] ?? array(),
				$asset['version'] ?? VG_PLUGIN_BOILERPLATE_VERSION,
				true
			);
		}

		$slot_handle     = 'vg-plugin-boilerplate-sample-slot';
		$slot_asset_file = VG_PLUGIN_BOILERPLATE_PLUGIN_DIR . 'build/sample-slot.asset.php';

		if ( file_exists( $slot_asset_file ) ) {
			$slot_asset = include $slot_asset_file;

			wp_enqueue_script(
				$slot_handle,
				VG_PLUGIN_BOILERPLATE_PLUGIN_URL . 'build/sample-slot.js',
				$slot_asset['dependencies'] ?? array(),
				$slot_asset['version'] ?? VG_PLUGIN_BOILERPLATE_VERSION,
				true
			);
		}
	}

	/**
	 * Initialize services that register their own hooks.
	 *
	 * Each resolved service is expected to expose a register()/register_hooks()
	 * method that attaches its own WordPress hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function initialize_services() {
		if ( ! $this->container ) {
			return;
		}

		/*
		 * Example — replace with your services:
		 *
		 * try {
		 *     $this->container->get( Some_Service::class )->register();
		 * } catch ( \Throwable $e ) {
		 *     if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		 *         error_log( 'Loader: service initialization failed: ' . $e->getMessage() );
		 *     }
		 * }
		 */
	}
}
