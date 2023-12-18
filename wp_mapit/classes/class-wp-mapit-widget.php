<?php
/**
 * Widget class.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Widget' ) ) {

	/**
	 * Class to manage widget for WP MAPIT
	 */
	class Wp_Mapit_Widget {
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
	Wp_Mapit_Widget::init();
}
