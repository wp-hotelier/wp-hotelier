<?php
/**
 * Room List Shortcode Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Shortcodes
 * @package  Hotelier/Classes
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Shortcode_Room_List' ) ) :

/**
 * HTL_Shortcode_Room_List Class
 */
class HTL_Shortcode_Room_List {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return HTL_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		self::room_list( $atts );
	}

	/**
	 * Show the room list form
	 */
	private static function room_list( $atts ) {
		$checkin  = HTL()->session->get( 'checkin' ) ? HTL()->session->get( 'checkin' ) :  null;
		$checkout = HTL()->session->get( 'checkout' ) ? HTL()->session->get( 'checkout' ) : null;

		// Reset coupon ID
		HTL()->session->set( 'coupon_id', null );

		// Check if we have valid dates before to run the query
		if ( ! HTL_Formatting_Helper::is_valid_checkin_checkout( $checkin, $checkout ) ) {
			htl_get_template( 'room-list/no-rooms-available.php' );
		} else {
			$room_id           = isset( $_GET[ 'room-id' ] ) ? absint( $_GET[ 'room-id' ] ) : false;
			$room_id_available = false;

			// A room ID was passed to the query so check if it is available
			if ( $room_id ) {
				$_room = htl_get_room( $room_id );

				if ( $_room->exists() && $_room->is_available( $checkin, $checkout ) ) {
					$room_id_available = true;
				}
			}

			// Get available rooms
			$rooms = htl_get_listing_rooms_query( $checkin, $checkout, $room_id );

			// Pass args to the template
			$room_list_args = array(
				'rooms'             => $rooms,
				'room_id'           => $room_id,
				'room_id_available' => $room_id_available,
				'shortcode_atts'    => $atts
			);

			htl_get_template( 'room-list/form-room-list.php', $room_list_args );
		}
	}
}

endif;
