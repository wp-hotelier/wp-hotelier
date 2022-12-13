<?php
/**
 * Hotelier Cart Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Prevent password protected rooms being added to the cart
 *
 * @param  bool $passed
 * @param  int $room_id
 * @return bool
 */
function htl_protected_room_add_to_cart( $passed, $room_id ) {
	if ( post_password_required( $room_id ) ) {
		$passed = false;
		htl_add_notice( esc_html__( 'This room is protected and cannot be reserved.', 'wp-hotelier' ), 'error' );
	}
	return $passed;
}
add_filter( 'hotelier_add_to_cart_validation', 'htl_protected_room_add_to_cart', 10, 2 );

/**
 * Clear cart after payment.
 *
 * @access public
 */
function htl_clear_cart_after_payment() {
	global $wp;

	if ( ! empty( $wp->query_vars[ 'reservation-received' ] ) ) {

		$reservation_id  = absint( $wp->query_vars[ 'reservation-received' ] );
		$reservation_key = isset( $_GET[ 'key' ] ) ? sanitize_text_field( $_GET[ 'key' ] ) : '';

		if ( $reservation_id > 0 ) {
			$reservation = htl_get_reservation( $reservation_id );

			if ( $reservation->reservation_key === $reservation_key ) {
				HTL()->cart->empty_cart();
			}
		}
	}

	if ( HTL()->session->get( 'reservation_awaiting_payment' ) > 0 ) {
		$reservation = htl_get_reservation( HTL()->session->get( 'reservation_awaiting_payment' ) );

		if ( $reservation && $reservation->id > 0 ) {
			// If the reservation has not failed, or is not pending, the reservation must have gone through
			if ( ! $reservation->has_status( array( 'failed', 'pending', 'cancelled', 'refunded' ) ) ) {
				HTL()->cart->empty_cart();
			}
		}
	}
}
add_action( 'get_header', 'htl_clear_cart_after_payment' );

/**
 * Clears the cart session when called.
 */
function htl_empty_cart() {
	if ( ! isset( HTL()->cart ) || HTL()->cart == '' ) {
		HTL()->cart = new HTL_Cart();
	}
	HTL()->cart->empty_cart();
}

/**
 * Get the formatted total.
 *
 * @access public
 * @return string
 */
function htl_cart_formatted_total() {
	$total = htl_price( htl_convert_to_cents( HTL()->cart->get_total() ) );

	echo $total;
}

/**
 * Get the formatted subtotal.
 *
 * @access public
 * @return string
 */
function htl_cart_formatted_subtotal() {
	$subtotal = htl_price( htl_convert_to_cents( HTL()->cart->get_subtotal() ) );

	echo $subtotal;
}

/**
 * Get the formatted tax total.
 *
 * @access public
 * @return string
 */
function htl_cart_formatted_tax_total() {
	$tax_total = htl_price( htl_convert_to_cents( HTL()->cart->get_tax_total() ) );

	echo $tax_total;
}

/**
 * Get the formatted required deposit.
 *
 * @access public
 * @return string
 */
function htl_cart_formatted_required_deposit() {
	$required_deposit = htl_price( htl_convert_to_cents( HTL()->cart->get_required_deposit() ) );

	echo $required_deposit;
}

/**
 * Get the formatted discount.
 *
 * @access public
 * @return string
 */
function htl_cart_formatted_discount() {
	$discount = '<span class="discount-separator">-</span>' . htl_price( htl_convert_to_cents( HTL()->cart->get_discount_total() ) );

	echo $discount;
}

/**
 * Output the price breakdown table.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @param int $room_id
 * @param int $rate_id
 * @param int $qty
 * @return string
 */
function htl_cart_price_breakdown( $checkin, $checkout, $room_id, $rate_id, $qty ) {

	$breakdown = htl_get_room_price_breakdown( $checkin, $checkout, $room_id, $rate_id, $qty );

	$html = '<table class="table table--price-breakdown price-breakdown" id="' . esc_attr( htl_generate_item_key( $room_id, $rate_id ) ) . '">';
	$html .= '<thead><tr class="price-breakdown__row price-breakdown__row--heading"><th colspan="2" class="price-breakdown__day price-breakdown__day--heading">' . esc_html__( 'Day', 'wp-hotelier' ) . '</th><th class="price-breakdown__cost price-breakdown__cost--heading">' . esc_html__( 'Cost', 'wp-hotelier' ) . '</th></tr><tbody>';

	foreach ( $breakdown as $day => $price ) {
		$html .= '<tr class="price-breakdown__row price-breakdown__row--body">';
		$html .= '<td colspan="2" class="price-breakdown__day price-breakdown__day--body">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $day ) ) ) . '</td>';
		$html .= '<td class="price-breakdown__cost price-breakdown__cost--body">' . htl_price( htl_convert_to_cents( $price ) ) . '</td>';
		$html .= '</tr>';
	}

	$html .= '</tbody></table>';

	echo apply_filters( 'hotelier_room_price_breakdown_table', $html, $checkin, $checkout, $room_id, $rate_id );
}
