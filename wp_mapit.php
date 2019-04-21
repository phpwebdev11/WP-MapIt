<?php

	/*
	 * Plugin Name: WP MapIt
	 * Plugin URI: http://wp-mapit.phpwebdev.in
	 * Description: WP MapIt is a Wordpress plugin to display Open street maps using leaflet on your Wordpress site
	 * Version: 1.1
	 * Author: Chandni Patel
	 * Author URI: http://phpwebdev.in/
	 * Developer: Chandni Patel
	 * Developer URI: http://phpwebdev.in/
	 * Text Domain: wp-mapit
	 * Domain Path: wp_mapit/languages
	 * License: GNU General Public License v3.0
 	 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
	 */

	/**
	 * Exit if accessed directly
	 */
	if( ! defined( 'ABSPATH' ) ) {
		die('Access Denied');
	}

	/**
	 * Define constants used in the plugin
	 */
	define( 'WP_MAPIT_DIR', plugin_dir_path(__FILE__) . 'wp_mapit/' );
	define( 'WP_MAPIT_URL', plugin_dir_url(__FILE__) . 'wp_mapit/' );
	define( 'WP_MAPIT_TEXTDOMAIN', 'wp-mapit' );

	/**
	 * Include the core file of the plugin
	 */
	require_once(WP_MAPIT_DIR . 'classes/class.wp_mapit.php');

	if(!function_exists('wp_mapit_init')) {

		/**
		 * Function to initialize the plugin.
		 * @since 1.0		
		 * @return class object
		 */
		function wp_mapit_init()
		{
			/* Initialize the base class of the plugin */
			return wp_mapit::instance();
		}
	}

	/**
	 * Create the main object of the plugin when the plugins are loaded
	 */
	add_action('plugins_loaded', 'wp_mapit_init');