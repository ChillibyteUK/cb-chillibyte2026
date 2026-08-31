import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, intro, vimeoId, size } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-chillibyte-2026-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } classPrefix="cb-chillibyte-2026" textDomain="cb-chillibyte-2026" title="CB Vimeo">
			<TextareaControl
				label={ __( 'Title', 'cb-chillibyte-2026' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
			/>
			<TextareaControl
				label={ __( 'Intro', 'cb-chillibyte-2026' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '50 1 0%' } }>
			<TextControl
				label={ __( 'Vimeo ID', 'cb-chillibyte-2026' ) }
				value={ vimeoId }
				onChange={ ( value ) => setAttributes( { vimeoId: value } ) }
			/>
				</div>
				<div style={ { flex: '50 1 0%' } }>
			<SelectControl
				label={ __( 'Size', 'cb-chillibyte-2026' ) }
				value={ size }
				options={ [
					{ label: 'Full-width', value: 'Full-width' },
					{ label: 'Container', value: 'Container' },
				] }
				onChange={ ( value ) => setAttributes( { size: value } ) }
			/>
				</div>
			</div>
		</EditorBlockShell>
	);
}
