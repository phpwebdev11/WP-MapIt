<?php
/**
 * Class to manage admin ajax.
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

			/* For search location */
			add_action(
				'wp_ajax_wp_mapit_location_search',
				array(
					__CLASS__,
					'wp_mapit_location_search',
				),
			);

			/* To get tag list, display as options when new pin is added */
			add_action(
				'wp_ajax_wp_mapit_get_tags',
				array(
					__CLASS__,
					'wp_mapit_get_tags',
				),
			);

			/* To export pins as CSV file */
			add_action(
				'wp_ajax_wp_mapit_export_pins_csv',
				array(
					__CLASS__,
					'wp_mapit_export_pins_csv',
				),
			);
		}

		/**
		 * Hook to handle wp_mapit_location_search ajax call
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function wp_mapit_location_search() {

			// Capability check.
			if ( ! current_user_can( 'manage_options' ) ) {
				echo wp_json_encode(
					array(
						'status'  => '0',
						'message' => __( 'Unauthorized', 'wp-mapit' ),
					)
				);
				die();
			}

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
			die();
		}

		/**
		 * Hook to handle wp_mapit_get_tags ajax call
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 */
		public static function wp_mapit_get_tags() {

			// Capability check.
			if ( ! current_user_can( 'manage_options' ) ) {
				echo wp_json_encode(
					array(
						'status'  => '0',
						'message' => __( 'Unauthorized', 'wp-mapit' ),
					)
				);
				die();
			}

			// Check nonce for security.
			if ( ! check_ajax_referer( 'wp_mapit_admin_ajax_nonce', 'wp_mapit_ajax' ) ) {
				echo wp_json_encode(
					array(
						'status'  => '0',
						'message' => __( 'Invalid request.', 'wp-mapit' ),
					)
				);
				die();
			}

			// Get the tags field HTML.
			$base_key = isset( $_POST['base_key'] ) ? sanitize_text_field( wp_unslash( $_POST['base_key'] ) ) : '';
			$index    = isset( $_POST['index'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['index'] ) ) : 0;
			echo self::get_tags_field_html( $base_key, $index ); // phpcs:ignore
			die();
		}

		/**
		 * Generate the tags select field HTML.
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 *
		 * @param string $base_key The base name for the select field.
		 * @param int    $index The index to differentiate multiple fields.
		 * @param array  $selected_tags Optional selected term IDs.
		 * @return string HTML for the tags select field.
		 */
		public static function get_tags_field_html( string $base_key, int $index, array $selected_tags = array() ) {
			ob_start();

			$terms = get_terms(
				array(
					'taxonomy'   => 'wp_mapit_tag',
					'hide_empty' => false,
				)
			);
			?>

			<div class="wp-mapit-row">
				<label><?php echo esc_html__( 'Tags', 'wp-mapit' ); ?></label>
				<select class="wp-mapit-select2 pin_tags" name="<?php echo esc_attr( $base_key ); ?>[<?php echo esc_attr( $index ); ?>][tags][]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select Tags', 'wp-mapit' ); ?>">
					<?php
					if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							$term_id = $term->term_id ?? 0;
							?>
							<option value="<?php echo esc_attr( $term_id ); ?>"<?php selected( in_array( $term_id, $selected_tags, true ) ); ?>><?php echo esc_html( $term->name ?? '' ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</div>
			<?php

			return (string) ob_get_clean();
		}

		/**
		 * To export pins as CSV file.
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 */
		public static function wp_mapit_export_pins_csv() {

			// Check nonce for security.
			if ( ! check_ajax_referer( 'wp_mapit_admin_ajax_nonce', 'wp_mapit_ajax' ) ) {
				echo wp_json_encode(
					array(
						'status'  => '0',
						'message' => __( 'Invalid request.', 'wp-mapit' ),
					)
				);
				die();
			}

			$post_id = isset( $_GET['post_id'] ) ? (int) sanitize_text_field( wp_unslash( $_GET['post_id'] ) ) : 0;
			$pins    = (array) get_post_meta( $post_id, 'wp_mapit_pins', true );

			// If no pins, send message.
			if ( 0 === count( $pins ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'No pins found.', 'wp-mapit' ),
					)
				);
			}

			// Set the response headers to download the generated CSV file.
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=WpMapit-Pins.csv' );

			// Create a temporary stream to generate the CSV before sending it in the response.
			$stream = fopen( 'php://temp', 'r+' ); // phpcs:ignore

			if ( false === $stream ) {
				wp_send_json_error(
					array(
						'message' => __( 'Unable to create CSV output stream.', 'wp-mapit' ),
					)
				);
			}

			// Add column headers to the CSV file.
			fputcsv(
				$stream,
				array(
					'Latitude',
					'Longitude',
					'Marker Title',
					'Marker URL',
					'Marker Image',
					'Tags',
					'Marker Content',
				)
			);

			// Write each pin as a CSV row.
			foreach ( $pins as $pin ) {

				// Get comma separated tags slug.
				$tag_ids = $pin['tags'] ?? array();
				$slugs   = array();
				if ( is_array( $tag_ids ) && count( $tag_ids ) > 0 ) {
					foreach ( $tag_ids as $tag_id ) {
						$term = get_term( $tag_id );

						if ( null !== $term && ! is_wp_error( $term ) ) {
							$slugs[] = $term->slug;
						}
					}
				}

				$tags = implode( ', ', $slugs );

				// Add pins data in CSV.
				fputcsv(
					$stream,
					array(
						$pin['lat'] ?? '',
						$pin['lng'] ?? '',
						$pin['marker_title'] ?? '',
						$pin['marker_url'] ?? '',
						$pin['marker_image'] ?? '',
						$tags,
						$pin['marker_content'] ?? '',
					)
				);
			}

			// Move the stream pointer to the beginning.
			rewind( $stream );

			// Get the generated CSV content from the stream.
			$content = stream_get_contents( $stream );

			// Close the temporary stream.
			fclose( $stream ); // phpcs:ignore

			// Send success response.
			wp_send_json_success(
				array(
					'filename' => 'wp_mapit_pins.csv',
					'content'  => $content,
				)
			);
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Admin_Ajax::init();
}
