<?php

	if( ! class_exists( 'wp_mapit_functions' ) ) {
		/**
		 * CLass to manage common functions of the plugin
		 */
		class wp_mapit_functions
		{
			/**
		     * Function to generate a random string
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $length Int Length of the string
		     * @return string Returns a random string
		     */
			public static function generate_random_string($length = 10) {
			    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
			}
		}
	}