<?php
/**
 * Plugin Loader.
 *
 * @package VG\Plugin_Boilerplate
 */

namespace VG\Plugin_Boilerplate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Loader
 *
 * Minimal bootstrap for the DI container.
 */
class Loader {

	/**
	 * DI Container instance.
	 *
	 * @var mixed|null
	 */
	private $container;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->build_container();
	}

	/**
	 * Builds the PHP-DI container using the scoped ThirdParty namespace.
	 */
	private function build_container() {
		$builder_class = '\VG\Plugin_Boilerplate\ThirdParty\DI\ContainerBuilder';

		if ( ! class_exists( $builder_class ) ) {
			return;
		}

		try {
			$builder = new $builder_class();
			$this->container = $builder->build();
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'DI Container Error: ' . $e->getMessage() );
			}
		}
	}

	/**
	 * Kick off the plugin logic.
	 */
	public function run() {
		if ( ! $this->container ) {
			return;
		}

		// Your hook registration logic starts here in your specific implementation.
	}
}
