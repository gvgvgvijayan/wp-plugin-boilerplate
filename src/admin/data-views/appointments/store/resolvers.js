/**
 * Data Views store resolvers.
 *
 * Resolvers are keyed by the selector name they populate, so this file
 * exports a resolver named `getRecords` (matching `selectors.js`). WordPress
 * automatically calls it the first time `getRecords()` is selected.
 *
 * Uses `@wordpress/api-fetch` which attaches the nonce automatically.
 * Replace the endpoint with your plugin's real REST route.
 */
import apiFetch from '@wordpress/api-fetch';

import { fetchRecords, receiveRecords, receiveRecordsError } from './actions';
import { normalizeRecords } from './utils';

/**
 * Resolve records from the REST API.
 *
 * Written as a generator so the `fetchRecords` action is dispatched (via
 * `yield`) before the request starts — this flips `isResolving` to true so
 * the UI can show a loading indicator. The resolved data is then dispatched
 * via `receiveRecords`.
 */
export function* getRecords() {
	yield fetchRecords();
	try {
		const records = yield apiFetch( { path: '/wp/v2/example' } );
		yield receiveRecords( normalizeRecords( records ) );
	} catch ( error ) {
		yield receiveRecordsError( error );
	}
}
