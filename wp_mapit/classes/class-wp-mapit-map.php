<?php
/**
 * Class to manage the map.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Map' ) ) {
	/**
	 * Class to generate the map from the settings in the post.
	 */
	class Wp_Mapit_Map {
		/**
		 * Get map latitude
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return Float Returns the latitude of the map
		 */
		public static function get_map_latitude( $post_id ) {
			return trim( get_post_meta( $post_id, 'wpmi_map_latitiude', true ) );
		}

		/**
		 * Get map longitude
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return float Returns the longitude of the map
		 */
		public static function get_map_longitude( $post_id ) {
			return trim( get_post_meta( $post_id, 'wpmi_map_longitude', true ) );
		}

		/**
		 * Get map zoom level
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return float Returns the zoom level of the map
		 */
		public static function get_map_zoom( $post_id ) {
			return trim( get_post_meta( $post_id, 'wpmi_map_zoom', true ) );
		}

		/**
		 * Get map display position
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return string Returns the display position of the map
		 */
		public static function get_map_position( $post_id = null ) {
			if ( null === $post_id ) {
				global $post;

				$post_id = $post->ID;
			}

			$map_position = trim( get_post_meta( $post_id, 'wpmi_map_position', true ) );

			return ( ( '' !== $map_position ) ? $map_position : wp_mapit_admin_settings::get_map_position() );
		}

		/**
		 * Get map type
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return string Returns the type of the map
		 */
		public static function get_map_type( $post_id ) {
			$map_type = trim( get_post_meta( $post_id, 'wpmi_map_type', true ) );

			return ( ( '' !== $map_type ) ? $map_type : wp_mapit_admin_settings::get_map_type() );
		}

		/**
		 * Get map marker image
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return string Returns the marker image of the map
		 */
		public static function get_map_marker( $post_id ) {
			$map_marker = trim( get_post_meta( $post_id, 'wpmi_marker_image', true ) );

			return ( ( '' !== $map_marker ) ? $map_marker : wp_mapit_admin_settings::get_map_marker() );
		}

		/**
		 * Get map marker title
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return string Returns the marker title of the map
		 */
		public static function get_map_title( $post_id ) {
			return trim( get_post_meta( $post_id, 'wpmi_marker_title', true ) );
		}

		/**
		 * Get map marker content
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return string Returns the marker content of the map
		 */
		public static function get_map_content( $post_id ) {
			return nl2br( trim( get_post_meta( $post_id, 'wpmi_marker_content', true ) ) );
		}

		/**
		 * Get map marker url
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return string Returns the marker url of the map
		 */
		public static function get_map_marker_url( $post_id ) {
			return trim( get_post_meta( $post_id, 'wpmi_marker_url', true ) );
		}

		/**
		 * Get map width
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @return int Returns the width of the map
		 */
		public static function get_map_width() {
			$width = trim( wp_mapit_admin_settings::get_map_width() );
			return ( ( '' !== $width && intval( $width ) > 0 ) ? $width : 300 );
		}

		/**
		 * Get map width type
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @return string Returns the width type of the map
		 */
		public static function get_map_width_type() {
			$width_type = trim( wp_mapit_admin_settings::get_map_width_type() );
			return ( ( in_array( $width_type, array( 'px', 'per' ), true ) ? $width_type : 'per' ) );
		}

		/**
		 * Get map height
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @return int Returns the height of the map
		 */
		public static function get_map_height() {
			$height = trim( wp_mapit_admin_settings::get_map_height() );
			return ( ( '' !== $height && intval( $height ) > 0 ) ? $height : 300 );
		}

		/**
		 * Get map height type
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @return string Returns the height type of the map
		 */
		public static function get_map_height_type() {
			$height_type = trim( wp_mapit_admin_settings::get_map_height_type() );
			return ( ( in_array( $height_type, array( 'px', 'per' ), true ) ? $height_type : 'px' ) );
		}

		/**
		 * Checks if map is added or not
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id xId of the post.
		 * @return boolean Returns true if map is added else false, if no id is passed, current post id is considered
		 */
		public static function has_map( $post_id = null ) {
			if ( null === $post_id ) {
				global $post;

				$post_id = $post->ID;
			}

			$lat = self::get_map_latitude( $post_id );
			$lng = self::get_map_longitude( $post_id );

			return ( ( '' !== $lat && 0 !== $lat && '' !== $lng && 0 !== $lng ) ? true : false );
		}

		/**
		 * Function to generate the map by id
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Int $post_id Id of the post.
		 * @return string Returns the map's markup as per the map id, if no id is passed, current post id is considered
		 */
		public static function generate_map( $post_id = null ) {
			if ( null === $post_id ) {
				global $post;

				$post_id = $post->ID;
			}

			if ( ! in_array( $post->post_type, get_option( 'wpmi_allowed_post_types', array() ), true ) ) {
				return;
			}

			ob_start();

			?>
				<div id="wp_mapit_<?php echo esc_attr( wp_mapit_functions::generate_random_string() ); ?>" class="wp_mapit_map" data-lat="<?php echo esc_attr( self::get_map_latitude( $post_id ) ); ?>" data-lng="<?php echo esc_attr( self::get_map_longitude( $post_id ) ); ?>" data-zoom="<?php echo esc_attr( self::get_map_zoom( $post_id ) ); ?>" data-type="<?php echo esc_attr( self::get_map_type( $post_id ) ); ?>" data-marker="<?php echo esc_url( self::get_map_marker( $post_id ) ); ?>" data-title="<?php echo esc_html( self::get_map_title( $post_id ) ); ?>" data-content="<?php echo wp_kses_post( htmlentities( self::get_map_content( $post_id ) ) ); ?>" data-url="<?php echo esc_url( self::get_map_marker_url( $post_id ) ); ?>" data-width="<?php echo esc_attr( self::get_map_width( $post_id ) ); ?>" data-width-type="<?php echo esc_attr( self::get_map_width_type( $post_id ) ); ?>" data-height="<?php echo esc_attr( self::get_map_height( $post_id ) ); ?>" data-height-type="<?php echo esc_attr( self::get_map_height_type( $post_id ) ); ?>"></div>
			<?php

			$content = ob_get_clean();

			return $content;
		}
	}
}
