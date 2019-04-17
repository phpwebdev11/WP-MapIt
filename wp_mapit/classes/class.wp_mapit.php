<?php

	if( ! class_exists( 'wp_mapit' ) ) {

		/**
		 * Base class of the plugin
		 */
		class wp_mapit
		{
			/**
		     * @since 1.0
		     * @var wp_mapit the single instance of the class
		     */
			protected static $instance = null;

			/**
		     * Instantiates the plugin and include all the files needed for the plugin.
		     * 
		     * @since 1.0
		     * @access public
		     */
			function __construct() {
				self::include_plugin_files();
			}

			/**
		     * Main WP MapIt Plugin instance
		     *
		     * Ensures only one instance of WP MapIt is loaded or can be loaded.
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @return WP MapIt - Main instance
		     */
			public static function instance() {
				if ( is_null( self::$instance ) ) {
		            self::$instance = new self();
		        }

		        return self::$instance;
			}

			/**
		     * Include all the files needed for the plugin.
		     *
		     * @since 1.0
		     * @static
		     * @access private
		     */
			private static function include_plugin_files() {
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_admin_settings.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_scripts.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_create_metabox.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_metabox.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_admin_ajax.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_functions.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_map.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_the_content.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_shortcode.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_gutenberg_block.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_contextual_map_widget.php' );
				require_once( WP_MAPIT_DIR . 'classes/class.wp_mapit_widget.php' );
			}
		}
	}