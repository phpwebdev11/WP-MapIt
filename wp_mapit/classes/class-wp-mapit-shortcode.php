<?php
/**
 * Manage shortcode.
 *
 * @package wp-mapit
 */

declare( strict_types = 1 );

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

			$map_id = (int) ( $atts['id'] ?? 0 );
			$tags   = $atts['tags'] ?? '';
			$tags   = self::wp_mapit_get_tag_ids_from_slugs( $tags );

			// If map ID is provided, generate the map for that ID.
			if ( $map_id > 0 ) {
				if ( Wp_Mapit_Multipin_Map::has_map( $map_id ) ) {
					return Wp_Mapit_Multipin_Map::generate_map( $map_id, $tags );
				}
			}
		}

		/**
		 * Function to convert tag slugs to tag IDs.
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 * @param string $slugs Comma-separated string of tag slugs.
		 * @return array Returns an array of tag IDs corresponding to the provided slugs.
		 */
		 public static function wp_mapit_get_tag_ids_from_slugs( $slugs ) {
			// Trim and split the slugs into an array.
			$slugs = array_filter( array_map( 'trim', explode( ',', $slugs ) ) );

			// If no slugs provided, return empty array.
			if ( empty( $slugs ) ) {
				return [];
			}

			// Get terms by slugs.
			$terms = get_terms([
				'taxonomy'   => 'wp_mapit_tag',
				'slug'       => $slugs,
				'hide_empty' => false,
			]);

			// If error or no terms found, return empty array.
			if ( is_wp_error( $terms ) ) {
				return [];
			}

			// Return array of term IDs.
			return wp_list_pluck( $terms, 'term_id' );
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Shortcode::init();
}
