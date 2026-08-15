/**
 * Data Views store resolvers.
 *
 * Resolvers are keyed by the selector name they populate, so this file
 * exports a resolver named `getRecords` (matching `selectors.js`). WordPress
 * automatically calls it the first time `getRecords()` is selected.
 *
 * Replace the endpoint with your plugin's real REST route and prefer
 * `@wordpress/api-fetch` (which attaches the nonce automatically).
 */
import { receiveRecords, receiveRecordsError } from './actions';
import { normalizeRecords } from './utils';

/**
 * Resolve a page of records from the REST API.
 *
 * @param {Object} query Query args (page, search, etc.).
 * @return {Promise} Resolves to an action when the fetch completes.
 */
export async function getRecords( query = {} ) {
	try {
		const params = new URLSearchParams( query ).toString();
		const url = `/wp-json/wp/v2/example${ params ? `?${ params }` : '' }`;

		const response = await fetch( url, {
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': window?.wpApiSettings?.nonce ?? '',
			},
		} );

		if ( ! response.ok ) {
			throw new Error( `Request failed: ${ response.status }` );
		}

		return receiveRecords( normalizeRecords( await response.json() ) );
	} catch ( error ) {
		return receiveRecordsError( error );
	}
}
