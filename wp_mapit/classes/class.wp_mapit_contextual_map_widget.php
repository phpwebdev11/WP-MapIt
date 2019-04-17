<?php

	if( ! class_exists( 'wp_mapit_contextual_map_widget' ) ) {
		/**
		 * Class to create a widget to display the map from the current page, post or custom post type
		 */
		class wp_mapit_contextual_map_widget extends WP_Widget
		{
			/**
		     * Constructor of the class, called when the object is initiated.
		     *
		     * @since 1.0
		     * @access public
		     */
			function __construct()
			{
				/* Call the parent constructor */
				parent::__construct( 'wp_mapit_contextual_map_widget', __( 'WP MAPIT', WP_MAPIT_TEXTDOMAIN ), array( 'description' => __( 'Show map for the current page, post or custom post types.', WP_MAPIT_TEXTDOMAIN ) ) );
			}

			/**
		     * Function to display the widget on the frontend.
		     *
		     * @since 1.0
		     * @access public
		     * @static
		     * @param $args Array Arguments of the widget
		     * @param $instance Array Instance of the widget
		     */
			public function widget( $args, $instance ) {
				$title = apply_filters( 'widget_title', ( isset( $instance['title'] ) ? $instance['title'] : '' ) );

				echo ( isset( $args['before_widget'] ) ? $args['before_widget'] : '' );

				if( ! empty( $title ) ) {
					echo ( isset( $args['before_title'] ) ? $args['before_title'] : '' ) . $title . ( isset( $args['after_title'] ) ? $args['after_title'] : '' );
				}

				echo do_shortcode( '[wp_mapit]' );

				echo ( isset( $args['after_widget'] ) ? $args['after_widget'] : '' );
			}

			/**
		     * Function to display form in the admin panel widget area
		     *
		     * @since 1.0
		     * @access public
		     * @static
		     * @param $instance Array Instance of the widget
		     */
			public function form( $instance ) {
				$title = ( isset( $instance['title'] ) ? $instance['title'] : '' );

				$titleId = $this->get_field_id( 'title' );
?>
				<p>
					<label for="<?php echo $titleId; ?>"></label>
					<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $titleId; ?>" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php _e( 'Enter a title', WP_MAPIT_TEXTDOMAIN ); ?>">
				</p>
<?php
			}

			/**
		     * Function to save widget fields from admin panel widget area.
		     *
		     * @since 1.0
		     * @access public
		     * @static
		     * @param $new_instance Array New instance of the widget
		     * @param $old_instance Array Old instance of the widget
		     * @return Array Instance of the widget
		     */
			public function update( $new_instance, $old_instance ) {
				$instance = array();

				$instance['title'] = ( isset( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '' );

				return $instance;
			}
		}
	}