<?php

	if( ! class_exists( 'wp_mapit_the_content' ) ) {
		/**
		 * Class to filter the_content to show map
		 */
		class wp_mapit_the_content
		{
			/**
		     * Add hooks and filters to display map in the content
		     *
		     * @since 1.0
		     * @access public
		     */
			public static function init() {
				add_filter( 'the_content', __CLASS__ . '::the_content' );
			}

			/**
		     * Function to manage the_content filter to display map
		     *
		     * @since 1.0
		     * @access public
		     * @param $content String content of the page
		     * @return String Returns content including the map as string
		     */
			public static function the_content( $content ){

				/* Check if map is added */
				if( wp_mapit_map::has_map() ) {

					$mapPosition = wp_mapit_map::get_map_position();

					/* If map is to be displayed before or after content, generate map */
					if( in_array( $mapPosition, array( 'before', 'after' ) ) ) {
						$mapContent = wp_mapit_map::generate_map();
						$content = ( ( $mapPosition == 'before' ) ? ( $mapContent . $content ) : ( $content . $mapContent ) );
					}
				}

				return $content;
			}
		}

		/**
		 * Calling init function to activate hooks and filters.
		 */
		wp_mapit_the_content::init();
	}