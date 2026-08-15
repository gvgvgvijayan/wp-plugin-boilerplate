/**
 * Sample block — index entry.
 *
 * Registers the block type from block.json and wires up the Edit and Save
 * components. This is the pattern wp-scripts discovers via block.json.
 */
import { registerBlockType } from '@wordpress/blocks';

import edit from './edit';
import save from './save';

import metadata from './block.json';
import './index.scss';

registerBlockType( metadata.name, {
	...metadata,
	edit,
	save,
} );
