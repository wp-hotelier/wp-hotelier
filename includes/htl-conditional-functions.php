<?php
/**
 * Hotelier Conditional Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'is_hotelier' ) ) {
	/**
	 * Returns true if on a page which uses Hotelier templates (booking and listing are standard pages with shortcodes and thus are not included).
	 * @return bool
	 */
	function is_hotelier() {
		return apply_filters( 'is_hotelier', ( is_room_category() || is_room_archive() || is_room() ) ? true : false );
	}
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * Returns true when the page is loaded via ajax.
	 * @return bool
	 */
	function is_ajax() {
		return defined( 'DOING_AJAX' );
	}
}

if ( ! function_exists( 'is_booking' ) ) {

	/**
	 * Returns true when viewing the booking page.
	 * @return bool
	 */
	function is_booking() {
		$page_id = htl_get_page_id( 'booking' );

		return ( $page_id && is_page( $page_id ) ) || htl_post_content_has_shortcode( 'hotelier_booking' ) || apply_filters( 'hotelier_is_booking', false ) ? true : false;
	}
}

if ( ! function_exists( 'is_listing' ) ) {

	/**
	 * Returns true when viewing the listing page (room_list form).
	 * @return bool
	 */
	function is_listing() {
		$page_id = htl_get_page_id( 'listing' );

		return ( $page_id && is_page( $page_id ) ) || htl_post_content_has_shortcode( 'hotelier_listing' ) || apply_filters( 'hotelier_is_listing', false ) ? true : false;
	}
}

if ( ! function_exists( 'is_reservation_received_page' ) ) {

	/**
	* Returns true when viewing the reservation received page.
	* @return bool
	*/
	function is_reservation_received_page() {
		global $wp;

		return ( is_page( htl_get_page_id( 'booking' ) || apply_filters( 'hotelier_is_booking', false ) ) && isset( $wp->query_vars[ 'reservation-received' ] ) ) ? true : false;
	}
}

if ( ! function_exists( 'is_pay_reservation_page' ) ) {

	/**
	* Returns true when viewing the pay reservation page.
	* @return bool
	*/
	function is_pay_reservation_page() {
		global $wp;

		return ( is_page( htl_get_page_id( 'booking' ) || apply_filters( 'hotelier_is_booking', false ) ) && isset( $wp->query_vars[ 'pay-reservation' ] ) ) ? true : false;
	}
}

if ( ! function_exists( 'is_booking_page' ) ) {

	/**
	* Returns true when viewing a booking page (listing and booking).
	* The pay reservation and received page are not included.
	* @return bool
	*/
	function is_booking_page() {
		return ( ( is_booking() || is_listing() ) && ! is_reservation_received_page() && ! is_pay_reservation_page() ) ? true : false;
	}
}

if ( ! function_exists( 'is_room' ) ) {

	/**
	 * Returns true when viewing a single room.
	 * @return bool
	 */
	function is_room() {
		return is_singular( array( 'room' ) );
	}
}

if ( ! function_exists( 'is_room_archive' ) ) {

	/**
	 * Returns true when viewing the room archive.
	 * @return bool
	 */
	function is_room_archive() {
		return is_post_type_archive( 'room' );
	}
}

if ( ! function_exists( 'is_room_category' ) ) {

	/**
	 * Returns true when viewing a room category.
	 * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_room_category( $term = '' ) {
		return is_tax( 'room_cat', $term );
	}
}

if ( ! function_exists( 'is_htl_endpoint_url' ) ) {

	/**
	 * Check if an endpoint is showing
	 * @param  string $endpoint
	 * @return bool
	 */
	function is_htl_endpoint_url( $endpoint = false ) {
		global $wp;

		$htl_endpoints = HTL()->query->get_query_vars();

		if ( $endpoint !== false ) {
			if ( ! isset( $htl_endpoints[ $endpoint ] ) ) {
				return false;
			} else {
				$endpoint_var = $htl_endpoints[ $endpoint ];
			}

			return isset( $wp->query_vars[ $endpoint_var ] );
		} else {
			foreach ( $htl_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}
	}
}

/**
 * Checks whether the content passed contains a specific short code.
 *
 * @param  string $tag Shortcode tag to check.
 * @return bool
 */
function htl_post_content_has_shortcode( $tag = '' ) {
	global $post;

	return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
}
