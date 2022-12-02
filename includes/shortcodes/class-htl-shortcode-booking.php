<?php
/**
 * Booking Shortcode Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Shortcodes
 * @package  Hotelier/Classes
 * @version  2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Shortcode_Booking' ) ) :

/**
 * HTL_Shortcode_Booking Class
 */
class HTL_Shortcode_Booking {

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
		global $wp;

		// Check cart class is loaded or abort
		if ( is_null( HTL()->cart ) ) {
			return;
		}

		// Handle booking actions
		if ( ! empty( $wp->query_vars[ 'pay-reservation' ] ) ) {

			self::pay_reservation( $wp->query_vars[ 'pay-reservation' ], $atts );

		} elseif ( isset( $wp->query_vars[ 'reservation-received' ] ) ) {

			self::reservation_received( $wp->query_vars[ 'reservation-received' ], $atts );

		} else {

			self::booking( $atts );

		}
	}

	/**
	 * Show the pay page.
	 *
	 * @param int $reservation_id
	 */
	private static function pay_reservation( $reservation_id, $atts ) {
		echo '<div class="pay-reservation">';

		do_action( 'before_hotelier_pay', $atts );

		htl_print_notices();

		$reservation_id = absint( $reservation_id );

		// Handle payment
		if ( isset( $_GET[ 'pay_for_reservation' ] ) && isset( $_GET[ 'key' ] ) && $reservation_id ) {

			// Pay for existing reservation
			$reservation_key = $_GET[ 'key' ];
			$reservation     = htl_get_reservation( $reservation_id );

			if ( $reservation->id == $reservation_id && $reservation->reservation_key == $reservation_key ) {

				if ( $reservation->needs_payment() ) {

					$available_gateways = HTL()->payment_gateways->get_available_payment_gateways();

					if ( sizeof( $available_gateways ) ) {
						current( $available_gateways )->set_selected();
					}

					$checkin      = $reservation->get_formatted_checkin();
					$checkout     = $reservation->get_formatted_checkout();
					$pets_message = HTL_Info::get_hotel_pets_message();
					$cards        = HTL_Info::get_hotel_accepted_credit_cards();

					htl_get_template( 'booking/reservation-details.php', array(
						'reservation'  => $reservation,
						'checkin'      => $checkin,
						'checkout'     => $checkout,
						'pets_message' => $pets_message,
						'cards'        => $cards,
					) );

					htl_get_template( 'booking/form-pay.php', array(
						'reservation'             => $reservation,
						'available_gateways'      => $available_gateways,
						'reservation_button_text' => apply_filters( 'hotelier_pay_reservation_button_text', esc_html__( 'Pay deposit', 'wp-hotelier' ) )
					) );

				} else {
					htl_add_notice( esc_html__( 'Hi there! Seems that this reservation does not require a deposit. Please contact us if you need assistance.', 'wp-hotelier' ), 'error' );
				}

			} else {
				htl_add_notice( esc_html__( 'Sorry, this reservation is invalid and cannot be paid for.', 'wp-hotelier' ), 'error' );
			}

		} else {
			htl_add_notice( esc_html__( 'Invalid reservation.', 'wp-hotelier' ), 'error' );
		}

		htl_print_notices();

		do_action( 'after_hotelier_pay', $atts );

		echo '</div>';
	}

	/**
	 * Show the reservation received page.
	 *
	 * @param int $reservation_id
	 */
	private static function reservation_received( $reservation_id = 0, $atts = array() ) {

		htl_print_notices();

		$reservation = false;

		// Get the reservation
		$reservation_id  = apply_filters( 'hotelier_received_reservation_id', absint( $reservation_id ) );
		$reservation_key = apply_filters( 'hotelier_received_reservation_key', empty( $_GET[ 'key' ] ) ? '' : sanitize_text_field( $_GET[ 'key' ] ) );

		if ( $reservation_id > 0 ) {
			$reservation = htl_get_reservation( $reservation_id );
			if ( $reservation->reservation_key != $reservation_key )
				$reservation = false;
		}

		// Empty awaiting payment session
		HTL()->session->set( 'reservation_awaiting_payment', null );

		htl_get_template( 'booking/received.php', array( 'reservation' => $reservation, 'shortcode_atts' => $atts ) );
	}

	/**
	 * Show the booking form
	 */
	private static function booking( $atts ) {
		// Hide booking page when booking_mode is set to 'no-booking'
		if ( htl_get_option( 'booking_mode' ) != 'no-booking' ) {

			// Show non-cart errors
			htl_print_notices();

			// Check cart has contents
			if ( HTL()->cart->is_empty() ) {
				return;
			}

			// Check cart contents for errors
			do_action( 'hotelier_booking_check_rooms_availability' );

			// Calc totals
			HTL()->cart->calculate_totals();

			// Get booking object
			$booking = HTL()->booking();

			if ( empty( $_POST ) && htl_notice_count( 'error' ) > 0 ) {

				htl_get_template( 'booking/cart-errors.php', array( 'booking' => $booking, 'shortcode_atts' => $atts ) );

			} else {

				htl_get_template( 'booking/form-booking.php', array( 'booking' => $booking, 'shortcode_atts' => $atts ) );

			}
		}
	}
}

endif;
