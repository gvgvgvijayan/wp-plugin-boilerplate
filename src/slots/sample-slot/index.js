/**
 * Sample slot entry point.
 *
 * A "slot" is a custom webpack entry that is NOT a block and NOT a block
 * style. Common uses:
 *   - Gutenberg slotfills / plugin extensions (@wordpress/plugins)
 *   - Editor settings panels
 *   - Admin scripts for a custom page
 *
 * Register this entry in webpack.config.js (e.g. `'sample-slot'`) and enqueue
 * the built asset from PHP with the right context.
 */
import { registerPlugin } from '@wordpress/plugins';

import SampleSlotfill from './slotfill';

registerPlugin( 'vg-plugin-boilerplate-sample-slot', {
	icon: 'admin-generic',
	render: SampleSlotfill,
	// Show in the editor's more-menu / plugin area. Adjust per use case.
	scope: 'general',
} );
