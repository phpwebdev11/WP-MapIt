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
		},
		edit( props ) {
			const { attributes, setAttributes } = props;

			const [ contentResponse, setPost ] = UseState( null );

			/* Get multipin map data through api */
			UseEffect( () => {
				apiFetch( {
					path: 'wp/v2/wp_mapit_map',
					method: 'POST',
					data: {
						wp_mapit_map: attributes.wp_mapit_map,
					},
				} ).then(
					( response ) => {
						setPost( response );
					},
				);
			}, [ props ] );

			const postList = [
				{ value: '', label: __( 'Select', 'wp-mapit' ) },
			];

			/* Get posts for selection */
			const postsArr = useSelect( ( select ) => {
			    return select('core').getEntityRecords( 'postType', 'wp_mapit_map', { per_page: -1 } );
			}, [] );
			if ( postsArr ) {
				postsArr.forEach( ( post ) => {
					if ( '' !== post.name ) {
						postList.push(
							{ value: post.id, label: post.title.rendered },
						);
					}
				} );
			}

			return (
				el( 'div',
					UseBlockProps( {
						className: 'wp-mapit-block-admin',
					} ),
					el( RawHTML, {},
						contentResponse || '<div>' + __( 'Please select map post.', 'wp-mapit' ) + '</div>'
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
