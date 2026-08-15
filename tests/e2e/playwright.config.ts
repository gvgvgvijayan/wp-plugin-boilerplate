import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright E2E configuration.
 *
 * Extends the @wordpress/scripts default Playwright config so it works with
 * the plugin's wp-scripts tooling, then runs against an existing environment
 * managed by wp-env (no embedded web server).
 */
const config = defineConfig( {
	// Extend the default configuration from @wordpress/scripts.
	...require( '@wordpress/scripts/config/playwright.config' ),

	// Point Playwright at the local specs directory.
	testDir: './specs',

	use: {
		...require( '@wordpress/scripts/config/playwright.config' ).use,
	},

	/* Configure projects for major browsers */
	projects: [
		{
			name: 'chromium',
			use: { ...devices[ 'Desktop Chrome' ] },
		},
	],

	/* Disable webServer since we test against an existing site managed by wp-env */
	webServer: undefined,
} );

export default config;
