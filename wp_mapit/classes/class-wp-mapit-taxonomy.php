<?php
/**
 * Taxonomy for WP MapIt.
 *
 * @package wp-mapit
 */

declare( strict_types = 1 );

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access Denied' );
}

if ( ! class_exists( 'Wp_Mapit_Taxonomy' ) ) {
	/**
	 * Manages taxonomy for WP MapIt Plugin
	 */
	class Wp_Mapit_Taxonomy {
		/**
		 * Add hooks and filters for taxonomy
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 */
		public static function init() {

			/* Register taxonomy in init */
			add_action(
				'init',
				array(
					__CLASS__,
					'register_taxonomy',
				)
			);

            /* Set parent file file for taxonomy list table */
			add_filter(
				'parent_file',
				array(
					__CLASS__,
					'set_parent_file',
				)
			);

            /* Set submenu file for taxonomy list table */
			add_filter(
				'submenu_file',
				array(
					__CLASS__,
					'set_submenu_file',
				)
			);

		}

        /**
		 * Register the taxonomy for WP MapIt Plugin
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 */
		public static function register_taxonomy() {

			$labels = array(
				'name'                       => __( 'Map Tags', 'wp-mapit' ),
				'singular_name'              => __( 'Map Tag', 'wp-mapit' ),
				'search_items'               => __( 'Search Map Tags', 'wp-mapit' ),
				'popular_items'              => __( 'Popular Map Tags', 'wp-mapit' ),
				'all_items'                  => __( 'All Map Tags', 'wp-mapit' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Map Tag', 'wp-mapit' ),
				'update_item'                => __( 'Update Map Tag', 'wp-mapit' ),
				'add_new_item'               => __( 'Add New Map Tag', 'wp-mapit' ),
				'new_item_name'              => __( 'New Map Tag Name', 'wp-mapit' ),
				'separate_items_with_commas' => __( 'Separate map tags with commas', 'wp-mapit' ),
				'add_or_remove_items'        => __( 'Add or remove map tags', 'wp-mapit' ),
				'choose_from_most_used'      => __( 'Choose from the most used map tags', 'wp-mapit' ),
				'not_found'                  => __( 'No map tags found', 'wp-mapit' ),
				'menu_name'                  => __( 'Map Tags', 'wp-mapit' ),
			);

			$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'wp_mapit_tag' ),
				'show_in_rest'          => true,
				'show_in_menu'          => false,
				'meta_box_cb'           => false, // Remove tags meta box from post edit screen.
				'show_in_quick_edit'    => false, // Remove from quick edit.
			);

			// Register the taxonomy for wp_mapit_map post type.
			register_taxonomy( 'wp_mapit_tag', 'wp_mapit_map', $args );
		}

		/**
		 * Set parent file when editing the taxonomy list table.
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 *
		 * @param string $parent_file Current parent file.
		 * @return string Modified parent file.
		 */
		public static function set_parent_file( $parent_file ) {
			$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'wp_mapit_tag' === $taxonomy ) {
				return 'wp_mapit';
			}

			return $parent_file;
		}

		/**
		 * Set submenu file when editing the taxonomy list table.
		 *
		 * @since 3.2.0
		 * @static
		 * @access public
		 *
		 * @param string $submenu_file Current submenu file.
		 * @return string Modified submenu file.
		 */
		public static function set_submenu_file( $submenu_file ) {
			$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'wp_mapit_tag' === $taxonomy ) {
				return 'edit-tags.php?taxonomy=wp_mapit_tag&post_type=wp_mapit_map';
			}

			return $submenu_file;
		}
	}

	/**
	 * Calling init function to activate hooks and filters.
	 */
	Wp_Mapit_Taxonomy::init();
}
