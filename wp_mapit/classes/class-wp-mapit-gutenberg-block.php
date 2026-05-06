<?php
/**
 * Gutenberg block.
 *
 * @package wp-mapit
 */

namespace WpMapit\Classes;

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Gutenberg_Block' ) ) {

	/**
	 * Class to manage the gutenberg block for WP MAPIT
	 */
	class Wp_Mapit_Gutenberg_Block {
		/**
		 * Add hooks and filters for the gutenberg block
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			add_action(
				'init',
				array(
					__CLASS__,
					'init_block',
				),
			);
		}

		/**
		 * Hook to handle the init action to initialize the block
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init_block() {

			$render_blocks = array(
				'wp-mapit-map-block' => array(
					'callback' => array( Wp_Mapit_Blocks::class, 'render_wp_mapit' ),
				),
			);

			if ( is_array( $render_blocks ) && count( $render_blocks ) > 0 ) {
				foreach ( $render_blocks as $block => $block_info ) {
					wp_register_script(
						'wpmi-' . $block . '-editor-script-js',
						WP_MAPIT_URL . 'blocks/' . $block . '/index.js',
						array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor' ),
						filemtime( WP_MAPIT_DIR . 'blocks/' . $block . '/index.js' ),
						false
					);

					wp_register_style(
						'wpmi-' . $block . '-editor-style-css',
						WP_MAPIT_URL . 'blocks/' . $block . '/index.css',
						null,
						filemtime( WP_MAPIT_DIR . 'blocks/' . $block . '/index.css' ),
						false
					);

					wp_register_script(
						'wpmi-' . $block . '-script-js',
						WP_MAPIT_URL . 'blocks/' . $block . '/script.js',
						array( 'jquery' ),
						filemtime( WP_MAPIT_DIR . 'blocks/' . $block . '/script.js' ),
						false
					);

					wp_register_style(
						'wpmi-' . $block . '-style-css',
						WP_MAPIT_URL . 'blocks/' . $block . '/style-index.css',
						null,
						filemtime( WP_MAPIT_DIR . 'blocks/' . $block . '/style-index.css' ),
						false
					);

					register_block_type_from_metadata(
						WP_MAPIT_DIR . 'blocks/' . $block . '/',
						array(
							'render_callback' => $block_info['callback'],
						)
					);
				}
			}
		}
	}
}
