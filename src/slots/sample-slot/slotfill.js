/**
 * Sample slotfill component.
 *
 * Renders a panel inside the block editor's SettingsSidebar via a slotfill.
 * Replace with your plugin's actual UI.
 */
import { __ } from '@wordpress/i18n';
import { PluginSidebar } from '@wordpress/edit-post';
import { PanelBody } from '@wordpress/components';

/**
 * Sample slotfill.
 *
 * @return {import('react').JSX.Element} The rendered panel.
 */
export default function SampleSlotfill() {
	return (
		<PluginSidebar
			name="vg-plugin-boilerplate-sample-slot"
			title={ __( 'Sample Panel', 'vg-plugin-boilerplate' ) }
		>
			<PanelBody>
				<p>
					{ __(
						'Edit this sample slot to build your own.',
						'vg-plugin-boilerplate'
					) }
				</p>
			</PanelBody>
		</PluginSidebar>
	);
}
