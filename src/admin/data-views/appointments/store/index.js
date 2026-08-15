/**
 * Data Views store registration.
 */
import { createReduxStore, register } from '@wordpress/data';

import * as actions from './actions';
import * as selectors from './selectors';
import * as resolvers from './resolvers';
import reducer from './reducer';

/**
 * The appointments data store name.
 *
 * @type {string}
 */
export const STORE_NAME = 'vg-plugin-boilerplate/appointments';

const storeConfig = {
	reducer,
	actions,
	selectors,
	resolvers,
};

const store = createReduxStore( STORE_NAME, storeConfig );
register( store );
