<?php

	if( ! class_exists( 'wp_mapit_create_metabox' ) ) {
		/**
		 * Class to manage metabox creation for WP MapIt
		 */
		class wp_mapit_create_metabox
		{
			/**
		     * @since 1.0
		     * @var id to manage id of the metabox
		     * @access private
		     */
			private $id;

			/**
		     * @since 1.0
		     * @var title to manage the title of the metabox
		     * @access private
		     */
			private $title;

			/**
		     * @since 1.0
		     * @var fields to manage fields of the metabox
		     * @access private
		     */
			private $fields;

			/**
		     * @since 1.0
		     * @var postTypes to manage post types where the metabox will be displayed
		     * @access private
		     */
			private $postTypes;

			/**
		     * Add hooks and filters and initialize values for custom metaboxes
		     * 
		     * @since 1.0
		     * @access public
		     * @param $id Int Id of the custom metabox
		     * @param $title String Title of the custom metabox
		     * @param $fields Array Fields of the custom metabox
		     * @param $postTypes Array Post types for the custom metabox
		     */
			public function __construct($id, $title, $fields, $postTypes)
			{
				$this->id = $id;
				$this->title = $title;
				$this->fields = $fields;
				$this->postTypes = ( is_array( $postTypes ) ? $postTypes : ( $postTypes != '' ? array( $postTypes ) : array() ) );

				add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
				add_action( 'save_post',  array( $this, 'save_post' ));
			}

			/**
		     * Hook to handle custom meta boxes
		     *
		     * @since 1.0
		     * @access public
		     */
			public function add_meta_boxes() {
				add_meta_box( $this->id, $this->title, array( $this, 'meta_box_callback' ), $this->postTypes, 'normal', 'high' );
			}

			/**
		     * Callback function to display custom metabox
		     *
		     * @since 1.0
		     * @access public
		     * @param $post Object Wordpress Post object
		     */
			public function meta_box_callback( $post ) {

				wp_nonce_field( 'wp_mapit_metabox', 'wp_mapit_metabox_nonce' );

				if( is_array( $this->fields ) && count( $this->fields ) > 0 ) {
?>
					<div class="wp-mapit-metabox-container">
						<?php
							foreach ($this->fields as $field) {
								if( $field['type'] == 'section' ) {
						?>
									<div class="wp-mapit-section <?php echo ( isset( $field['class'] ) ? $field['class'] : '' ); ?>">
										<div class="wp-mapit-row <?php echo ( isset( $field['row_class'] ) ? $field['row_class'] : '' ); ?>">
											<label><?php echo $field['label']; ?></label>
											<?php
												if( is_array( $field['fields'] ) && count( $field['fields'] ) > 0 ) {
													foreach ($field['fields'] as $curField) {
											?>
														<div class="wp-mapit-row <?php echo ( isset( $curField['row_class'] ) ? $curField['row_class'] : '' ); ?>">
															<label><?php echo $curField['label']; ?></label>
															<?php
																$value = get_post_meta( $post->ID, $curField['id'], true );
																echo $this->meta_box_field( $post->ID, $curField, $value );
															?>
														</div>
											<?php
													}
												}
											?>
										</div>
									</div>
						<?php
								} else {
						?>
									<div class="wp-mapit-row <?php echo ( isset( $field['row_class'] ) ? $field['row_class'] : '' ); ?>">
										<label><?php echo $field['label']; ?></label>
										<?php
											$value = get_post_meta( $post->ID, $field['id'], true );
											echo $this->meta_box_field( $post->ID, $field, $value );
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
		     * @param $postId Int Id of the post
		     * @param $field Array Metabox field
		     * @param $value String Value for the metabox field
		     */
			private function meta_box_field( $postId, $field, $value = null ) {
				$type = isset( $field['type'] ) ? $field['type'] : null;
				$label = isset( $field['label'] ) ? $field['label'] : null;
				$desc = isset( $field['desc'] ) ? '<p class="description">' . $field['desc'] . '</p>' : null;
				$options = isset( $field['options'] ) ? $field['options'] : null;
				$settings = isset( $field['settings'] ) ? $field['settings'] : null;
				$repeatable_fields = isset( $field['repeatable_fields'] ) ? $field['repeatable_fields'] : null;
				$id = $name = isset( $field['id'] ) ? esc_attr( $field['id'] ) : null;

				switch ( $type ) {
					case 'text':
					case 'tel':
					case 'email':
					default:
?>
						<input type="<?php echo $type ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>">
<?php
						break;
					case 'url':
?>
						<input type="url" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_url( $value ); ?>">
<?php
						break;
					case 'number':
						$step = ( isset( $field['step'] ) ? $field['step'] : '' );
						$min = ( isset( $field['min'] ) ? $field['min'] : '' );
						$max = ( isset( $field['max'] ) ? $field['max'] : '' );
?>
						<input type="number" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo floatval( $value ); ?>" step="<?php echo $step; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>">
<?php
						break;
					case 'textarea':
?>
						<textarea id="<?php echo $id; ?>" name="<?php echo $name; ?>"><?php echo esc_textarea( $value ); ?></textarea>
<?php
						break;
					case 'editor':
						echo wp_editor( $value, $id );
						break;
					case 'checkbox':
?>
						<input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" <?php echo checked( $value, true, false ) ?> value="1">
<?php
						break;
					case 'select':
?>
						<select id="<?php echo $id; ?>" name="<?php echo $name; ?>">
							<?php
								if( is_array( $options ) && count( $options ) > 0 ) {
									foreach ($options as $option) {
							?>
										<option value="<?php echo $option['value']; ?>" <?php echo selected( $value, $option['value'], false ); ?>><?php echo $option['label']; ?></option>
							<?php
									}
								}
							?>
						</select>
<?php
						break;
					case 'radio':
						if( is_array( $options ) && $options == '' ) {
?>
							<ul>
								<?php
									foreach ($options as $option) {
								?>
										<li><input type="radio" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $option['value']; ?>"> <?php echo $option['label']; ?></li>
								<?php
									}
								?>
							</ul>
<?php
						}
						break;
					case 'checkbox_group':
						if( is_array( $options ) && $options == '' ) {
?>
							<ul>
								<?php
									foreach ($options as $option) {
								?>
										<li><input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>[]" value="<?php echo $option['value']; ?>"> <?php echo $option['label']; ?></li>
								<?php
									}
								?>
							</ul>
<?php
						}
						break;
					case 'image':
?>
						<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
						<?php
							if( trim( $value ) != '' ) {
						?>
								<img src="<?php echo $value; ?>">
						<?php
							}
						?>
						<a href="#" class="upload_image button"><?php _e( 'Choose Image', WP_MAPIT_TEXTDOMAIN ); ?></a> 
						<a href="#" class="remove_image button"><?php _e( 'Remove Image', WP_MAPIT_TEXTDOMAIN ); ?></a>
<?php
						break;
					case 'map':
						$defaultMapType = wp_mapit_admin_settings::get_map_type();
						$defaultZoom = wp_mapit_admin_settings::get_map_zoom();
						$defaultLat = wp_mapit_admin_settings::get_map_latitude();
						$defaultLng = wp_mapit_admin_settings::get_map_longitude();
						$defaultMarker = wp_mapit_admin_settings::get_map_marker();
?>
						<div id="<?php echo $id; ?>" data-maptype="<?php echo $defaultMapType; ?>" data-zoom="<?php echo $defaultZoom; ?>" data-latitude="<?php echo $defaultLat; ?>" data-longitude="<?php echo $defaultLng; ?>" data-marker="<?php echo $defaultMarker; ?>"></div>
<?php
						break;
					case 'map_search':
?>
						<div class="wp-mapit-search">
							<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>">
							<a href="#" id="<?php echo $id; ?>_btn" class="button"><?php _e( 'Search', WP_MAPIT_TEXTDOMAIN ); ?></a>
						</div>
<?php
						break;
				}

				echo $desc;
			}

			/**
		     * Hook to save values for the custom meta boxes
		     *
		     * @since 1.0
		     * @access public
		     * @param $post_id Int Id of the post
		     */
			public function save_post( $post_id ) {
				$post_type = get_post_type();
		
				// verify nonce
				if ( ! isset( $_POST['wp_mapit_metabox_nonce'] ) )
					return $post_id;
				if ( ! ( in_array( $post_type, $this->postTypes ) || wp_verify_nonce( $_POST['wp_mapit_metabox_nonce'],  'wp_mapit_metabox' ) ) ) 
					return $post_id;
				// check autosave
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
					return $post_id;
				// check permissions
				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;

				if( is_array( $this->fields ) && count( $this->fields ) > 0 ) {
					foreach ( $this->fields as $field ) {

						if( ! in_array( $field['type'] , array( 'map', 'map_search' ) ) ) {
							if( $field['type'] == 'section' ) {
								if( isset( $field['fields'] ) && is_array( $field['fields'] ) && count( $field['fields'] ) > 0 ) {
									foreach ( $field['fields'] as $sectionField ) {
										$sanitize = ( isset( $sectionField['sanitize'] ) ? $sectionField['sanitize'] : '' );
										update_post_meta( $post_id, $sectionField['id'], ( isset( $_POST[$sectionField['id']] ) ? $this->sanitize_field( $_POST[$sectionField['id']], $sanitize ) : '' ) );
									}
								}
							} else {
								$sanitize = ( isset( $field['sanitize'] ) ? $field['sanitize'] : '' );
								update_post_meta( $post_id, $field['id'], ( isset( $_POST[$field['id']] ) ? $this->sanitize_field( $_POST[$field['id']], $sanitize ) : '' ) );
							}
						}
					}
				}
			}

			/**
		     * Function to sanitize user input
		     *
		     * @since 1.0
		     * @access private
		     * @param $string String String to be sanitized
		     * @param $function String Function used to sanitize the string
		     * @return string Returns the sanitize string
		     */
			private function sanitize_field( $string, $function = 'sanitize_text_field' ) {
				switch ( $function ) {
					case 'intval':
						return intval( $string );
					case 'absint':
						return absint( $string );
					case 'wp_kses_post':
						return wp_kses_post( $string );
					case 'wp_kses_data':
						return wp_kses_data( $string );
					case 'esc_url_raw':
						return esc_url_raw( $string );
					case 'is_email':
						return is_email( $string );
					case 'sanitize_title':
						return sanitize_title( $string );
					case 'santitize_boolean':
						return santitize_boolean( $string );
					case 'sanitize_textarea':
						return sanitize_textarea_field( $string );
					case 'sanitize_text_field':
					default:
						return sanitize_text_field( $string );
				}
			}
		}
	}