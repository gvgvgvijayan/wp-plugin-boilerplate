<?php
/**
 * Sample unit test.
 *
 * Demonstrates the test structure. This test does not require a live
 * WordPress install; it exercises the bootstrap path only. Replace with real
 * tests for your plugin classes.
 *
 * @package VG\Plugin_Boilerplate
 */

namespace VG\Plugin_Boilerplate\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class SampleTest
 */
class SampleTest extends TestCase {

	/**
	 * Ensure the Composer autoloader loaded the plugin namespace.
	 *
	 * @return void
	 */
	public function test_plugin_namespace_autoloads() {
		$this->assertTrue( class_exists( '\VG\Plugin_Boilerplate\Loader' ) );
	}
}
