import { test, expect } from '@playwright/test';

/**
 * Sample E2E test.
 *
 * Demonstrates the E2E structure. The inherited wp-scripts global setup
 * authenticates each test context, so this asserts on the authenticated
 * wp-admin shell. Replace with real flows for your plugin.
 */
test( 'WP admin dashboard is reachable', async ( { page } ) => {
	await page.goto( '/wp-admin/' );

	await expect( page.locator( 'body.wp-admin' ) ).toBeVisible();
} );
