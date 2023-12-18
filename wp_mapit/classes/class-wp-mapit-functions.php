<?php
/**
 * Map IT Funtions.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Functions' ) ) {
	/**
	 * CLass to manage common functions of the plugin
	 */
	class Wp_Mapit_Functions {
		/**
		 * Function to generate a random string
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $length Length of the string.
		 * @return String Returns a random string
		 */
		public static function generate_random_string( $length = 10 ) {
			$x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			return substr( str_shuffle( str_repeat( $x, ceil( $length / strlen( $x ) ) ) ), 1, $length );
		}
	}
}
