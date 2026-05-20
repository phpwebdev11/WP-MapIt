<?php
/**
 * Class file for mapit block
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

			// To get tags by map post.
			register_rest_route(
				'wp/v2',
				'/get_tags_by_post',
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'get_tags_by_post' ),
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
		 * Function to handle callback for wp mapit block api.
		 * To get tags by map post.
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 *
		 * @param array $request array of attributes.
		 * @return string JSON encoded array of tags.
		 */
		public static function get_tags_by_post( $request ): string {
			$map_id = (int) ( isset( $request['map_post'] ) ? $request['map_post'] : 0 );
			
			$tag_data = array();
			if ( 0 !== $map_id ) {
				// Get the tags for the selected map post.
				$post_tags = array();
				$pins = Wp_Mapit_Multipin_Map::get_map_pins( $map_id );
				if ( is_array( $pins ) && count( $pins ) > 0 ) {
					foreach ( $pins as $pin ) {
						$pin_tags = isset( $pin['tags'] ) ? (array) $pin['tags'] : array();
						$pin_tags = array_map( 'intval', $pin_tags );
						$post_tags = array_merge( $post_tags, $pin_tags );
					}
				}

				$post_tags = array_unique( $post_tags ); // Get unique tag IDs.

				// Create tags array.
				if ( is_array( $post_tags ) && count( $post_tags ) > 0 ) {
					foreach ( $post_tags as $tag_id ) {
						$tag = get_term( $tag_id, 'wp_mapit_tag' );
						if ( $tag && ! is_wp_error( $tag ) ) {
							$tag_data[] = array(
								'id'   => $tag->term_id ?? 0,
								'name' => $tag->name ?? '',
							);
						}
					}
				}
			}

			return wp_json_encode( $tag_data );
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
			$map_id = $attributes['wp_mapit_map'] ?? '';
			$tags   = $attributes['tags'] ?? array();
			$tags   = is_array( $tags ) ? array_map( 'intval', $tags ) : array();
			$align  = $attributes['align'] ?? '';

			if ( '' !== $map_id ) {
				?>
				<div class="wp-mapit-block <?php echo esc_attr( '' !== $align ? ' align' . $align : '' ); ?>">
					<?php
					if ( Wp_Mapit_Multipin_Map::has_map( $map_id ) ) {
						echo wp_kses_post( Wp_Mapit_Multipin_Map::generate_map( $map_id, $tags ) );
					}
					?>
				</div>
				<?php
			}

			$content = ob_get_clean();
			return $content;
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Blocks::init();
}
