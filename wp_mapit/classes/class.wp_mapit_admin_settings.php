<?php

	if( ! class_exists( 'wp_mapit_admin_settings' ) ) {

		/**
		 * Class to manage admin settings for WP MapIt.
		 */
		class wp_mapit_admin_settings
		{
			
			/**
		     * @since 1.0
		     * @var mapTypes to manage the map types
		     * @access private
		     */
			private static $mapTypes = array(
					'normal' => 'Normal',
					'grayscale' => 'Grayscale',
					'topographic' => 'Topographic'
				);

			/**
		     * @since 1.0
		     * @var mapPostions to manage the map positions
		     * @access private
		     */
			private static $mapPostions = array(
					'before' => 'Before Content',
					'after' => 'After Content',
					'none' => 'Custom'
				);

			/**
		     * @since 1.0
		     * @var mapSizeType to manage the map size type
		     * @access private
		     */
			private static $mapSizeType = array(
					'px' => 'px',
					'per' => '%'
				);

			/**
		     * @since 1.0
		     * @var mapMarker to manage the default map pin
		     * @access private
		     */
			private static $mapMarker = WP_MAPIT_URL . 'images/map-pin.png';

			/**
		     * Add hooks and filters for admin menu
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function init()
			{
				/* Add admin menu */
				add_action( 'admin_menu', __CLASS__ . '::admin_menu' );

				/* Add init hook */
				add_action( 'admin_init', __CLASS__ . '::admin_init' );
			}

			/**
		     * Hook to manage WP MapIt admin menu
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function admin_menu() {
				add_menu_page( __('WP MapIt', WP_MAPIT_TEXTDOMAIN), __('WP MapIt', WP_MAPIT_TEXTDOMAIN), 'manage_options', 'wp_mapit', __CLASS__ . '::wp_mapit_settings', 'dashicons-location-alt' );
			}

			/**
		     * Hook to display WP MapIt Settings page
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function wp_mapit_settings() {
?>
				<div class="wrap">
					<div id="wp-mapit-settings-container">
						<div class="wp-mapit-header">
							<ul>
								<li><?php _e( 'Support:', WP_MAPIT_TEXTDOMAIN ); ?> <a href="mailto:wp-mapit@phpwebdev.in">wp-mapit@phpwebdev.in</a></li>
								<li><a href="https://www.paypal.me/chandnipatel11" target="_blank"><?php _e( 'Donate', WP_MAPIT_TEXTDOMAIN ); ?></a></li>
							</ul>
						</div>
						
						<?php

							if( isset( $_REQUEST['info'] ) && $_REQUEST['info'] == 's' ) {
						?>
								<div class="updated notice is-dismissible">
									<p><strong><?php _e( 'Settings saved.', WP_MAPIT_TEXTDOMAIN ); ?></strong></p>
									<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', WP_MAPIT_TEXTDOMAIN ); ?></span></button>
								</div>
						<?php
							}

						?>
						<div class="wp-mapit-content">
							<div class="wp-mapit-form">
								<form name="frmWpMapitSettings" id="frmWpMapitSettings" method="post" action="">
									<?php
										wp_nonce_field( 'wp_mapit_settings', 'wp_mapit_settings_nonce' );
									?>
									
									<div class="wp-mapit-row">
										<label><?php _e( 'Map Settings', WP_MAPIT_TEXTDOMAIN ); ?></label>
										<div class="wp-mapit-row">
											<label for="wpmi_map_type"><?php _e( 'Map Type', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<select name="wpmi_map_type" id="wpmi_map_type">
												<?php
													$selectedMapType = self::get_map_type();
													foreach (self::$mapTypes as $mapTypeId => $mapType) {
												?>
														<option value="<?php echo $mapTypeId; ?>" <?php echo ( $selectedMapType == $mapTypeId ? "selected='selected'" : '' ); ?>><?php _e( $mapType, WP_MAPIT_TEXTDOMAIN ); ?></option>
												<?php
													}
												?>
											</select>
											<p class="description"><?php _e( 'Type of the map to be used.', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_latitude"><?php _e( 'Latitude', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<input type="number" name="wpmi_latitude" id="wpmi_latitude" step="any" value="<?php echo self::get_map_latitude(); ?>">
											<p class="description"><?php _e( 'Default latitude for the map.', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_longitude"><?php _e( 'Longitude', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<input type="number" name="wpmi_longitude" id="wpmi_longitude" step="any" value="<?php echo self::get_map_longitude(); ?>">
											<p class="description"><?php _e( 'Default longitude for the map.', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
										<div class="wp-mapit-row no-margin">
											<label for="wpmi_map_zoom"><?php _e( 'Zoom', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<input type="number" name="wpmi_map_zoom" id="wpmi_map_zoom" step="0.01" min="1" max="20" value="<?php echo self::get_map_zoom(); ?>">
											<p class="description"><?php _e( 'Default zoom for the map.', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
									</div>
									
									<div class="wp-mapit-row">
										<label><?php _e( 'General Settings', WP_MAPIT_TEXTDOMAIN ); ?></label>
										<div class="wp-mapit-row">
											<label for="wpmi_map_width"><?php _e( 'Map Width', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<div class="wp-mapit-size">
												<input type="number" name="wpmi_map_width" id="wpmi_map_width" value="<?php echo self::get_map_width(); ?>">
												<select name="wpmi_map_width_type" id="wpmi_map_width_type">
													<?php
														$selectedMapWidthType = self::get_map_width_type();
														foreach (self::$mapSizeType as $mapSizeTypeId => $mapSizeTypeName) {
													?>
															<option value="<?php echo $mapSizeTypeId; ?>" <?php echo ( $selectedMapWidthType == $mapSizeTypeId ? "selected='selected'" : '' ); ?>><?php _e( $mapSizeTypeName, WP_MAPIT_TEXTDOMAIN ); ?></option>
													<?php
														}
													?>
												</select>
											</div>
											<p class="description"><?php _e( 'Map width', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_map_height"><?php _e( 'Map Height', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<div class="wp-mapit-size">
												<input type="number" name="wpmi_map_height" id="wpmi_map_height" value="<?php echo self::get_map_height(); ?>">
												<select name="wpmi_map_height_type" id="wpmi_map_height_type">
													<?php
														$selectedMapHeightType = self::get_map_height_type();
														foreach (self::$mapSizeType as $mapSizeTypeId => $mapSizeTypeName) {
													?>
															<option value="<?php echo $mapSizeTypeId; ?>" <?php echo ( $selectedMapHeightType == $mapSizeTypeId ? "selected='selected'" : '' ); ?>><?php _e( $mapSizeTypeName, WP_MAPIT_TEXTDOMAIN ); ?></option>
													<?php
														}
													?>
												</select>
											</div>
											<p class="description"><?php _e( 'Map height', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_map_marker"><?php _e( 'Map Marker', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<?php
												$wpmiMapPin = self::get_map_marker();
											?>
											<div class="wp_mapit_image_upload">
												<img src="<?php echo $wpmiMapPin; ?>" alt="Map Pin" width="24px">
												<a href="#" id="upload_map_pin" class="button"><?php _e( 'Choose Map Pin', WP_MAPIT_TEXTDOMAIN ); ?></a>
												<a href="#" id="reset_map_pin" class="button" data-default-pin="<?php echo self::$mapMarker; ?>"><?php _e( 'Reset Map Pin', WP_MAPIT_TEXTDOMAIN ); ?></a>
												<input type="hidden" name="wpmi_map_marker" id="wpmi_map_marker" value="<?php echo $wpmiMapPin; ?>">
											</div>
											<p class="description"><?php _e( 'Default marker for the map. Max size 100px X 100px. Image bigger then the size will be resized.', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label><?php _e( 'Allow map for', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<?php
												
												$allowedPostTypes = get_option( 'wpmi_allowed_post_types', array() );	

												$arrPostTypes = get_post_types( array(
													'public' => true,
												) );

												if( is_array( $arrPostTypes ) && count( $arrPostTypes ) > 0 ) {
											?>
													<ul>
											<?php
														foreach ( $arrPostTypes as $postType ) {
															if( ! in_array( $postType, array( 'wp_mapit_map', 'attachment' )) ) {
											?>
															<li><input type="checkbox" name="wpmi_allowed_post_types[]" id="wpmi_allowed_post_type_<?php echo $postType; ?>" value="<?php echo $postType; ?>" <?php echo ( in_array( $postType , $allowedPostTypes ) ? "checked='checked'" : '' ); ?>><?php echo $postType; ?></li>
											<?php
															}
														}
											?>
													</ul>
											<?php	
												}
											?>
											<p class="description"><?php _e( 'Select where the map can be displayed.', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
										<div class="wp-mapit-row no-margin">
											<label for="wpmi_map_position"><?php _e( 'Show Map', WP_MAPIT_TEXTDOMAIN ); ?></label>
											<select name="wpmi_map_position" id="wpmi_map_position">
												<?php
													$selectedMapPosition = self::get_map_position();
													foreach (self::$mapPostions as $mapPositionId => $mapPosition) {
												?>
														<option value="<?php echo $mapPositionId; ?>" <?php echo ( $selectedMapPosition == $mapPositionId ? "selected='selected'" : '' ); ?>><?php _e( $mapPosition, WP_MAPIT_TEXTDOMAIN ); ?></option>
												<?php
													}
												?>
											</select>
											<p class="description"><?php _e( 'Position where the map needs to be displayed.<br>For custom, use one of the following: <br>1. Shortcode [wp_mapit].<br>2. Sidebar widget<br>3. Gutenberg block "WP MAPIT".', WP_MAPIT_TEXTDOMAIN ); ?></p>
										</div>
									</div>
									<div class="wp-mapit-row button-container">
										<input type='submit' class='button-primary' name='save_settings' id='save_settings' value='<?php _e( 'Save Settings', WP_MAPIT_TEXTDOMAIN ); ?>'>
									</div>
								</form>
							</div>
							<div class="wp-mapit-map">
								<div class="wp-mapit-row no-margin">
									<label><?php _e( 'Search Location', WP_MAPIT_TEXTDOMAIN ); ?></label>
									<div class="wp-mapit-search">
										<input type="text" name="search_map" id="search_map">
										<input type='button' class='button' name='search_map_btn' id='search_map_btn' value='<?php _e( 'Search', WP_MAPIT_TEXTDOMAIN ); ?>'>
									</div>
								</div>
								<div class="admin-settings-map-container">
									<div id="admin_setting_map"></div>
									<p class="description"><?php _e( 'Changes in the Map Settings will be reflected on the map and vice versa.', WP_MAPIT_TEXTDOMAIN ); ?></p>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
<?php
			}

			/**
		     * Hook to be called when 'admin_init' action is called by wordpress.
		     * Handles saving of the settings
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function admin_init() {
				if( isset( $_POST['wp_mapit_settings_nonce'] ) && wp_verify_nonce( $_POST['wp_mapit_settings_nonce'], 'wp_mapit_settings' ) ) {
					update_option( 'wpmi_map_type', ( isset( $_POST['wpmi_map_type'] ) ? $_POST['wpmi_map_type'] : '' ) );
					update_option( 'wpmi_latitude', ( isset( $_POST['wpmi_latitude'] ) ? $_POST['wpmi_latitude'] : '' ) );
					update_option( 'wpmi_longitude', ( isset( $_POST['wpmi_longitude'] ) ? $_POST['wpmi_longitude'] : '' ) );
					update_option( 'wpmi_map_width', ( isset( $_POST['wpmi_map_width'] ) ? $_POST['wpmi_map_width'] : '' ) );
					update_option( 'wpmi_map_width_type', ( isset( $_POST['wpmi_map_width_type'] ) ? $_POST['wpmi_map_width_type'] : '' ) );
					update_option( 'wpmi_map_height', ( isset( $_POST['wpmi_map_height'] ) ? $_POST['wpmi_map_height'] : '' ) );
					update_option( 'wpmi_map_height_type', ( isset( $_POST['wpmi_map_height_type'] ) ? $_POST['wpmi_map_height_type'] : '' ) );
					update_option( 'wpmi_map_zoom', ( isset( $_POST['wpmi_map_zoom'] ) ? $_POST['wpmi_map_zoom'] : '' ) );
					update_option( 'wpmi_map_marker', ( isset( $_POST['wpmi_map_marker'] ) ? $_POST['wpmi_map_marker'] : '' ) );
					update_option( 'wpmi_allowed_post_types', ( ( isset( $_POST['wpmi_allowed_post_types'] ) && is_array( $_POST['wpmi_allowed_post_types'] ) ) ? $_POST['wpmi_allowed_post_types'] : array() ) );
					update_option( 'wpmi_map_position', ( isset( $_POST['wpmi_map_position'] ) ? $_POST['wpmi_map_position'] : '' ) );

					wp_redirect( admin_url( 'admin.php?page=wp_mapit&info=s' ) );
					die;
				}
			}

			/**
		     * Returns the allowed map types
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return array Returns array of the allowed map types
		     */
			public static function get_map_types() {
				return self::$mapTypes;
			}

			/**
		     * Returns the allowed map positions
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return array Returns array of the allowed map positions
		     */
			public static function get_map_positions() {
				return self::$mapPostions;
			}

			/**
		     * Returns the allowed post types for which map can be displayed
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return array Returns array of the allowed post types where map can be displayed
		     */
			public static function get_allowed_posttypes() {
				return get_option( 'wpmi_allowed_post_types', array() );
			}

			/**
		     * Returns the default map type for which map
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return string Returns default map type
		     */
			public static function get_map_type() {
				return get_option( 'wpmi_map_type', 'before' );
			}

			/**
		     * Returns the default map zoom for which map
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return int Returns default map zoom
		     */
			public static function get_map_zoom() {
				return get_option( 'wpmi_map_zoom', '1' );
			}

			/**
		     * Returns the default map latitude for which map
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return int Returns default map latitude
		     */
			public static function get_map_latitude() {
				return get_option( 'wpmi_latitude', '0' );
			}

			/**
		     * Returns the default map longitude for which map
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return int Returns default map longitude
		     */
			public static function get_map_longitude() {
				return get_option( 'wpmi_longitude', '0' );
			}

			/**
		     * Returns the default map marker
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return string Returns url of the default map marker
		     */
			public static function get_map_marker() {
				$wpmiMapPin = get_option( 'wpmi_map_marker', '' );
				return ( trim( $wpmiMapPin ) != '' ? $wpmiMapPin : self::$mapMarker );
			}

			/**
		     * Returns the default map width
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return int Returns the default map width
		     */
			public static function get_map_width() {
				return get_option( 'wpmi_map_width', '100' );
			}

			/**
		     * Returns the default map width type
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return string Returns the default map width type
		     */
			public static function get_map_width_type() {
				return get_option( 'wpmi_map__type', 'per' );
			}

			/**
		     * Returns the default map height
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return int Returns the default map height
		     */
			public static function get_map_height() {
				return get_option( 'wpmi_map_height', '300' );
			}

			/**
		     * Returns the default map height type
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return string Returns the default map height type
		     */
			public static function get_map_height_type() {
				return get_option( 'wpmi_map_height_type', 'px' );
			}

			/**
		     * Returns the default map position
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return string Returns the default map position
		     */
			public static function get_map_position() {
				return get_option( 'wpmi_map_position', 'before' );
			}
		}

		/**
		 * Calling init function to activate hooks and filters.
		 */
		wp_mapit_admin_settings::init();

	}