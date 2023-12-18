<?php
/**
 * Post types.
 *
 * @package wp-mapit
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Post_Type' ) ) {
	/**
	 * Manages custom post for the multipin map
	 */
	class Wp_Mapit_Post_Type {
		/**
		 * Add hooks and filters for custom post type
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init() {
			/* Create post types in init */
			add_action(
				'init',
				array(
					__CLASS__,
					'init_hook',
				)
			);

			/* Add admin menu */
			add_action(
				'admin_menu',
				array(
					__CLASS__,
					'admin_menu',
				)
			);

			/* Filter admin panel list columns */
			add_filter(
				'manage_edit-wp_mapit_map_columns',
				array(
					__CLASS__,
					'manage_edit_wp_mapit_map_columns',
				)
			);

			/* Filter admin panel list columns data */
			add_action(
				'manage_wp_mapit_map_posts_custom_column',
				array(
					__CLASS__,
					'manage_wp_mapit_map_posts_custom_column',
				),
				10,
				2
			);
		}

		/**
		 * Handle init hook for the custom post type
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function init_hook() {
			$maps = array(
				'labels'              => array(
					'name'               => __( 'Maps', 'wp-mapit' ),
					'singular_name'      => __( 'Map', 'wp-mapit' ),
					'menu_name'          => __( 'WP MapIt', 'wp-mapit' ),
					'all_items'          => __( 'All Maps', 'wp-mapit' ),
					'add_new'            => __( 'Add Map', 'wp-mapit' ),
					'add_new_item'       => __( 'Add New Map', 'wp-mapit' ),
					'edit_item'          => __( 'Edit Map', 'wp-mapit' ),
					'new_item'           => __( 'New Map', 'wp-mapit' ),
					'view_item'          => __( 'View Map', 'wp-mapit' ),
					'search_items'       => __( 'Search Maps', 'wp-mapit' ),
					'not_found'          => __( 'No maps found', 'wp-mapit' ),
					'not_found_in_trash' => __( 'No maps found in Trash', 'wp-mapit' ),
				),
				'public'              => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'supports'            => array( 'title' ),
				'show_in_menu'        => false,
			);

			register_post_type( 'wp_mapit_map', $maps ); /* phpcs:ignore */
		}

		/**
		 * Hook to manage WP MapIt admin menu
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 */
		public static function admin_menu() {
			add_submenu_page(
				'wp_mapit',
				__( 'Multipin Map', 'wp-mapit' ),
				__( 'Multipin Map', 'wp-mapit' ),
				'manage_options',
				'edit.php?post_type=wp_mapit_map'
			);
		}

		/**
		 * Handle filter for wp_mapit_map columns
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param Array $columns columns for the table.
		 * @return Array Columns for the table as array
		 */
		public static function manage_edit_wp_mapit_map_columns( $columns ) {

			$columns['shortcode'] = __( 'Shortcode', 'wp-mapit' );

			return $columns;
		}

		/**
		 * Handle action to display shortcode in admin list columns
		 *
		 * @since 1.0
		 * @static
		 * @access public
		 * @param String $column Name of the column.
		 * @param Int    $post_id Id of the current post.
		 */
		public static function manage_wp_mapit_map_posts_custom_column( $column, $post_id ) {

			if ( 'shortcode' === $column ) {
				echo esc_html( '[wp_mapit_map id="' . $post_id . '"]' );
			}
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Post_Type::init();
}
