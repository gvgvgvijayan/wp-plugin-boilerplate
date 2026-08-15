/**
 * Appointments field definitions for the Data Views component.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-dataviews/
 */
import { __ } from '@wordpress/i18n';

/**
 * The field configuration for the appointments list.
 *
 * @type {Array<Object>}
 */
export const fields = [
	{
		id: 'displayTitle',
		label: __( 'Title', 'vg-plugin-boilerplate' ),
	},
	{
		id: 'status',
		label: __( 'Status', 'vg-plugin-boilerplate' ),
	},
];

/**
 * The default view configuration.
 *
 * @type {Object}
 */
export const defaultView = {
	type: 'table',
	fields: [ 'displayTitle', 'status' ],
	perPage: 10,
};
