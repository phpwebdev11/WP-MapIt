<?php
/**
 * Widget class.
 *
 * @package wp-mapit
 */

namespace WpMapit\Classes;

use WpMapit\Classes\Wp_Mapit_Contextual_Map_Widget;

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
			require_once ABSPATH . 'wp-includes/class-wp-widget.php';

			add_action(
				'widgets_init',
				array(
					__CLASS__,
					'widgets_init',
				),
			);
		}

		/**
		 * Hook to handle the widget_init action
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function widgets_init() {
			register_widget( Wp_Mapit_Contextual_Map_Widget::class );
		}
	}
}
