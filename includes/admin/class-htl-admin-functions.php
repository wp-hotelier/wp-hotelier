<?php
/**
 * Hotelier Admin Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Functions' ) ) :

/**
 * HTL_Admin_Functions Class
 */
class HTL_Admin_Functions {
	/**
	 * Get all Hotelier screen ids.
	 */
	public static function get_screen_ids() {
		$prefix = self::get_prefix_screen_id();

		$screen_ids   = array(
			'toplevel_page_hotelier-settings',
			$prefix . '_hotelier-logs',
			$prefix . '_hotelier-add-reservation',
			$prefix . '_hotelier-calendar',
			'edit-room',
			'room',
			'room_reservation',
			'edit-room_reservation',
			'edit-room_facilities',
			'coupon',
			'edit-coupon',
			'extra',
			'edit-extra',
		);

		return apply_filters( 'hotelier_screen_ids', $screen_ids );
	}

	/**
	 * Helper to get the screen ID in case "Hotelier" is translated.
	 *
	 * See https://core.trac.wordpress.org/ticket/18857
	 */
	public static function get_prefix_screen_id() {

	    $prefix = sanitize_title( __( 'Hotelier', 'wp-hotelier' ) );
	    return $prefix . '_page';
	}

	/**
	 * Create a page and store the ID in an option.
	 *
	 * @param mixed $key Key of the new page
	 * @param mixed $slug Slug for the new page
	 * @param string $option Option name to store the page's ID
	 * @param string $page_title (default: '') Title for the new page
	 * @param string $page_content (default: '') Content for the new page
	 * @param int $post_parent (default: 0) Parent for the new page
	 * @return int page ID
	 */
	public static function create_page( $key, $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value     = get_option( $option );
		$page_found_trash = false;

		if ( $option_value > 0 && ( $page_object = get_post( $option_value ) ) ) {
			if ( 'trash' != $page_object->post_status ) {
				return -1;
			} else {
				$page_found_trash = true;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_name = %s LIMIT 1;", $slug ) );
		}

		$page_found = apply_filters( 'hotelier_create_page_id', $page_found, $slug, $page_content );


		if ( $page_found && ! $page_found_trash ) {
			if ( ! $option_value ) {
				update_option( $option, $page_found );

				// Set the page in hotelier_settings
				htl_update_option( $key . '_page', $page_found );
			}

			return $page_found;
		}
		elseif ( ! $page_found && $page_found_trash ) {
			// Page was found in trash but it did not have the correct shortcode (so just recreate it)
			$page_found_trash = false;
		}

		if ( ! $page_found_trash ) {
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => wp_strip_all_tags( $page_title ),
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed'
			);
			$page_id   = wp_insert_post( $page_data );
		} else {
			$page_data = array(
				'ID'             => $page_found,
				'post_status'    => 'publish',
			);
			$page_id = wp_update_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );

			// Set the page in hotelier_settings
			htl_update_option( $key . '_page', $page_id );
		}

		return $page_id;
	}
}

endif;
