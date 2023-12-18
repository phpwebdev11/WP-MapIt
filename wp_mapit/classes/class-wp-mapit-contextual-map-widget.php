<?php
/**
 * Contextual map widget.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Contextual_Map_Widget' ) ) {
	/**
	 * Class to create a widget to display the map from the current page, post or custom post type
	 */
	class Wp_Mapit_Contextual_Map_Widget extends WP_Widget {
		/**
		 * Constructor of the class, called when the object is initiated.
		 *
		 * @since 1.0
		 * @access public
		 */
		public function __construct() {
			/* Call the parent constructor */
			parent::__construct( 'wp_mapit_contextual_map_widget', __( 'WP MAPIT', 'wp-mapit' ), array( 'description' => __( 'Show map for the current page, post or custom post types.', 'wp-mapit' ) ) );
		}

		/**
		 * Function to display the widget on the frontend.
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 * @param Array $args Arguments of the widget.
		 * @param Array $instance Instance of the widget.
		 */
		public function widget( $args, $instance ) {
			$title = apply_filters( 'widget_title', ( isset( $instance['title'] ) ? $instance['title'] : '' ) );

			echo wp_kses_post( isset( $args['before_widget'] ) ? $args['before_widget'] : '' );

			if ( ! empty( $title ) ) {
				echo wp_kses_post( ( isset( $args['before_title'] ) ? $args['before_title'] : '' ) . $title . ( isset( $args['after_title'] ) ? $args['after_title'] : '' ) );
			}

			echo do_shortcode( '[wp_mapit]' );

			echo wp_kses_post( isset( $args['after_widget'] ) ? $args['after_widget'] : '' );
		}

		/**
		 * Function to display form in the admin panel widget area
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 * @param Array $instance Instance of the widget.
		 */
		public function form( $instance ) {
			$title = ( isset( $instance['title'] ) ? $instance['title'] : '' );

			$title_id = $this->get_field_id( 'title' );
			?>
				<p>
					<label for="<?php echo esc_html( $title_id ); ?>"></label>
					<input class="widefat" type="text" name="<?php echo esc_html( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $title_id ); ?>" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php esc_html_e( 'Enter a title', 'wp-mapit' ); ?>">
				</p>
			<?php
		}

		/**
		 * Function to save widget fields from admin panel widget area.
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 * @param Array $new_instance New instance of the widget.
		 * @param Array $old_instance Old instance of the widget.
		 * @return Array Instance of the widget
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title'] = ( isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '' );

			return $instance;
		}
	}
}
