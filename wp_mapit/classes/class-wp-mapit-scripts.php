<?php
/**
 * Exit if accessed directly
 *
 * @package wp-mapit
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Scripts' ) ) {

	/**
	 * Class to manage the scripts and styles for WP MAPIT
	 */
	class Wp_Mapit_Scripts {
		/**
		 * Add hooks and filters to enqueue scripts and styles needed for WP MAPIT
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			add_action(
				'wp_enqueue_scripts',
				array(
					__CLASS__,
					'wp_enqueue_scripts',
				)
			);

			add_action(
				'admin_enqueue_scripts',
				array(
					__CLASS__,
					'admin_enqueue_scripts',
				)
			);
		}

		/**
		 * Hook to add scripts and styles for WP MAPIT frontend
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function wp_enqueue_scripts() {
			wp_enqueue_style( 'wp-mapit-leaflet-css', WP_MAPIT_URL . 'css/leaflet.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.css' ) );
			wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.responsive.popup.css' ) );
			wp_enqueue_style( 'wp-mapit-leaflet-gesture-handling-css', WP_MAPIT_URL . 'css/leaflet-gesture-handling.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet-gesture-handling.css' ) );
			wp_enqueue_style( 'wp-mapit-leaflet-fullscreen-css', WP_MAPIT_URL . 'css/leaflet.fullscreen.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.fullscreen.css' ) );
			wp_enqueue_style( 'wp-mapit-css', WP_MAPIT_URL . 'css/wp_mapit.css', array(), filemtime( WP_MAPIT_DIR . 'css/wp_mapit.css' ) );

			if ( is_rtl() ) {
				wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-rtl-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.rtl.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.responsive.popup.rtl.css' ) );
			}

			wp_enqueue_script( 'wp-mapit-leaflet-js', WP_MAPIT_URL . 'js/leaflet.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/leaflet.js' ), true );
			wp_enqueue_script( 'wp-mapit-leaflet-responsive-popup-js', WP_MAPIT_URL . 'js/leaflet.responsive.popup.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/leaflet.responsive.popup.js' ), true );
			wp_enqueue_script( 'wp-mapit-leaflet-gesture-handling-js', WP_MAPIT_URL . 'js/leaflet-gesture-handling.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/leaflet-gesture-handling.js' ), true );

			wp_enqueue_script( 'wp-mapit-leaflet-fullscreen-js', WP_MAPIT_URL . 'js/Leaflet.fullscreen.min.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/Leaflet.fullscreen.min.js' ), true );

			wp_enqueue_script( 'wp-mapit-js', WP_MAPIT_URL . 'js/wp_mapit.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/wp_mapit.js' ), true );

			wp_localize_script(
				'wp-mapit-js',
				'wp_mapit',
				array(
					'plugin_attribution' => '<strong>Developed by <a href="http://wp-mapit.chandnipatel.in">WP MAPIT</a></strong> | ',
				)
			);

			wp_enqueue_script( 'wp-mapit-multipin-js', WP_MAPIT_URL . 'js/wp_mapit_multipin.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/wp_mapit_multipin.js' ), true );
		}

		/**
		 * Hook to add scripts and styles for WP MAPIT admin
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function admin_enqueue_scripts() {
			wp_enqueue_media();

			wp_enqueue_style( 'wp-mapit-leaflet-css', WP_MAPIT_URL . 'css/leaflet.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.css' ) );
			wp_enqueue_style( 'wp-mapit-css', WP_MAPIT_URL . 'css/wp_mapit_admin.css', array(), filemtime( WP_MAPIT_DIR . 'css/wp_mapit_admin.css' ) );
			wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.responsive.popup.css' ) );
			wp_enqueue_style( 'wp-mapit-leaflet-gesture-handling-css', WP_MAPIT_URL . 'css/leaflet-gesture-handling.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet-gesture-handling.css' ) );
			wp_enqueue_style( 'wp-mapit-leaflet-fullscreen-css', WP_MAPIT_URL . 'css/leaflet.fullscreen.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.fullscreen.css' ) );

			if ( is_rtl() ) {
				wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-rtl-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.rtl.css', array(), filemtime( WP_MAPIT_DIR . 'css/leaflet.responsive.popup.rtl.css' ) );
			}

			wp_enqueue_script( 'wp-mapit-leaflet-js', WP_MAPIT_URL . 'js/leaflet.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/leaflet.js' ), true );
			wp_enqueue_script( 'wp-mapit-leaflet-responsive-popup-js', WP_MAPIT_URL . 'js/leaflet.responsive.popup.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/leaflet.responsive.popup.js' ), true );
			wp_enqueue_script( 'wp-mapit-leaflet-gesture-handling-js', WP_MAPIT_URL . 'js/leaflet-gesture-handling.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/leaflet-gesture-handling.js' ), true );

			wp_enqueue_script( 'wp-mapit-leaflet-fullscreen-js', WP_MAPIT_URL . 'js/Leaflet.fullscreen.min.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/Leaflet.fullscreen.min.js' ), true );

			wp_enqueue_script( 'wp-mapit-admin-js', WP_MAPIT_URL . 'js/wp_mapit_admin.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/wp_mapit_admin.js' ), true );

			wp_localize_script(
				'wp-mapit-admin-js',
				'wp_mapit',
				array(
					'choose_image_text'  => __( 'Choose Image', 'wp-mapit' ),
					'ajax_error_message' => __( 'Oops! Something went wrong. Please try again.', 'wp-mapit' ),
					'ajax_url'           => admin_url( 'admin-ajax.php' ),
					'please_wait_text'   => __( 'Please wait', 'wp-mapit' ),
					'search_text'        => __( 'Search', 'wp-mapit' ),
					'plugin_attribution' => '<strong>Developed by <a href="http://wp-mapit.chandnipatel.in">WP MAPIT</a></strong> | ',
					'ajax_nonce'         => wp_create_nonce( 'wp_mapit_admin_ajax_nonce' ),

				)
			);

			wp_enqueue_script( 'wp-mapit-admin-settings-js', WP_MAPIT_URL . 'js/wp_mapit_admin_settings.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/wp_mapit_admin_settings.js' ), true );

			global $post;

			if ( ! empty( $post->post_type ) && 'wp_mapit_map' === $post->post_type ) {
				wp_enqueue_script( 'wp-mapit-admin-multipin-js', WP_MAPIT_URL . 'js/wp_mapit_admin_multipin.js', array( 'jquery' ), filemtime( WP_MAPIT_DIR . 'js/wp_mapit_admin_multipin.js' ), true );

				wp_localize_script(
					'wp-mapit-admin-multipin-js',
					'wp_mapit_multipin',
					array(
						'search_map_text'         => __( 'Search Map', 'wp-mapit' ),
						'latitude_text'           => __( 'Latitude', 'wp-mapit' ),
						'longitude_text'          => __( 'Longitude', 'wp-mapit' ),
						'marker_image_text'       => __( 'Marker Image', 'wp-mapit' ),
						'choose_image_text'       => __( 'Choose Image', 'wp-mapit' ),
						'remove_image_text'       => __( 'Remove Image', 'wp-mapit' ),
						'marker_title_text'       => __( 'Marker Title', 'wp-mapit' ),
						'marker_url_text'         => __( 'Marker URL', 'wp-mapit' ),
						'marker_content_text'     => __( 'Marker Content', 'wp-mapit' ),
						'map_text'                => __( 'Map', 'wp-mapit' ),
						'remove_pin_text'         => __( 'Remove Pin', 'wp-mapit' ),
						'remove_pin_confirm_text' => __( 'Are you sure you want to remove the pin?', 'wp-mapit' ),
					)
				);
			}
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Scripts::init();
}
