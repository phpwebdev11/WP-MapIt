<?php
/**
 * Class to manage mata box creation.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Create_Metabox' ) ) {
	/**
	 * Class to manage metabox creation for WP MapIt
	 */
	class Wp_Mapit_Create_Metabox {
		/**
		 * Id of the custom metabox.
		 *
		 * @since 1.0
		 * @var id to manage id of the metabox
		 * @access private
		 */
		private $id;

		/**
		 * Title of the custom metabox.
		 *
		 * @since 1.0
		 * @var title to manage the title of the metabox
		 * @access private
		 */
		private $title;

		/**
		 * Metabox Fields.
		 *
		 * @since 1.0
		 * @var fields to manage fields of the metabox
		 * @access private
		 */
		private $fields;

		/**
		 * Metabox post types.
		 *
		 * @since 1.0
		 * @var post_types to manage post types where the metabox will be displayed
		 * @access private
		 */
		private $post_types;

		/**
		 * Add hooks and filters and initialize values for custom metaboxes.
		 *
		 * @since 1.0
		 * @access public
		 * @param Int    $id Id of the custom metabox.
		 * @param String $title Title of the custom metabox.
		 * @param Array  $fields Fields of the custom metabox.
		 * @param Array  $post_types Post types for the custom metabox.
		 */
		public function __construct( $id, $title, $fields, $post_types ) {
			$this->id         = $id;
			$this->title      = $title;
			$this->fields     = $fields;
			$this->post_types = ( is_array( $post_types ) ? $post_types : ( '' !== $post_types ? array( $post_types ) : array() ) );

			add_action(
				'add_meta_boxes',
				array(
					$this,
					'add_meta_boxes',
				)
			);

			add_action(
				'save_post',
				array(
					$this,
					'save_post',
				)
			);
		}

		/**
		 * Sanitize array.
		 *
		 * @since 1.0
		 * @access public
		 * @param Array $_array Array data to be sanitized.
		 * @return Array $_array Array data after sanitization.
		 */
		private function sanitize_array( $_array ) {
			$_array = array_map(
				function ( $_val ) {
					return array_map( 'sanitize_text_field', $_val );
				},
				$_array
			);

			return $_array;
		}

		/**
		 * Hook to handle custom meta boxes
		 *
		 * @since 1.0
		 * @access public
		 */
		public function add_meta_boxes() {
			add_meta_box( $this->id, $this->title, array( $this, 'meta_box_callback' ), $this->post_types, 'normal', 'high' );
		}

		/**
		 * Callback function to display custom metabox.
		 *
		 * @since 1.0
		 * @access public
		 * @param Object $post WordPress Post object.
		 */
		public function meta_box_callback( $post ) {

			wp_nonce_field( 'wp_mapit_metabox', 'wp_mapit_metabox_nonce' );

			if ( is_array( $this->fields ) && count( $this->fields ) > 0 ) {
				?>
				<div class="wp-mapit-metabox-container">
					<?php
					foreach ( $this->fields as $field ) {
						if ( 'section' === $field['type'] ) {
							?>
								<div class="wp-mapit-section <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
									<div class="wp-mapit-row <?php echo esc_attr( isset( $field['row_class'] ) ? $field['row_class'] : '' ); ?>">
										<label><?php echo esc_html( $field['label'] ); ?></label>
										<?php
										if ( is_array( $field['fields'] ) && count( $field['fields'] ) > 0 ) {
											foreach ( $field['fields'] as $cur_field ) {
												?>
													<div class="wp-mapit-row <?php echo esc_attr( isset( $cur_field['row_class'] ) ? $cur_field['row_class'] : '' ); ?>">
														<label><?php echo esc_html( $cur_field['label'] ); ?></label>
														<?php
														$value  = get_post_meta( $post->ID, $cur_field['id'], true );
														$_field = $this->meta_box_field( $post->ID, $cur_field, $value );
														if ( null !== $_field ) {
															echo wp_kses_post( $_field );
														}
														?>
													</div>
												<?php
											}
										}
										?>
									</div>
								</div>
							<?php
						} elseif ( 'multimap_shortcode' === $field['type'] ) {
							$screen = get_current_screen();

							if ( 'add' === $screen->action ) {
								?>
									<p><?php esc_html_e( 'Please save the map to view the shortcode', 'wp-mapit' ); ?></p>
								<?php
							} else {
								?>
									<span><?php esc_html_e( 'Shortcode: ', 'wp-mapit' ); ?> <strong>[wp_mapit_map id="<?php echo esc_html( $post->ID ); ?>"]</strong></span>
									<?php
									if ( isset( $field['desc'] ) ) {
										?>
											<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
										<?php
									}
									?>
								<?php
							}
						} elseif ( 'mappins' === $field['type'] ) {
							$arr_pins = get_post_meta( $post->ID, $field['id'], true );

							$counter = ( is_array( $arr_pins ) ? count( $arr_pins ) : 0 );

							?>
								<div class="wp-mapit-row button-container text-right">
									<a href="#wp-mapit-metabox-map" class="button"><?php esc_html_e( 'Preview Map', 'wp-mapit' ); ?></a>
									<a href="#" id="add_multipin" data-pinid="<?php echo esc_attr( $field['id'] ); ?>" data-counter="<?php echo esc_attr( $counter ); ?>" class="button"><?php esc_html_e( 'Add Map Pin', 'wp-mapit' ); ?></a>
									<a href="#" class="upload_csv_file button"><?php esc_html_e( 'Import Pins CSV', 'wp-mapit' ); ?><span></span></a>

									<?php
										$csv_heading = 'Latitude,Longitude,Marker-Title,Marker-Content';
										$url         = 'data:text/csv;charset=utf-8,' . rawurlencode( $csv_heading );
									?>
									<a href="<?php echo esc_attr( $url ); ?>" target="_blank" download="wp_mapit_pins_csv_template.csv" class="button"><?php esc_html_e( 'Download CSV Template', 'wp-mapit' ); ?></a>
									<a href="#" class="delete_all_pins button"><?php esc_html_e( 'Delete All Pins', 'wp-mapit' ); ?></a>
								</div>
								<div id="wpmi_mappin_container">
									<?php
									if ( $counter > 0 ) {
										$pin_cnt = 0;
										foreach ( $arr_pins as $pin ) {
											?>
												<div id="pin_container_<?php echo esc_attr( $pin_cnt ); ?>" class="wp-mapit-row pin_container">
													<a href="#" title="<?php esc_html_e( 'Remove Pin', 'wp-mapit' ); ?>" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="remove_pin"></a>
													<div class="column-3">
														<div class="wp-mapit-row">
															<label><?php esc_html_e( 'Search Map', 'wp-mapit' ); ?></label>
															<div class="wp-mapit-search">
																<input type="text" id="search_map_<?php echo esc_attr( $pin_cnt ); ?>">
																<a href="#" title="<?php esc_html_e( 'Search Map', 'wp-mapit' ); ?>" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="button map-pin-search"></a>
															</div>
														</div>
														<div class="wp-mapit-row">
															<label><?php esc_html_e( 'Latitude', 'wp-mapit' ); ?></label>
															<input type="number" step="any" name="<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $pin_cnt ); ?>][lat]" required="required" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="pin_latitude" value="<?php echo esc_html( isset( $pin['lat'] ) ? $pin['lat'] : '' ); ?>">
														</div>
														<div class="wp-mapit-row">
															<label><?php esc_html_e( 'Longitude', 'wp-mapit' ); ?></label>
															<input type="number" step="any" name="<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $pin_cnt ); ?>][lng]" required="required" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="pin_longitude" value="<?php echo esc_html( isset( $pin['lng'] ) ? $pin['lng'] : '' ); ?>">
														</div>
														<div class="wp-mapit-row no-margin pin-img-container">
															<label><?php esc_html_e( 'Marker Image', 'wp-mapit' ); ?></label>
															<?php
																$pin_url = ( isset( $pin['marker_image'] ) ? $pin['marker_image'] : '' );
															?>
															<input type="hidden" name="<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $pin_cnt ); ?>][marker_image]" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="pin_marker_image" value="<?php echo esc_url( $pin_url ); ?>">
															<?php
															if ( '' !== $pin_url ) {
																?>
																	<img src="<?php echo esc_url( $pin_url ); ?>">
																<?php
															}
															?>
															<a href="#" class="upload_image button"><?php esc_html_e( 'Choose Image', 'wp-mapit' ); ?></a><span>&nbsp;</span><a href="#" class="remove_image button"><?php esc_html_e( 'Remove Image', 'wp-mapit' ); ?></a>
														</div>
													</div>
													<div class="column-3">
														<div class="wp-mapit-row">
															<label><?php esc_html_e( 'Marker Title', 'wp-mapit' ); ?></label>
															<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $pin_cnt ); ?>][marker_title]" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="pin_title" value="<?php echo esc_html( isset( $pin['marker_title'] ) ? $pin['marker_title'] : '' ); ?>">
														</div>
														<div class="wp-mapit-row">
															<label><?php esc_html_e( 'Marker Content', 'wp-mapit' ); ?></label>
															<textarea name="<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $pin_cnt ); ?>][marker_content]"  data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="pin_content"><?php echo wp_kses_post( isset( $pin['marker_content'] ) ? $pin['marker_content'] : '' ); ?></textarea>
														</div>
														<div class="wp-mapit-row no-margin">
															<label><?php esc_html_e( 'Marker URL', 'wp-mapit' ); ?></label>
															<input type="text" name="<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $pin_cnt ); ?>][marker_url]" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="pin_url" value="<?php echo esc_url( isset( $pin['marker_url'] ) ? $pin['marker_url'] : '' ); ?>">
														</div>
													</div>
													<div class="column-3 no-margin">
														<div class="wp-mapit-row no-margin">
															<label><?php esc_html_e( 'Map', 'wp-mapit' ); ?></label>
															<div id="pin_map_<?php echo esc_attr( $pin_cnt ); ?>" data-counter="<?php echo esc_attr( $pin_cnt ); ?>" class="pin_map"></div>
														</div>
													</div>
													<div class="clearfix"></div>
												</div>
											<?php
											++$pin_cnt;
										}
									}
									?>
								</div>
							<?php
						} else {
							?>
								<div class="wp-mapit-row <?php echo esc_attr( isset( $field['row_class'] ) ? $field['row_class'] : '' ); ?>">
									<label><?php echo esc_html( $field['label'] ); ?></label>
									<?php
									$value  = get_post_meta( $post->ID, $field['id'], true );
									$_field = $this->meta_box_field( $post->ID, $field, $value );
									if ( null !== $_field ) {
										echo wp_kses_post( $_field );
									}
									?>
								</div>
							<?php
						}
					}
					?>
					<div class="clearfix"></div>
				</div>
				<?php
			}
		}

		/**
		 * Function to display the field for the custom metabox
		 *
		 * @since 1.0
		 * @access private
		 * @param Int    $post_id Id of the post.
		 * @param Array  $field Metabox field.
		 * @param String $value Value for the metabox field.
		 */
		private function meta_box_field( $post_id, $field, $value = null ) {
			$type              = isset( $field['type'] ) ? $field['type'] : null;
			$label             = isset( $field['label'] ) ? $field['label'] : null;
			$desc              = isset( $field['desc'] ) ? '<p class="description">' . $field['desc'] . '</p>' : null;
			$options           = isset( $field['options'] ) ? $field['options'] : null;
			$settings          = isset( $field['settings'] ) ? $field['settings'] : null;
			$repeatable_fields = isset( $field['repeatable_fields'] ) ? $field['repeatable_fields'] : null;
			$id                = isset( $field['id'] ) ? esc_attr( $field['id'] ) : null;
			$name              = $id;

			switch ( $type ) {
				case 'text':
				case 'tel':
				case 'email':
					?>
						<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $value ); ?>">
					<?php
					break;
				case 'url':
					?>
						<input type="url" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_url( $value ); ?>">
					<?php
					break;
				case 'number':
					$step = ( isset( $field['step'] ) ? $field['step'] : '' );
					$min  = ( isset( $field['min'] ) ? $field['min'] : '' );
					$max  = ( isset( $field['max'] ) ? $field['max'] : '' );
					?>
						<input type="number" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo floatval( $value ); ?>" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>">
					<?php
					break;
				case 'textarea':
					?>
						<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
					<?php
					break;
				case 'editor':
					wp_editor( $value, $id );
					break;
				case 'checkbox':
					?>
						<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo checked( $value, true, false ); ?> value="1">
					<?php
					break;
				case 'checkbox_group':
					if ( is_array( $options ) && count( $options ) > 0 ) {
						$value = is_array( $value ) ? $value : array();
						?>
							<div class="wp-mapit-checkbox-group">
							<?php
							foreach ( $options as $option ) {
								?>
									<div class="wp-mapit-checkbox"><input type="checkbox" id="<?php echo esc_attr( $id . $option['value'] ); ?>" name="<?php echo esc_attr( $name ); ?>[]" <?php echo esc_attr( in_array( $option['value'], $value, true ) ? 'checked="checked"' : '' ); ?> value="<?php echo esc_html( $option['value'] ); ?>"><span for="<?php echo esc_attr( $id . $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></span></div>
								<?php
							}
							?>
							</div>
						<?php
					}

					break;
				case 'select':
					?>
						<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
							<?php
							if ( is_array( $options ) && count( $options ) > 0 ) {
								foreach ( $options as $option ) {
									?>
										<option value="<?php echo esc_html( $option['value'] ); ?>" <?php echo selected( $value, $option['value'], false ); ?>><?php echo esc_html( $option['label'] ); ?></option>
									<?php
								}
							}
							?>
						</select>
					<?php
					break;
				case 'radio':
					if ( is_array( $options ) && '' !== $options ) {
						?>
							<ul>
								<?php
								foreach ( $options as $option ) {
									?>
										<li><input type="radio" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $option['value'] ); ?>"> <?php echo esc_html( $option['label'] ); ?></li>
									<?php
								}
								?>
							</ul>
						<?php
					}
					break;
				case 'image':
					?>
						<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $value ); ?>">
						<?php
						if ( '' !== trim( $value ) ) {
							?>
								<img src="<?php echo esc_url( $value ); ?>">
							<?php
						}
						?>
						<a href="#" class="upload_image button"><?php esc_html_e( 'Choose Image', 'wp-mapit' ); ?></a> 
						<a href="#" class="remove_image button"><?php esc_html_e( 'Remove Image', 'wp-mapit' ); ?></a>
					<?php
					break;
				case 'map':
					$default_map_type = wp_mapit_admin_settings::get_map_type();
					$default_zoom     = wp_mapit_admin_settings::get_map_zoom();
					$default_lat      = wp_mapit_admin_settings::get_map_latitude();
					$default_lng      = wp_mapit_admin_settings::get_map_longitude();
					$default_marker   = wp_mapit_admin_settings::get_map_marker();
					?>
						<div id="<?php echo esc_attr( $id ); ?>" data-maptype="<?php echo esc_attr( $default_map_type ); ?>" data-zoom="<?php echo esc_attr( $default_zoom ); ?>" data-latitude="<?php echo esc_attr( $default_lat ); ?>" data-longitude="<?php echo esc_attr( $default_lng ); ?>" data-marker="<?php echo esc_url( $default_marker ); ?>"></div>
					<?php
					break;
				case 'map_search':
					?>
						<div class="wp-mapit-search">
							<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
							<a href="#" id="<?php echo esc_attr( $id ); ?>_btn" class="button"><?php esc_html_e( 'Search', 'wp-mapit' ); ?></a>
						</div>
					<?php
					break;
				default:
					do_action( 'wp_mapit_custom_field_' . $type, $post_id, $field, $value );
			}

			if ( null !== $desc ) {
				echo wp_kses_post( $desc );
			}
		}

		/**
		 * Hook to save values for the custom meta boxes.
		 *
		 * @since 1.0
		 * @access public
		 * @param Int $post_id Id of the post.
		 */
		public function save_post( $post_id ) {
			$post_type = get_post_type();

			/* verify nonce */
			if ( ! isset( $_POST['wp_mapit_metabox_nonce'] ) ) {
				return $post_id;
			}

			if ( ! ( in_array( $post_type, $this->post_types, true ) || wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_mapit_metabox_nonce'] ) ), 'wp_mapit_metabox' ) ) ) {
				return $post_id;
			}

			/* check autosave */
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}
			/* check permissions */
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

			if ( is_array( $this->fields ) && count( $this->fields ) > 0 && in_array( $post_type, $this->post_types, true ) ) {
				foreach ( $this->fields as $field ) {

					if ( ! in_array( $field['type'], apply_filters( 'wp_mapit_exclude_save_fields', array( 'map', 'map_search' ) ), true ) ) {
						if ( 'section' === $field['type'] ) {
							if ( isset( $field['fields'] ) && is_array( $field['fields'] ) && count( $field['fields'] ) > 0 ) {
								foreach ( $field['fields'] as $section_field ) {
									$sanitize = ( isset( $section_field['sanitize'] ) ? $section_field['sanitize'] : '' );
									if ( 'sanitize_textarea' === $sanitize ) {
										update_post_meta( $post_id, $section_field['id'], ( isset( $_POST[ $section_field['id'] ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $section_field['id'] ] ) ) : '' ) );
									} else {
										update_post_meta( $post_id, $section_field['id'], ( isset( $_POST[ $section_field['id'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $section_field['id'] ] ) ) : '' ) );
									}
								}
							}
						} elseif ( 'mappins' === $field['type'] ) {
							update_post_meta( $post_id, $field['id'], ( ( isset( $_POST[ $field['id'] ] ) && is_array( $_POST[ $field['id'] ] ) ) ? $this->sanitize_array( wp_unslash( $_POST[ $field['id'] ] ) ) : array() ) );
						} else {
							$sanitize = ( isset( $field['sanitize'] ) ? $field['sanitize'] : '' );
							if ( 'sanitize_textarea' === $sanitize ) {
								update_post_meta( $post_id, $field['id'], ( isset( $_POST[ $field['id'] ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $field['id'] ] ) ) : '' ) );
							} else {
								update_post_meta( $post_id, $field['id'], ( isset( $_POST[ $field['id'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field['id'] ] ) ) : '' ) );
							}
						}
					}
				}

				do_action( 'wp_mapit_after_save', $post_id );
			}
		}
	}
}
