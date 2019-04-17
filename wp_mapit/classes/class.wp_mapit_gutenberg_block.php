<?php

	if( ! class_exists( 'wp_mapit_gutenberg_block' ) ) {

		/**
		 * Class to manage the gutenberg block for WP MAPIT
		 */
		class wp_mapit_gutenberg_block
		{
			/**
		     * Add hooks and filters for the gutenberg block
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function init()
			{
				add_action( 'init', __CLASS__ . '::init_block' );
			}

			/**
		     * Hook to handle the init action to initialize the block
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function init_block() {

				if( ! function_exists( 'register_block_type' ) ) {
					/* Guternberg is not active */
					return;
				}

				wp_register_script( 'wp-mapit-gutenberg-js', WP_MAPIT_URL . 'js/wp_mapit_gutenberg.js', array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ), filemtime( WP_MAPIT_DIR . 'js/wp_mapit_gutenberg.js' ) );

				wp_localize_script( 'wp-mapit-gutenberg-js', 'wp_mapit_gutenberg', array( 
					'logo' => WP_MAPIT_URL . 'images/logo.jpg',	
				) );

				register_block_type( WP_MAPIT_TEXTDOMAIN . '/wp-mapit-gutenberg-map-block', array( 'editor_script' => 'wp-mapit-gutenberg-js' ) );
			}

		}

		/**
		 * Calling init function to activate hooks and filters.
		 */
		wp_mapit_gutenberg_block::init();

	}