<?php
/**
 * Hotelier AJAX Room Booking Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Apply coupon.
 */
function hotelier_ajax_action_room_booking() {
	if ( isset( $_POST[ 'ajax_room_booking_nonce' ] ) ) {
		// Check nonce
		if ( ! wp_verify_nonce( $_POST[ 'ajax_room_booking_nonce' ], 'hotelier-ajax-room-booking-nonce' ) ) {
			// Invalid nonce
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid nonce.', 'wp-hotelier' )
				)
			);
		}

		$room_id = isset( $_POST[ 'room_id' ] ) ? absint( $_POST[ 'room_id' ] ) : false;

		if ( ! $room_id > 0 ) {
			// Invalid room ID
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Invalid room ID.', 'wp-hotelier' )
				)
			);
		}

		// Get the room
		$room = htl_get_room( $room_id );

		if ( ! $room->exists() ) {
			// Oops, check failed so throw an error (this this room does not exists)
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Sorry, this room does not exists.', 'wp-hotelier' )
				)
			);
		}

		$form_data = array();
		parse_str( $_POST['form_data'], $form_data );

		// Get form data
		$checkin              = isset( $form_data['checkin'] ) && $form_data['checkin'] ? $form_data['checkin'] : false;
		$checkout             = isset( $form_data['checkout'] ) && $form_data['checkout'] ? $form_data['checkout'] : false;
		$quantity             = isset( $form_data['quantity'] ) && $form_data['quantity'] ? absint( $form_data['quantity'] ) : 1;
		$adults               = isset( $form_data['adults'] ) && $form_data['adults'] ? absint( $form_data['adults'] ) : 1;
		$children             = isset( $form_data['children'] ) && $form_data['children'] ? absint( $form_data['children'] ) : 0;
		$show_rate_desc       = isset( $form_data['show_rate_desc'] ) && $form_data['show_rate_desc'] ? true : false;
		$show_room_conditions = isset( $form_data['show_room_conditions'] ) && $form_data['show_room_conditions'] ? true : false;
		$show_room_deposit    = isset( $form_data['show_room_deposit'] ) && $form_data['show_room_deposit'] ? true : false;

		// Check if we are booking
		$is_doing_booking = isset( $_POST[ 'is_available' ] ) && $_POST[ 'is_available' ] === 'true' ? true : false;

		if ( $is_doing_booking ) {
			$can_redirect = false;
			$redirect_url = '';

			// Book the room
			$redirect_response = HTL_Form_Functions::add_to_cart_from_ajax( $room_id, $checkin, $checkout, $quantity, $form_data );

			if ( isset( $redirect_response['added_to_cart'] ) && $redirect_response['added_to_cart'] ) {
				$can_redirect = true;
				$redirect_url = isset( $redirect_response['redirect_url'] ) && $redirect_response['redirect_url'] ? $redirect_response['redirect_url'] : $redirect_url;
			} else {
				$error_message = isset( $redirect_response['error'] ) ? $redirect_response['error'] : esc_html__( 'We were unable to process your reservation, please try again.', 'wp-hotelier' );

				// Unable to ad the room to the cart
				wp_send_json_error(
					array(
						'message'   => $error_message,
					)
				);
			}

			wp_send_json_success(
				array(
					'is_doing_booking' => $is_doing_booking,
					'can_redirect'     => $can_redirect,
					'redirect_url'     => $redirect_url

				)
			);
		} else {
			if ( HTL_Formatting_Helper::is_valid_checkin_checkout( $checkin, $checkout ) ) {
				// Empty cart
				htl_empty_cart();

				// Set checkin/checkout dates
				HTL()->session->set( 'checkin', $checkin );
				HTL()->session->set( 'checkout', $checkout );
			} else {
				// Invalid dates
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Sorry, this room is not available on the given dates.', 'wp-hotelier' )
					)
				);
			}

			// Check if the room is available
			$is_available_with_reason = $room->is_available_with_reason( $checkin, $checkout, $quantity );
			$is_available = isset( $is_available_with_reason['is_available'] ) && $is_available_with_reason['is_available'] ? true : false;

			if ( ! $is_available ) {
				// Not available
				$reason = isset( $is_available_with_reason['reason'] ) && $is_available_with_reason['reason'] ? $is_available_with_reason['reason'] : esc_html__( 'Sorry, this room is not available on the given dates.', 'wp-hotelier' );

				wp_send_json_error(
					array(
						'message' => $reason
					)
				);
			}

			ob_start();

			htl_get_template( 'widgets/ajax-room-booking/ajax-room-booking-result.php', array(
				'room'                 => $room,
				'checkin'              => $checkin,
				'checkout'             => $checkout,
				'show_rate_desc'       => $show_rate_desc,
				'show_room_conditions' => $show_room_conditions,
				'show_room_deposit'    => $show_room_deposit,
			) );

			$html = ob_get_clean();

			wp_send_json_success(
				array(
					'available' => true,
					'html'      => $html,
				)
			);
		}
	} else {
		// Invalid data
		wp_send_json_error(
			array(
				'message' => esc_html__( 'Invalid or empty data.', 'wp-hotelier' )
			)
		);
	}
}
