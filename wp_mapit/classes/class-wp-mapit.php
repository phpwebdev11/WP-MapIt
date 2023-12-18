<?php
/**
 * Base class of the plugin.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit' ) ) {

	/**
	 * Base class of the plugin
	 */
	class Wp_Mapit {
		/**
		 * Instance fo the plugin.
		 *
		 * @since 1.0
		 * @var Wp_Mapit the single instance of the class
		 */
		protected static $instance = null;

		/**
		 * Instantiates the plugin and include all the files needed for the plugin.
		 *
		 * @since 1.0
		 * @access public
		 */
		public function __construct() {
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
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-admin-settings.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-scripts.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-create-metabox.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-metabox.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-admin-ajax.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-functions.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-map.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-multipin-map.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-the-content.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-shortcode.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-gutenberg-block.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-contextual-map-widget.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-widget.php';
			require_once WP_MAPIT_DIR . 'classes/class-wp-mapit-post-type.php';
		}
	}
}
