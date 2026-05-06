<?php
/**
 * Manage shortcode.
 *
 * @package wp-mapit
 */

namespace WpMapit\Classes;

use WpMapit\Classes\Wp_Mapit_Multipin_Map;
use WpMapit\Classes\Wp_Mapit_Map;

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Shortcode' ) ) {
	/**
	 * Class to manage the shortcodes of the plugins
	 */
	class Wp_Mapit_Shortcode {
		/**
		 * Add hooks and filters for shortcode
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			/* Shortcode to display current page map */
			add_shortcode(
				'wp_mapit',
				array(
					__CLASS__,
					'wp_mapit',
				),
			);

			/* Shortcode to display map from map module (multipin map) */
			add_shortcode(
				'wp_mapit_map',
				array(
					__CLASS__,
					'wp_mapit_map',
				),
			);
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
			if ( wp_mapit_map::has_map() ) {
				return wp_mapit_map::generate_map();
			}
		}

		/**
		 * Hook to handle the shortcode wp_mapit_map
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Array $atts attrubutes of the shortcode.
		 * @return string Returns Map markup as string
		 */
		public static function wp_mapit_map( $atts ) {

			$map_id = isset( $atts['id'] ) ? intval( $atts['id'] ) : 0;

			if ( $map_id > 0 ) {
				if ( Wp_Mapit_Multipin_Map::has_map( $map_id ) ) {
					return Wp_Mapit_Multipin_Map::generate_map( $map_id );
				}
			}
		}
	}
}
