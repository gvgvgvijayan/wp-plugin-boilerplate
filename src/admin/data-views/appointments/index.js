/**
 * Appointments admin entry point.
 *
 * This is registered as a custom webpack entry (see webpack.config.js) and
 * enqueued on the plugin's admin page. It boots the Data Views store and
 * mounts the appointments list.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-dom-ready/
 */
import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

import './store';
import AppointmentsList from './components/appointments-list';
import './components/style.scss';

domReady( () => {
	const mountPoint = document.getElementById(
		'vg-plugin-boilerplate-appointments'
	);

	if ( ! mountPoint ) {
		return;
	}

	const root = createRoot( mountPoint );
	root.render( <AppointmentsList /> );
} );
