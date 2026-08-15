/**
 * Sample block — edit component.
 *
 * Renders the block in the editor. Uses RichText so the user can type
 * directly into the paragraph.
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

/**
 * Edit component.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Update attributes callback.
 * @return {import('react').JSX.Element} The edit UI.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();

	return (
		<RichText
			{ ...blockProps }
			tagName="p"
			value={ attributes.content }
			onChange={ ( content ) => setAttributes( { content } ) }
			placeholder={ __( 'Type something…', 'vg-plugin-boilerplate' ) }
		/>
	);
}
