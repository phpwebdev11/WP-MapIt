<?php
/**
 * Base class of the plugin.
 *
 * @package wp-mapit
 */

namespace WpMapit\Classes;

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
			Wp_Mapit_Admin_Settings::init();
			Wp_Mapit_Scripts::init();
			Wp_Mapit_Metabox::init();
			Wp_Mapit_Admin_Ajax::init();
			Wp_Mapit_The_Content::init();
			Wp_Mapit_Shortcode::init();
			Wp_Mapit_Gutenberg_Block::init();
			Wp_Mapit_Widget::init();
			Wp_Mapit_Post_Type::init();
			Wp_Mapit_Blocks::init();
		}
	}
}
