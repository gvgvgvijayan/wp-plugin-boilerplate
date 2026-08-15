/**
 * Data Views store reducer.
 */
import {
	DEFAULT_STATE,
	FETCH_RECORDS,
	FETCH_RECORDS_SUCCESS,
	FETCH_RECORDS_ERROR,
} from './types';

/**
 * Root reducer for the appointments store.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 * @return {Object} Next state.
 */
export default function reducer( state = DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case FETCH_RECORDS:
			return { ...state, isResolving: true, error: null };

		case FETCH_RECORDS_SUCCESS:
			return {
				...state,
				records: action.records,
				isResolving: false,
				hasResolved: true,
			};

		case FETCH_RECORDS_ERROR:
			return {
				...state,
				isResolving: false,
				hasResolved: true,
				error: action.error,
			};

		default:
			return state;
	}
}
