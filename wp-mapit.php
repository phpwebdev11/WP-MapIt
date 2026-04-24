<?php
/**
 * Plugin Name: WP MapIt
 * Plugin URI: http://wp-mapit.chandnipatel.in
 * Description: WP MapIt is a WordPress plugin to display Open street maps using leaflet on your WordPress site
 * Version: 3.1.0
 * Author: Chandni Patel
 * Author URI: http://chandnipatel.in/
 * Developer: Chandni Patel
 * Developer URI: http://chandnipatel.in/
 * Text Domain: wp-mapit
 * Domain Path: /wp-mapit/languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package wp-mapit
 */

namespace WpMapit;

use WpMapit\Classes\Wp_Mapit;

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

/**
 * Define constants used in the plugin
 */
define( 'WP_MAPIT_DIR', plugin_dir_path( __FILE__ ) . 'wp-mapit/' );
define( 'WP_MAPIT_URL', plugin_dir_url( __FILE__ ) . 'wp-mapit/' );

/* Autoload class files */
spl_autoload_register(
	function ( $class_name ) {
		$class_file = strtolower(
			str_replace(
				array( 'WpMapit\\', '\\', '_' ),
				array( WP_MAPIT_DIR, '/', '-' ),
				$class_name
			)
		) . '.php';

		$pos = strrpos( $class_file, '/' );
		if ( false !== $pos ) {
			$class_file = substr_replace( $class_file, '/class-', $pos, 1 );
		}

		if ( file_exists( $class_file ) ) {
			require_once $class_file;
		}
	}
);

if ( ! function_exists( 'wp_mapit_init' ) ) {

	/**
	 * Function to initialize the plugin.
	 *
	 * @since 1.0
	 * @return class object
	 */
	function wp_mapit_init() {

		/* Initialize the base class of the plugin */
		return Wp_Mapit::instance();
	}
}

/**
 * Create the main object of the plugin when the plugins are loaded
 */
add_action( 'plugins_loaded', 'WpMapit\wp_mapit_init' );

add_action(
	'init',
	function () {
		/* Loading the textdomain */
		load_plugin_textdomain( 'wp-mapit', false, WP_MAPIT_DIR . 'languages' );
	},
	99
);
