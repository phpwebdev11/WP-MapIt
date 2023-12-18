<?php
/**
 * Class to manage meta box.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Metabox' ) ) {
	/**
	 * Class to manage metaboxes displayed on various pages.
	 */
	class Wp_Mapit_Metabox {
		/**
		 * Define fields to be displayed in the custom metabox.
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			$arr_allowed_post_types = wp_mapit_admin_settings::get_allowed_posttypes();

			$arr_map_types = array();
			$_map_types    = wp_mapit_admin_settings::get_map_types();
			array_walk(
				$_map_types,
				function ( $label, $value ) use ( &$arr_map_types ) {
					$arr_map_types[] = array(
						'value' => $value,
						'label' => $label,
					);
				}
			);

			if ( is_array( $arr_allowed_post_types ) && count( $arr_allowed_post_types ) > 0 ) {
				$arr_map_position = array();
				$_map_positions   = wp_mapit_admin_settings::get_map_positions();
				array_walk(
					$_map_positions,
					function ( $label, $value ) use ( &$arr_map_position ) {
						$arr_map_position[] = array(
							'value' => $value,
							'label' => $label,
						);
					}
				);

				$arr_map_metabox_fields = array(
					array(
						'label' => __( 'Map', 'wp-mapit' ),
						'id'    => 'wp_mapit_map',
						'type'  => 'map',
						'desc'  => __( 'Changes in the map settings will affect the map and vice versa. Once you add a pin, you can drag it to set a correct position.', 'wp-mapit' ),
					),
					array(
						'type'   => 'section',
						'class'  => 'right-margin',
						'label'  => __( 'Map Settings', 'wp-mapit' ),
						'fields' => array(
							array(
								'label' => __( 'Search Location', 'wp-mapit' ),
								'desc'  => __( 'Search for a location to pin it on the map. Do not find what you are looking for? Click on the map to mark the location.', 'wp-mapit' ),
								'id'    => 'wpmi_search',
								'type'  => 'map_search',
							),
							array(
								'label' => __( 'Latitude', 'wp-mapit' ),
								'id'    => 'wpmi_map_latitiude',
								'type'  => 'number',
								'desc'  => __( 'Latitude for the map pin.', 'wp-mapit' ),
								'step'  => 'any',
							),
							array(
								'label' => __( 'Longitude', 'wp-mapit' ),
								'id'    => 'wpmi_map_longitude',
								'type'  => 'number',
								'desc'  => __( 'Longitude for the map pin.', 'wp-mapit' ),
								'step'  => 'any',
							),
							array(
								'label' => __( 'Zoom', 'wp-mapit' ),
								'id'    => 'wpmi_map_zoom',
								'type'  => 'number',
								'desc'  => __( 'Zoom level for the map.', 'wp-mapit' ),
								'min'   => '0',
								'max'   => '20',
								'step'  => '1',
							),
							array(
								'label'     => __( 'Map Type', 'wp-mapit' ),
								'id'        => 'wpmi_map_type',
								'type'      => 'select',
								'options'   => array_merge(
									array(
										array(
											'value' => '',
											'label' => __( 'Default Map Type', 'wp-mapit' ),
										),
									),
									$arr_map_types
								),
								'desc'      => __( 'Type of the map to be displayed.', 'wp-mapit' ),
								'row_class' => 'no-margin',
							),
						),
					),
					array(
						'type'   => 'section',
						'class'  => 'left-margin',
						'label'  => __( 'General Settings', 'wp-mapit' ),
						'fields' => array(
							array(
								'label' => __( 'Marker Image', 'wp-mapit' ),
								'id'    => 'wpmi_marker_image',
								'type'  => 'image',
								'desc'  => __( 'Marker image to be displayed on the map. If no image is selected, default image will be displayed. Max size 100px X 100px. Image bigger then the size will be resized.', 'wp-mapit' ),
							),
							array(
								'label' => __( 'Marker Title', 'wp-mapit' ),
								'id'    => 'wpmi_marker_title',
								'type'  => 'text',
								'desc'  => __( 'Title to be displayed on the map marker.', 'wp-mapit' ),
							),
							array(
								'label'    => __( 'Marker Content', 'wp-mapit' ),
								'id'       => 'wpmi_marker_content',
								'type'     => 'textarea',
								'desc'     => __( 'Content to be displayed on the map marker.', 'wp-mapit' ),
								'sanitize' => 'sanitize_textarea',
							),
							array(
								'label'    => __( 'Marker URL', 'wp-mapit' ),
								'id'       => 'wpmi_marker_url',
								'type'     => 'url',
								'desc'     => __( 'URL which will open on marker click. If Marker URL is entered, Marker Title and Marker Content will be ignored.', 'wp-mapit' ),
								'sanitize' => 'sanitize_textarea',
							),
							array(
								'label'     => __( 'Map Position', 'wp-mapit' ),
								'id'        => 'wpmi_map_position',
								'type'      => 'select',
								'options'   => array_merge(
									array(
										array(
											'value' => '',
											'label' => __( 'Default Map Position', 'wp-mapit' ),
										),
									),
									$arr_map_position
								),
								'desc'      => __( 'Position in the content where the map to be displayed.<br>For custom, use one of the following: <br>1. Shortcode [wp_mapit].<br>2. Sidebar widget<br>3. Gutenberg block "WP MAPIT".', 'wp-mapit' ),
								'row_class' => 'no-margin',
							),
						),
					),
				);

				new wp_mapit_create_metabox( 'wp-mapit-metabox', __( 'WP MapIt', 'wp-mapit' ), $arr_map_metabox_fields, $arr_allowed_post_types, true );
			}

			$arr_map_multi_metabox_shortcode_fields = array(
				array(
					'label'     => __( 'Map Shortcode', 'wp-mapit' ),
					'id'        => 'wpmi_multipin_shortcode',
					'type'      => 'multimap_shortcode',
					'row_class' => 'no-margin',
					'desc'      => __( 'Place the shortcode anywhere you want to display this map.', 'wp-mapit' ),
				),
			);

			new wp_mapit_create_metabox( 'wp-mapit-metabox-shortcode', __( 'WP MapIt Shortcode', 'wp-mapit' ), $arr_map_multi_metabox_shortcode_fields, 'wp_mapit_map', true );

			$arr_map_multi_metabox_map_fields = array(
				array(
					'label'     => __( 'Map', 'wp-mapit' ),
					'id'        => 'wpmi_multipin_map',
					'type'      => 'map',
					'row_class' => 'no-margin',
					'desc'      => __( 'Changes in the map settings will affect the map and vice versa. You can search, drag or zoom the map to set the center of the map.', 'wp-mapit' ),
				),
			);

			new wp_mapit_create_metabox( 'wp-mapit-metabox-map', __( 'WP MapIt Map', 'wp-mapit' ), $arr_map_multi_metabox_map_fields, 'wp_mapit_map', true );

			$arr_map_multi_metabox_settings_fields = array(
				array(
					'type'      => 'section',
					'class'     => 'right-margin',
					'row_class' => 'no-margin',
					'label'     => __( 'General Settings', 'wp-mapit' ),
					'fields'    => array(
						array(
							'label' => __( 'Search Location', 'wp-mapit' ),
							'desc'  => __( 'Search for a location to center the map. Do not find what you are looking for? Drag or zoom the map to set the center.', 'wp-mapit' ),
							'id'    => 'wpmi_multipin_map_search',
							'type'  => 'map_search',
						),
						array(
							'label'   => __( 'Map Type', 'wp-mapit' ),
							'id'      => 'wpmi_multipin_map_type',
							'type'    => 'select',
							'options' => array_merge(
								array(
									array(
										'value' => '',
										'label' => __( 'Default Map Type', 'wp-mapit' ),
									),
								),
								$arr_map_types
							),
							'desc'    => __( 'Type of the map to be displayed.', 'wp-mapit' ),
						),
						array(
							'label'     => __( 'Marker Image', 'wp-mapit' ),
							'id'        => 'wpmi_multipin_map_marker_image',
							'type'      => 'image',
							'desc'      => __( 'Marker image to be displayed on the map. If no image is selected, default image will be displayed. Max size 100px X 100px. Image bigger then the size will be resized.', 'wp-mapit' ),
							'row_class' => 'no-margin',
						),
					),
				),
				array(
					'type'      => 'section',
					'class'     => 'left-margin',
					'row_class' => 'no-margin',
					'label'     => __( 'Map Center', 'wp-mapit' ),
					'fields'    => array(
						array(
							'label' => __( 'Latitude', 'wp-mapit' ),
							'id'    => 'wpmi_multipin_map_latitiude',
							'type'  => 'number',
							'desc'  => __( 'Latitude for the map pin.', 'wp-mapit' ),
							'step'  => 'any',
						),
						array(
							'label' => __( 'Longitude', 'wp-mapit' ),
							'id'    => 'wpmi_multipin_map_longitude',
							'type'  => 'number',
							'desc'  => __( 'Longitude for the map pin.', 'wp-mapit' ),
							'step'  => 'any',
						),
						array(
							'label'     => __( 'Zoom', 'wp-mapit' ),
							'id'        => 'wpmi_multipin_map_zoom',
							'type'      => 'number',
							'desc'      => __( 'Zoom level for the map.', 'wp-mapit' ),
							'min'       => '0',
							'max'       => '20',
							'step'      => '1',
							'row_class' => 'no-margin',
						),
					),
				),
			);

			new wp_mapit_create_metabox( 'wp-mapit-metabox-settings', __( 'WP MapIt Map Settings', 'wp-mapit' ), $arr_map_multi_metabox_settings_fields, 'wp_mapit_map', true );

			$arr_map_multi_metabox_pin_fields = array(
				array(
					'label' => __( 'Map Pin', 'wp-mapit' ),
					'id'    => 'wp_mapit_pins',
					'type'  => 'mappins',
				),
			);

			new wp_mapit_create_metabox( 'wp-mapit-metabox-pins', __( 'WP MapIt Map Pins', 'wp-mapit' ), $arr_map_multi_metabox_pin_fields, 'wp_mapit_map', true );
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Metabox::init();
}
