<?php
/**
 * Class to manage admin ajax.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Admin_Ajax' ) ) {
	/**
	 * Class to manage all ajax requests for admin.
	 */
	class Wp_Mapit_Admin_Ajax {
		/**
		 * Function to initialize all the ajax requests for admin
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			add_action( 'wp_ajax_wp_mapit_location_search', __CLASS__ . '::wp_mapit_location_search' );
		}

		/**
		 * Hook to handle wp_mapit_location_search ajax call
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function wp_mapit_location_search() {
			$status  = '0';
			$message = '';
			$data    = array();
			if ( check_ajax_referer( 'wp_mapit_admin_ajax_nonce', 'wp_mapit_ajax' ) ) {
				$search = isset( $_REQUEST['q'] ) ? trim( sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) ) : '';
				if ( '' !== $search ) {

					$request_url = 'https://nominatim.openstreetmap.org/search?q=' . $search . '&format=json';

					$content = wp_remote_retrieve_body( wp_remote_get( $request_url ) );
					if ( '' !== $content ) {
						$content = json_decode( $content, true );

						if ( is_array( $content ) && count( $content ) > 0 ) {
							$status = '1';
							$data   = $content;
						} else {
							$message = __( 'Location not found.', 'wp-mapit' );
						}
					}
				} else {
					$message = __( 'Please enter a value to search.', 'wp-mapit' );
				}
			} else {
				$message = __( 'Invalid search request.', 'wp-mapit' );
			}

			echo wp_json_encode(
				array(
					'status'  => $status,
					'message' => $message,
					'data'    => $data,
				)
			);
			die;
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Admin_Ajax::init();
}
