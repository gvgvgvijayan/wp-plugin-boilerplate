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
 * Resolve a page of records from the REST API.
 *
 * @param {Object} query Query args (page, search, etc.).
 * @return {Promise} Resolves to an action when the fetch completes.
 */
export async function getRecords( query = {} ) {
	try {
		// Flag the request as in-flight so loading state reflects it.
		fetchRecords();

		const records = await apiFetch( { path: '/wp/v2/example', query } );

		return receiveRecords( normalizeRecords( records ) );
	} catch ( error ) {
		return receiveRecordsError( error );
	}
}
