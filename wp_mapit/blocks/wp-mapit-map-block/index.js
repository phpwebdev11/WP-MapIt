( function( blocks, i18n, element ) {
	const __ = i18n.__;
	const el = element.createElement;
	const { useBlockProps, InspectorControls } = window.wp.blockEditor;
	const { PanelBody, PanelRow, SelectControl } = window.wp.components;
	const { useEffect, useState, RawHTML } = wp.element;
	const { useSelect } = wp.data;
	const apiFetch = wp.apiFetch;
	const UseBlockProps = useBlockProps;
	const UseState = useState;
	const UseEffect = useEffect;

	/* Style for the block displayed in the editor after block selected */
	var blockStyle = {
		margin: '0 auto',
		width: '100px',
		height: 'auto',
		display: 'block'
	};

	/* Registering the block */
	blocks.registerBlockType( 'wp-mapit/wp-mapit-map-block', {
		attributes: {
			wp_mapit_map: {
				type: 'string',
			},
			tags: {
				type: "array",
				default: []
			}
		},
		edit( props ) {
			const { attributes, setAttributes } = props;

			const [ editorContent, setEditorContent ] = UseState( null );
			const [ tags, setTags ] = UseState( null );

			// Get multipin map data through api.
			UseEffect( () => {
				// Get response for admin editor.
				apiFetch( {
					path: 'wp/v2/wp_mapit_map',
					method: 'POST',
					data: {
						wp_mapit_map: attributes.wp_mapit_map,
					},
				} ).then(
					( response ) => {
						setEditorContent( response );
					},
				);

				// Get tags for the selected map.
				apiFetch( {
					path: 'wp/v2/get_tags_by_post',
					method: 'POST',
					data: {
						map_post: attributes.wp_mapit_map,
					},
				} ).then(
					( response ) => {
						response = JSON.parse( response );
						setTags( response );
					},
				);

				// Load Select2 and initialize it on the tag select element after a delay to ensure the element is rendered.
				setTimeout( () => {
						const tagSelect = jQuery( '.wp-mapit-select2 select' );

						if ( tagSelect.length ) {
							// Initialize Select2 on the tag select element.
							tagSelect.select2( {
								placeholder: __( 'Select Tags', 'wp-mapit' ),
							} );

							// On change event for the tag selection to update block attributes.
							tagSelect.on( 'change', function () {
								setAttributes( {
									tags: jQuery( this ).val() || [],
								} );
							} );
						}
					}, 4000 );
			}, [] );

			const postList = [
				{ value: '', label: __( 'Select', 'wp-mapit' ) },
			];

			/* Get posts for selection */
			const postsArr = useSelect( ( select ) => {
			    return select( 'core' ).getEntityRecords( 'postType', 'wp_mapit_map', { per_page: -1 } );
			}, [] );

			// Map posts options.
			if ( postsArr ) {
				postsArr.forEach( ( post ) => {
					if ( '' !== post.name ) {
						postList.push(
							{ value: post.id, label: post.title.rendered },
						);
					}
				} );
			}

			// Get tags for selection.
			/* const tags = useSelect( ( select ) => {
				return select( 'core' ).getEntityRecords( 'taxonomy', 'wp_mapit_tag', { per_page: -1, } );
			}, [] ); */
			
			// Map tags options.
			const tagOptions = Array.isArray( tags ) ? tags.map( ( tag ) => ( { label: tag.name, value: tag.id, } ) ) : [];

			return (
				el( 'div',
					UseBlockProps( {
						className: 'wp-mapit-block-admin',
					} ),
					el( RawHTML, {},
						editorContent || '<div>' + __( 'Please select map post.', 'wp-mapit' ) + '</div>'
					),
					el( InspectorControls, {},
						el( PanelBody,
							{
								title: __( 'Multipin Map Settings', 'wp-mapit' ),
								initialOpen: true,
							},
							el( SelectControl,
								{
									label: __( 'Posts', 'wp-mapit' ),
									value: attributes.wp_mapit_map,
									options: postList,
									onChange( value ) {
										setAttributes( { wp_mapit_map: value } );
									},
								},
							),
							el( SelectControl,
								{
									label: __( 'Tags', 'wp-mapit' ),
									value: attributes.tags,
									options: tagOptions,
									className: 'wp-mapit-select2',
									multiple: true,
									onChange() {},
								},
							),
						),
					),
				)
			);
		},
		save: function() {
			return null;
		},
	} );
}(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element
) );
