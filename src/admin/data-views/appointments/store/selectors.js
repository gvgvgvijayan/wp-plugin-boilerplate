/**
 * Data Views store selectors.
 */
/**
 * Return the records from the store.
 *
 * @param {Object} state Current state.
 * @return {Array} Records.
 */
export const getRecords = ( state ) => state.records;

/**
 * Whether a fetch is in progress.
 *
 * @param {Object} state Current state.
 * @return {boolean} True if resolving.
 */
export const isResolving = ( state ) => state.isResolving;

/**
 * Whether a fetch has completed at least once.
 *
 * @param {Object} state Current state.
 * @return {boolean} True if resolved.
 */
export const hasResolved = ( state ) => state.hasResolved;

/**
 * Return the current error, if any.
 *
 * @param {Object} state Current state.
 * @return {Object|null} The error.
 */
export const getError = ( state ) => state.error;
