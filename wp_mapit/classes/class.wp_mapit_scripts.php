<?php

	if( ! class_exists( 'wp_mapit_scripts' ) ) {

		/**
		 * Class to manage the scripts and styles for WP MAPIT
		 */
		class wp_mapit_scripts
		{
			/**
		     * Add hooks and filters to enqueue scripts and styles needed for WP MAPIT
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function init() {
				add_action( 'wp_enqueue_scripts', __CLASS__ . '::wp_enqueue_scripts' );
				add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
			}

			/**
		     * Hook to add scripts and styles for WP MAPIT frontend
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     */
			public static function wp_enqueue_scripts() {
				wp_enqueue_style( 'wp-mapit-leaflet-css', WP_MAPIT_URL . 'css/leaflet.css' );
				wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.css' );

				if( is_rtl() ) {
					wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-rtl-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.rtl.css' );
				}

				wp_enqueue_script( 'wp-mapit-leaflet-js', WP_MAPIT_URL . 'js/leaflet.js', array( 'jquery' ) );
				wp_enqueue_script( 'wp-mapit-leaflet-responsive-popup-js', WP_MAPIT_URL . 'js/leaflet.responsive.popup.js', array( 'jquery' ) );
				wp_enqueue_script( 'wp-mapit-leaflet-gray-js', WP_MAPIT_URL . 'js/TileLayer.Grayscale.js', array( 'jquery' ) );

				wp_enqueue_script( 'wp-mapit-js', WP_MAPIT_URL . 'js/wp_mapit.js', array( 'jquery' ) );

				wp_localize_script( 'wp-mapit-js', 'wp_mapit', array( 
					'plugin_attribution' => '<strong>Developed by <a href="http://wp-mapit.phpwebdev.in">WP MAPIT</a></strong> | '
				) );
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

				wp_enqueue_style( 'wp-mapit-leaflet-css', WP_MAPIT_URL . 'css/leaflet.css' );
				wp_enqueue_style( 'wp-mapit-css', WP_MAPIT_URL . 'css/wp_mapit_admin.css' );
				wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.css' );

				if( is_rtl() ) {
					wp_enqueue_style( 'wp-mapit-leaflet-responsive-popup-rtl-css', WP_MAPIT_URL . 'css/leaflet.responsive.popup.rtl.css' );
				}

				wp_enqueue_script( 'wp-mapit-leaflet-js', WP_MAPIT_URL . 'js/leaflet.js', array( 'jquery' ) );
				wp_enqueue_script( 'wp-mapit-leaflet-gray-js', WP_MAPIT_URL . 'js/TileLayer.Grayscale.js', array( 'jquery' ) );
				
				wp_enqueue_script( 'wp-mapit-leaflet-responsive-popup-js', WP_MAPIT_URL . 'js/leaflet.responsive.popup.js', array( 'jquery' ) );
				wp_enqueue_script( 'wp-mapit-admin-js', WP_MAPIT_URL . 'js/wp_mapit_admin.js', array( 'jquery' ) );

				wp_localize_script( 'wp-mapit-admin-js', 'wp_mapit', array( 
					'choose_image_text' => __( 'Choose Image', WP_MAPIT_TEXTDOMAIN ),
					'ajax_error_message' => __( 'Oops! Something went wrong. Please try again.', WP_MAPIT_TEXTDOMAIN ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'please_wait_text' => __( 'Please wait', WP_MAPIT_TEXTDOMAIN ),
					'search_text' => __( 'Search', WP_MAPIT_TEXTDOMAIN ),
					'plugin_attribution' => '<strong>Developed by <a href="http://wp-mapit.phpwebdev.in">WP MAPIT</a></strong> | '
				) );

				if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wp_mapit' ) {
					wp_enqueue_script( 'wp-mapit-admin-settings-js', WP_MAPIT_URL . 'js/wp_mapit_admin_settings.js', array( 'jquery' ) );
				}
			}
		}

		/**
		 * Calling init function to activate hooks and filters.
		 */
		wp_mapit_scripts::init();
	}