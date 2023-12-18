<?php
/**
 * Manage content filter.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_The_Content' ) ) {
	/**
	 * Class to filter the_content to show map
	 */
	class Wp_Mapit_The_Content {
		/**
		 * Add hooks and filters to display map in the content
		 *
		 * @since 1.0
		 * @access public
		 */
		public static function init() {
			add_filter( 'the_content', __CLASS__ . '::the_content' );
		}

		/**
		 * Function to manage the_content filter to display map
		 *
		 * @since 1.0
		 * @access public
		 * @param String $content content of the page.
		 * @return String Returns content including the map as string
		 */
		public static function the_content( $content ) {

			/* Check if map is added */
			if ( wp_mapit_map::has_map() ) {

				$map_position = wp_mapit_map::get_map_position();

				/* If map is to be displayed before or after content, generate map */
				if ( in_array( $map_position, array( 'before', 'after' ), true ) ) {
					$map_content = wp_mapit_map::generate_map();
					$content     = ( ( 'before' === $map_position ) ? ( $map_content . $content ) : ( $content . $map_content ) );
				}
			}

			return $content;
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_The_Content::init();
}
