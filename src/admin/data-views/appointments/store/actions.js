/**
 * Data Views store actions.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-data/
 */
import {
	FETCH_RECORDS,
	FETCH_RECORDS_SUCCESS,
	FETCH_RECORDS_ERROR,
} from './types';

/**
 * Request a page of records.
 *
 * Dispatched when a fetch starts so the store can reflect a loading state.
 *
 * @return {Object} Action object.
 */
export function fetchRecords() {
	return { type: FETCH_RECORDS };
}

/**
 * Records loaded successfully.
 *
 * @param {Array} records The resolved records.
 * @return {Object} Action object.
 */
export function receiveRecords( records ) {
	return { type: FETCH_RECORDS_SUCCESS, records };
}

/**
 * A fetch failed.
 *
 * @param {Object} error The error.
 * @return {Object} Action object.
 */
export function receiveRecordsError( error ) {
	return { type: FETCH_RECORDS_ERROR, error };
}
