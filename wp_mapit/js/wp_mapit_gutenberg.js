( function( blocks, i18n, element ) {
	var el = element.createElement;
	var __ = i18n.__;

	/* Style for the block displayed in the editor after block selected */
	var blockStyle = {
		margin: '0 auto',
		width: '100px',
		height: 'auto',
		display: 'block'
	};

	/* Registering the block */
	blocks.registerBlockType( 'wp-mapit/wp-mapit-gutenberg-map-block', {
		title: __( 'WP MAPIT', 'wp-mapit' ),
		icon: 'location-alt',
		category: 'embed',
		edit: function() {
			return el(
				'img',
				{ style: blockStyle, src: wp_mapit_gutenberg.logo }
			);
		},
		save: function() {
			return el(
				'p',
				{},
				'[wp_mapit]'
			);
		},
	} );
}(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element
) );