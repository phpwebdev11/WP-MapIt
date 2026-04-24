<?php
/**
 * Class file for mapit block
 *
 * @package wp-mapit
 */

declare( strict_types = 1 );

namespace WpMapit\Classes;

use WP_REST_Request;
use WpMapit\Classes\Wp_Mapit_Multipin_Map;

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Blocks' ) ) {

	/**
	 * Class to manage post content Block
	 *
	 * @since 2.0.0
	 */
	class Wp_Mapit_Blocks {

		/**
		 * Add hooks and filters
		 *
		 * @since  1.0
		 * @static
		 * @access public
		 */
		public static function init() {

			/* For wp mait map api */
			add_action(
				'rest_api_init',
				array(
					__CLASS__,
					'wp_mapit_api',
				)
			);
		}

		/**
		 * Function to handle rest_api_init action.
		 *
		 * @since 2.0.0
		 * @static
		 * @access public
		 */
		public static function wp_mapit_api() {

			// Register api for mapit block.
			register_rest_route(
				'wp/v2',
				'/wp_mapit_map',
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'wp_mapit_callback' ),
					'permission_callback' => '__return_true',
				)
			);
		}

		/**
		 * Function to handle callback for wp mapit block api.
		 *
		 * @since 2.0.0
		 * @static
		 * @access public
		 *
		 * @param array $request array of attributes.
		 */
		public static function wp_mapit_callback( $request ) {
			/* Get the block html */
			ob_start();
			$map_id = isset( $request['wp_mapit_map'] ) ? $request['wp_mapit_map'] : '';

			if ( '' !== $map_id ) {
				?>
				<div class="wp-mapit-block">
					<img src="<?php echo esc_url( WP_MAPIT_URL . 'images/logo.jpg' ); ?>">
				</div>
				<?php
			}

			$content = ob_get_clean();
			return $content;
		}

		/**
		 * Function to handle rendering for wp-mapit-map-block block.
		 * Display map in front end
		 *
		 * @since 2.0.0
		 * @static
		 * @access public
		 *
		 * @param array $attributes array of attributes.
		 */
		public static function render_wp_mapit( $attributes ) {
			ob_start();
			$map_id = isset( $attributes['wp_mapit_map'] ) ? $attributes['wp_mapit_map'] : '';

			if ( '' !== $map_id ) {
				?>
				<div class="wp-mapit-block">
					<?php
					if ( Wp_Mapit_Multipin_Map::has_map( $map_id ) ) {
						echo wp_kses_post( Wp_Mapit_Multipin_Map::generate_map( $map_id ) );
					}
					?>
				</div>
				<?php
			}

			$content = ob_get_clean();
			return $content;
		}
	}
}
