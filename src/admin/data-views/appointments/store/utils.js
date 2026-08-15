/**
 * Data Views store utilities.
 */

/**
 * Normalize a list of records into the shape the UI expects.
 *
 * @param {Array} rawRecords Raw records from the API.
 * @return {Array} Normalized records.
 */
export function normalizeRecords( rawRecords = [] ) {
	return rawRecords.map( ( record ) => ( {
		...record,
		displayTitle: record.title?.rendered ?? record.title ?? '',
	} ) );
}
