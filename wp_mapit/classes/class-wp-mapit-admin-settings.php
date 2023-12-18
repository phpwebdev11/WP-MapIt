<?php
/**
 * Class to manage admin settings.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Admin_Settings' ) ) {

	/**
	 * Class to manage admin settings for WP MapIt.
	 */
	class Wp_Mapit_Admin_Settings {

		/**
		 * Map types.
		 *
		 * @since 1.0
		 * @var map_types to manage the map types
		 * @access private
		 */
		private static $map_types = array(
			'normal'      => 'Normal',
			'grayscale'   => 'Grayscale',
			'topographic' => 'Topographic',
		);

		/**
		 * Map positions.
		 *
		 * @since 1.0
		 * @var map_postions to manage the map positions
		 * @access private
		 */
		private static $map_postions = array(
			'before' => 'Before Content',
			'after'  => 'After Content',
			'none'   => 'Custom',
		);

		/**
		 * Map size type.
		 *
		 * @since 1.0
		 * @var map_size_type to manage the map size type
		 * @access private
		 */
		private static $map_size_type = array(
			'px'  => 'px',
			'per' => '%',
		);

		/**
		 * Map marker.
		 *
		 * @since 1.0
		 * @var map_marker to manage the default map pin
		 * @access private
		 */
		private static $map_marker = WP_MAPIT_URL . 'images/map-pin.png';

		/**
		 * Add hooks and filters for admin menu
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			/* Add admin menu */
			add_action(
				'admin_menu',
				array(
					__CLASS__,
					'admin_menu',
				)
			);

			/* Add init hook */
			add_action(
				'admin_init',
				array(
					__CLASS__,
					'admin_init',
				)
			);

			/* Change menu order */
			add_filter(
				'custom_menu_order',
				array(
					__CLASS__,
					'custom_menu_order',
				)
			);
		}

		/**
		 * Hook to manage WP MapIt admin menu
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function admin_menu() {
			add_menu_page(
				__( 'WP MapIt', 'wp-mapit' ),
				__( 'WP MapIt', 'wp-mapit' ),
				'manage_options',
				'wp_mapit',
				array(
					__CLASS__,
					'wp_mapit_settings',
				),
				'dashicons-location-alt'
			);

			add_submenu_page(
				'wp_mapit',
				__( 'Settings', 'wp-mapit' ),
				__( 'Settings', 'wp-mapit' ),
				'manage_options',
				'wp_mapit',
				array(
					__CLASS__,
					'wp_mapit_settings',
				)
			);
		}

		/**
		 * Hook to manage WP MapIt custom menu order.
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 *
		 * @param Array $menu_ord Menu order as array.
		 * @return Array Menu order as array.
		 */
		public static function custom_menu_order( $menu_ord ) {

			global $submenu;

			if ( isset( $submenu['wp_mapit'] ) && isset( $submenu['wp_mapit'][0] ) ) {
				$submenu['wp_mapit'][99] = $submenu['wp_mapit'][0]; /* phpcs:ignore */
				unset( $submenu['wp_mapit'][0] );
			}

			return $menu_ord;
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
								<li><?php esc_html_e( 'Support:', 'wp-mapit' ); ?> <a href="mailto:wp-mapit@chandnipatel.in">wp-mapit@chandnipatel.in</a></li>
								<li><a href="https://www.paypal.me/chandnipatel11" target="_blank"><?php esc_html_e( 'Donate', 'wp-mapit' ); ?></a></li>
							</ul>
						</div>
						
						<?php
						if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'settings_submit' ) && isset( $_REQUEST['info'] ) && 's' === sanitize_text_field( wp_unslash( $_REQUEST['info'] ) ) ) {
							?>
								<div class="updated notice is-dismissible">
									<p><strong><?php esc_html_e( 'Settings saved.', 'wp-mapit' ); ?></strong></p>
									<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'wp-mapit' ); ?></span></button>
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
										<label><?php esc_html_e( 'Map Settings', 'wp-mapit' ); ?></label>
										<div class="wp-mapit-row">
											<label for="wpmi_map_type"><?php esc_html_e( 'Map Type', 'wp-mapit' ); ?></label>
											<select name="wpmi_map_type" id="wpmi_map_type">
												<?php
												$selected_map_type = self::get_map_type();
												foreach ( self::$map_types as $map_type_id => $map_type ) {
													?>
														<option value="<?php echo esc_html( $map_type_id ); ?>" <?php echo esc_attr( $selected_map_type === $map_type_id ? "selected='selected'" : '' ); ?>><?php echo esc_html( $map_type ); ?></option>
													<?php
												}
												?>
											</select>
											<p class="description"><?php esc_html_e( 'Type of the map to be used.', 'wp-mapit' ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_latitude"><?php esc_html_e( 'Latitude', 'wp-mapit' ); ?></label>
											<input type="number" name="wpmi_latitude" id="wpmi_latitude" step="any" value="<?php echo esc_html( self::get_map_latitude() ); ?>">
											<p class="description"><?php esc_html_e( 'Default latitude for the map.', 'wp-mapit' ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_longitude"><?php esc_html_e( 'Longitude', 'wp-mapit' ); ?></label>
											<input type="number" name="wpmi_longitude" id="wpmi_longitude" step="any" value="<?php echo esc_html( self::get_map_longitude() ); ?>">
											<p class="description"><?php esc_html_e( 'Default longitude for the map.', 'wp-mapit' ); ?></p>
										</div>
										<div class="wp-mapit-row no-margin">
											<label for="wpmi_map_zoom"><?php esc_html_e( 'Zoom', 'wp-mapit' ); ?></label>
											<input type="number" name="wpmi_map_zoom" id="wpmi_map_zoom" step="0.01" min="1" max="20" value="<?php echo esc_html( self::get_map_zoom() ); ?>">
											<p class="description"><?php esc_html_e( 'Default zoom for the map.', 'wp-mapit' ); ?></p>
										</div>
									</div>
									
									<div class="wp-mapit-row">
										<label><?php esc_html_e( 'General Settings', 'wp-mapit' ); ?></label>
										<div class="wp-mapit-row">
											<label for="wpmi_map_width"><?php esc_html_e( 'Map Width', 'wp-mapit' ); ?></label>
											<div class="wp-mapit-size">
												<input type="number" name="wpmi_map_width" id="wpmi_map_width" value="<?php echo esc_html( self::get_map_width() ); ?>">
												<select name="wpmi_map_width_type" id="wpmi_map_width_type">
													<?php
													$selected_map_width_type = self::get_map_width_type();
													foreach ( self::$map_size_type as $map_size_type_id => $map_size_type_name ) {
														?>
															<option value="<?php echo esc_html( $map_size_type_id ); ?>" <?php echo esc_attr( $selected_map_width_type === $map_size_type_id ? "selected='selected'" : '' ); ?>><?php echo esc_html( $map_size_type_name ); ?></option>
														<?php
													}
													?>
												</select>
											</div>
											<p class="description"><?php esc_html_e( 'Map width', 'wp-mapit' ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_map_height"><?php esc_html_e( 'Map Height', 'wp-mapit' ); ?></label>
											<div class="wp-mapit-size">
												<input type="number" name="wpmi_map_height" id="wpmi_map_height" value="<?php echo esc_html( self::get_map_height() ); ?>">
												<select name="wpmi_map_height_type" id="wpmi_map_height_type">
													<?php
													$selected_map_height_type = self::get_map_height_type();
													foreach ( self::$map_size_type as $map_size_type_id => $map_size_type_name ) {
														?>
															<option value="<?php echo esc_html( $map_size_type_id ); ?>" <?php echo esc_attr( $selected_map_height_type === $map_size_type_id ? "selected='selected'" : '' ); ?>><?php echo esc_html( $map_size_type_name ); ?></option>
														<?php
													}
													?>
												</select>
											</div>
											<p class="description"><?php esc_html_e( 'Map height', 'wp-mapit' ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label for="wpmi_map_marker"><?php esc_html_e( 'Map Marker', 'wp-mapit' ); ?></label>
											<?php
												$wpmi_map_pin = self::get_map_marker();
											?>
											<div class="wp_mapit_image_upload">
												<img src="<?php echo esc_url( $wpmi_map_pin ); ?>" alt="<?php esc_html_e( 'Map Pin', 'wp-mapit' ); ?>" width="24px">
												<a href="#" id="upload_map_pin" class="button"><?php esc_html_e( 'Choose Map Pin', 'wp-mapit' ); ?></a>
												<a href="#" id="reset_map_pin" class="button" data-default-pin="<?php echo esc_url( self::$map_marker ); ?>"><?php esc_html_e( 'Reset Map Pin', 'wp-mapit' ); ?></a>
												<input type="hidden" name="wpmi_map_marker" id="wpmi_map_marker" value="<?php echo esc_url( $wpmi_map_pin ); ?>">
											</div>
											<p class="description"><?php esc_html_e( 'Default marker for the map. Max size 100px X 100px. Image bigger then the size will be resized.', 'wp-mapit' ); ?></p>
										</div>
										<div class="wp-mapit-row">
											<label><?php esc_html_e( 'Allow map for', 'wp-mapit' ); ?></label>
											<?php
											$allowed_post_types = get_option( 'wpmi_allowed_post_types', array() );

											$arr_post_types = get_post_types(
												array(
													'public' => true,
												)
											);

											$arr_exclude_post_types = apply_filters( 'wp_mapitesc_html_exclude_post_types', array( 'wp_mapit_map', 'attachment' ) );

											if ( is_array( $arr_post_types ) && count( $arr_post_types ) > 0 ) {
												?>
													<ul>
														<?php
														foreach ( $arr_post_types as $post_type ) {
															if ( ! in_array( $post_type, $arr_exclude_post_types, true ) ) {
																?>
																	<li><input type="checkbox" name="wpmi_allowed_post_types[]" id="wpmi_allowed_post_type_<?php echo esc_attr( $post_type ); ?>" value="<?php echo esc_html( $post_type ); ?>" <?php echo esc_attr( in_array( $post_type, $allowed_post_types, true ) ? 'checked=checked' : '' ); ?>><?php echo esc_html( $post_type ); ?></li>
																<?php
															}
														}
														?>
													</ul>
												<?php
											}
											?>
											<p class="description"><?php esc_html_e( 'Select where the map can be displayed.', 'wp-mapit' ); ?></p>
										</div>
										<div class="wp-mapit-row no-margin">
											<label for="wpmi_map_position"><?php esc_html_e( 'Show Map', 'wp-mapit' ); ?></label>
											<select name="wpmi_map_position" id="wpmi_map_position">
												<?php
												$selected_map_position = self::get_map_position();
												foreach ( self::$map_postions as $map_position_id => $map_position ) {
													?>
														<option value="<?php echo esc_html( $map_position_id ); ?>" <?php echo esc_attr( $selected_map_position === $map_position_id ? 'selected=selected' : '' ); ?>><?php echo esc_html( $map_position, ); ?></option>
													<?php
												}
												?>
											</select>
											<p class="description"><?php echo wp_kses_post( 'Position where the map needs to be displayed.<br>For custom, use one of the following: <br>1. Shortcode [wp_mapit].<br>2. Sidebar widget<br>3. Gutenberg block "WP MAPIT".', 'wp-mapit' ); ?></p>
										</div>
									</div>
									<div class="wp-mapit-row button-container">
										<input type='submit' class='button-primary' name='save_settings' id='save_settings' value='<?php esc_html_e( 'Save Settings', 'wp-mapit' ); ?>'>
									</div>
								</form>
							</div>
							<div class="wp-mapit-map">
								<div class="wp-mapit-row no-margin">
									<label><?php esc_html_e( 'Search Location', 'wp-mapit' ); ?></label>
									<div class="wp-mapit-search">
										<input type="text" name="search_map" id="search_map">
										<input type='button' class='button' name='search_map_btn' id='search_map_btn' value='<?php esc_html_e( 'Search', 'wp-mapit' ); ?>'>
									</div>
								</div>
								<div class="admin-settings-map-container">
									<div id="admin_setting_map"></div>
									<p class="description"><?php esc_html_e( 'Changes in the Map Settings will be reflected on the map and vice versa.', 'wp-mapit' ); ?></p>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			<?php
		}

		/**
		 * Hook to be called when 'admin_init' action is called by WordPress.
		 * Handles saving of the settings
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function admin_init() {
			if ( isset( $_POST['wp_mapit_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_mapit_settings_nonce'] ) ), 'wp_mapit_settings' ) ) {
				update_option( 'wpmi_map_type', ( isset( $_POST['wpmi_map_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_type'] ) ) : '' ) );
				update_option( 'wpmi_latitude', ( isset( $_POST['wpmi_latitude'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_latitude'] ) ) : '' ) );
				update_option( 'wpmi_longitude', ( isset( $_POST['wpmi_longitude'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_longitude'] ) ) : '' ) );
				update_option( 'wpmi_map_width', ( isset( $_POST['wpmi_map_width'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_width'] ) ) : '' ) );
				update_option( 'wpmi_map_width_type', ( isset( $_POST['wpmi_map_width_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_width_type'] ) ) : '' ) );
				update_option( 'wpmi_map_height', ( isset( $_POST['wpmi_map_height'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_height'] ) ) : '' ) );
				update_option( 'wpmi_map_height_type', ( isset( $_POST['wpmi_map_height_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_height_type'] ) ) : '' ) );
				update_option( 'wpmi_map_zoom', ( isset( $_POST['wpmi_map_zoom'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_zoom'] ) ) : '' ) );
				update_option( 'wpmi_map_marker', ( isset( $_POST['wpmi_map_marker'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_marker'] ) ) : '' ) );
				update_option( 'wpmi_allowed_post_types', ( ( isset( $_POST['wpmi_allowed_post_types'] ) && is_array( $_POST['wpmi_allowed_post_types'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wpmi_allowed_post_types'] ) ) : array() ) );
				update_option( 'wpmi_map_position', ( isset( $_POST['wpmi_map_position'] ) ? sanitize_text_field( wp_unslash( $_POST['wpmi_map_position'] ) ) : '' ) );

				wp_safe_redirect( wp_nonce_url( admin_url( 'admin.php?page=wp_mapit&info=s' ), 'settings_submit' ) );
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
			return self::$map_types;
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
			return self::$map_postions;
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
			$wpmi_map_pin = get_option( 'wpmi_map_marker', '' );
			return ( trim( $wpmi_map_pin ) !== '' ? $wpmi_map_pin : self::$map_marker );
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
			return get_option( 'wpmi_map_type', 'per' );
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
	Wp_Mapit_Admin_Settings::init();

}
