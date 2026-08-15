/**
 * Appointments list component (Data Views).
 *
 * Demonstrates how to render a @wordpress/dataviews DataViews component
 * backed by the plugin data store. Replace with real components for your
 * plugin's entities.
 */
import { useSelect } from '@wordpress/data';
import { DataViews } from '@wordpress/dataviews';

import { STORE_NAME } from '../store';
import { fields, defaultView } from './fields';

/**
 * Appointments list.
 *
 * @return {import('react').JSX.Element} The rendered list.
 */
export default function AppointmentsList() {
	const { records, isResolving, hasResolved } = useSelect( ( select ) => {
		const store = select( STORE_NAME );
		return {
			records: store.getRecords(),
			isResolving: store.isResolving(),
			hasResolved: store.hasResolved(),
		};
	}, [] );

	const paginationInfo = {
		totalItems: records?.length ?? 0,
		totalPages: 1,
	};

	return (
		<div className="admin-appointments">
			<DataViews
				fields={ fields }
				view={ defaultView }
				data={ records ?? [] }
				getItemId={ ( record ) => record.id }
				isLoading={ isResolving && ! hasResolved }
				paginationInfo={ paginationInfo }
				onChangeView={ () => {} }
			/>
		</div>
	);
}
