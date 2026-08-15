/**
 * Data Views store types.
 *
 * Central type/action-name definitions for the admin appointments data store.
 */

/**
 * Action type: fetch a page of records.
 *
 * @type {string}
 */
export const FETCH_RECORDS = 'FETCH_RECORDS';

/**
 * Action type: a fetch succeeded.
 *
 * @type {string}
 */
export const FETCH_RECORDS_SUCCESS = 'FETCH_RECORDS_SUCCESS';

/**
 * Action type: a fetch failed.
 *
 * @type {string}
 */
export const FETCH_RECORDS_ERROR = 'FETCH_RECORDS_ERROR';

/**
 * Initial store state.
 *
 * @type {Object}
 */
export const DEFAULT_STATE = {
	records: [],
	isResolving: false,
	hasResolved: false,
	error: null,
};
