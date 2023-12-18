<?php
/**
 * Plugin Name: WP MapIt
 * Plugin URI: http://wp-mapit.chandnipatel.in
 * Description: WP MapIt is a WordPress plugin to display Open street maps using leaflet on your WordPress site
 * Version: 3.0.0
 * Author: Chandni Patel
 * Author URI: http://chandnipatel.in/
 * Developer: Chandni Patel
 * Developer URI: http://chandnipatel.in/
 * Text Domain: wp-mapit
 * Domain Path: wp_mapit/languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

/**
 * Define constants used in the plugin
 */
define( 'WP_MAPIT_DIR', plugin_dir_path( __FILE__ ) . 'wp_mapit/' );
define( 'WP_MAPIT_URL', plugin_dir_url( __FILE__ ) . 'wp_mapit/' );

/**
 * Include the core file of the plugin
 */
require_once WP_MAPIT_DIR . 'classes/class-wp-mapit.php';

if ( ! function_exists( 'wp_mapit_init' ) ) {

	/**
	 * Function to initialize the plugin.
	 *
	 * @since 1.0
	 * @return class object
	 */
	function wp_mapit_init() {

		/* Loading the textdomain */
		load_plugin_textdomain( 'wp-mapit', false, basename( dirname( __DIR__ ) ) . '/wp_mapit/languages' );

		/* Initialize the base class of the plugin */
		return Wp_Mapit::instance();
	}
}

/**
 * Create the main object of the plugin when the plugins are loaded
 */
add_action( 'plugins_loaded', 'wp_mapit_init' );
