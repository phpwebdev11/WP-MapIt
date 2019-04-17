<?php
	
	if( !class_exists( 'wp_mapit_admin_ajax' ) ) {
		/**
		 * Class to manage all ajax requests for admin.
		 */
		class wp_mapit_admin_ajax
		{
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
		     * @return string JSON string including the status of the call, data for the location search and message in case of error
		     */
			public static function wp_mapit_location_search() {
				$status = '0';
				$message = '';
				$data = array();

				if( isset( $_REQUEST['q'] ) && trim( $_REQUEST['q'] ) != '' ) {

					$requestUrl = utf8_encode( 'https://nominatim.openstreetmap.org/search?q='.$_REQUEST['q'].'&format=json' );

					$content = wp_remote_retrieve_body( wp_remote_get( $requestUrl ) );

					if( $content != '' ){
						$content = json_decode( $content, true );
						if( is_array( $content ) && count( $content ) > 0 ) {
							$status = '1';
							$data = $content;
						} else {
							$message = __( 'Location not found.', WP_MAPIT_TEXTDOMAIN );
						}
					}

				} else {
					$message = __( 'Please enter a value to search. ', WP_MAPIT_TEXTDOMAIN );
				}

				echo json_encode( array( 'status' => $status, 'message' => $message, 'data' => $data ) );
				die;
			}
		}

		/**
		 * Calling init function to activate hooks and filters.
		 */
		wp_mapit_admin_ajax::init();
	}