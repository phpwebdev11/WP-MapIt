<?php
	
	if( ! class_exists( 'wp_mapit_map' ) ) {
		/**
		 * Class to generate the map from the settings in the post.
		 */
		class wp_mapit_map
		{
			/**
		     * Get map latitude
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return float Returns the latitude of the map
		     */
			public static function get_map_latitude( $postId ) {
				return trim( get_post_meta( $postId, 'wpmi_map_latitiude', true ) );
			}

			/**
		     * Get map longitude
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return float Returns the longitude of the map
		     */
			public static function get_map_longitude( $postId ) {
				return trim( get_post_meta( $postId, 'wpmi_map_longitude', true ) );
			}

			/**
		     * Get map zoom level
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return float Returns the zoom level of the map
		     */
			public static function get_map_zoom( $postId ) {
				return trim( get_post_meta( $postId, 'wpmi_map_zoom', true ) );
			}

			/**
		     * Get map display position
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the display position of the map
		     */
			public static function get_map_position( $postId = null ) {
				if( $postId == null ) {
					global $post;

					$postId = $post->ID;
				}

				$mapPosition = trim( get_post_meta( $postId, 'wpmi_map_position', true ) );

				return ( ( $mapPosition != '' ) ? $mapPosition : wp_mapit_admin_settings::get_map_position() );
			} 

			/**
		     * Get map type
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the type of the map
		     */
			public static function get_map_type( $postId ) {
				$mapType = trim( get_post_meta( $postId, 'wpmi_map_type', true ) );

				return ( ( $mapType != '' ) ? $mapType : wp_mapit_admin_settings::get_map_type() );
			}

			/**
		     * Get map marker image
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the marker image of the map
		     */
			public static function get_map_marker( $postId ) {
				$mapMarker = trim( get_post_meta( $postId, 'wpmi_marker_image', true ) );

				return ( ( $mapMarker != '' ) ? $mapMarker : wp_mapit_admin_settings::get_map_marker() );
			}

			/**
		     * Get map marker title
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the marker title of the map
		     */
			public static function get_map_title( $postId ) {
				return trim( get_post_meta( $postId, 'wpmi_marker_title', true ) );
			}

			/**
		     * Get map marker content
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the marker content of the map
		     */
			public static function get_map_content( $postId ) {
				return nl2br( trim( get_post_meta( $postId, 'wpmi_marker_content', true ) ) );
			}

			/**
		     * Get map marker url
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the marker url of the map
		     */
			public static function get_map_marker_url( $postId ) {
				return trim( get_post_meta( $postId, 'wpmi_marker_url', true ) );
			}

			/**
		     * Get map width
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return int Returns the width of the map
		     */
			public static function get_map_width( $postId ) {
				$width = trim( wp_mapit_admin_settings::get_map_width() );
				return ( ( $width != '' && intval( $width ) > 0 ) ? $width : 300 );
			}

			/**
		     * Get map width type
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the width type of the map
		     */
			public static function get_map_width_type( $postId ) {
				$widthType = trim( wp_mapit_admin_settings::get_map_width_type() );
				return ( ( in_array( $widthType, array( 'px', 'per' ) ) ? $widthType : 'per' ) );
			}

			/**
		     * Get map height
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return int Returns the height of the map
		     */
			public static function get_map_height( $postId ) {
				$height = trim( wp_mapit_admin_settings::get_map_height() );
				return ( ( $height != '' && intval( $height ) > 0 ) ? $height : 300 );
			}

			/**
		     * Get map height type
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the height type of the map
		     */
			public static function get_map_height_type( $postId ) {
				$heightType = trim( wp_mapit_admin_settings::get_map_height_type() );
				return ( ( in_array( $heightType, array( 'px', 'per' ) ) ? $heightType : 'px' ) );
			}

			/**
		     * Checks if map is added or not
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return boolean Returns true if map is added else false, if no id is passed, current post id is considered
		     */
			public static function has_map( $postId = null ) {
				if ( $postId == null ) {
					global $post;

					$postId = $post->ID;
				}

				$lat = self::get_map_latitude( $postId );
				$lng = self::get_map_longitude( $postId );

				return ( ( $lat != '' && $lat != 0 && $lng != '' && $lng != 0 ) ? true : false );
			}

			/**
		     * Function to generate the map by id
		     *
		     * @since 1.0
		     * @static
		     * @access public
		     * @param $postId Int Id of the post
		     * @return string Returns the map's markup as per the map id, if no id is passed, current post id is considered
		     */
			public static function generate_map( $postId = null ) {
				if( $postId == null ) {
					global $post;

					$postId = $post->ID;
				}

				ob_start();

?>
					<div id="wp_mapit_<?php echo wp_mapit_functions::generate_random_string(); ?>" class="wp_mapit_map" data-lat="<?php echo self::get_map_latitude( $postId ); ?>" data-lng="<?php echo self::get_map_longitude( $postId ); ?>" data-zoom="<?php echo self::get_map_zoom( $postId ); ?>" data-type="<?php echo self::get_map_type( $postId ); ?>" data-marker="<?php echo self::get_map_marker( $postId ) ?>" data-title="<?php echo self::get_map_title( $postId ); ?>" data-content="<?php echo htmlentities( self::get_map_content( $postId ) ); ?>" data-url="<?php echo self::get_map_marker_url( $postId ); ?>" data-width="<?php echo self::get_map_width( $postId ); ?>" data-width-type="<?php echo self::get_map_width_type( $postId ); ?>" data-height="<?php echo self::get_map_height( $postId ); ?>" data-height-type="<?php echo self::get_map_height_type( $postId ); ?>"></div>
<?php

				$content = ob_get_clean();

				return $content;
			}
		}
	}