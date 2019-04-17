<?php

	if( ! class_exists( 'wp_mapit_widget' ) ) {

		/**
		 * Class to manage widget for WP MAPIT
		 */
		class wp_mapit_widget
		{
			
			/**
		     * Add hooks and filters for the widgets
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function init() {
				add_action( 'widgets_init', __CLASS__ . '::widgets_init' );
			}

			/**
		     * Hook to handle the widget_init action
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function widgets_init() {
				register_widget( 'wp_mapit_contextual_map_widget' );
			}
		}

		/**
		 * Calling init function to activate hooks and filters.
		 */
		wp_mapit_widget::init();
	}