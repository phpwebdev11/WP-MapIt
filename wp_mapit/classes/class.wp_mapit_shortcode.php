<?php
	
	if( ! class_exists( 'wp_mapit_shortcode' ) ) {
		/**
		 * Class to manage the shortcodes of the plugins
		 */
		class wp_mapit_shortcode
		{
			/**
		     * Add hooks and filters for shortcode
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function init()
			{
				/* Shortcode to display current page map */
				add_shortcode( 'wp_mapit', __CLASS__ . '::wp_mapit' );
			}

			/**
		     * Hook to handle the shortcode wp_mapit
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return string Returns Map markup as string
		     */
			public static function wp_mapit() {
				if ( wp_mapit_map::has_map() ){
					return wp_mapit_map::generate_map();
				}
			}
		}

		/**
		 * Calling init function to activate hooks and filters.
		 */
		wp_mapit_shortcode::init();

	}