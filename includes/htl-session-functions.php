<?php
/**
 * Session related functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check default checkin and checkout when the page loads
 * and set the default values
 */
function htl_check_default_dates() {
	if ( ! is_admin() ) {
		if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {

			// Check if we have checkin/checkout dates in session. If not, load default dates.
			if ( is_null( HTL()->session->get( 'checkin' ) ) || is_null( HTL()->session->get( 'checkout' ) ) ) {
				do_action( 'hotelier_set_cookies', true );

				$dates = htl_get_default_dates();

				HTL()->session->set( 'checkin', $dates[ 'checkin' ] );
				HTL()->session->set( 'checkout', $dates[ 'checkout' ] );

			// Check if the checkin date is greater than today.
			// If not, load default dates.
			} else if ( ! is_null( HTL()->session->get( 'checkin' ) ) ) {
				$today    = new DateTime( current_time( 'Y-m-d' ) );
				$checkin  = new DateTime( HTL()->session->get( 'checkin' ) );

				if ( $checkin < $today ) {
					$dates = htl_get_default_dates();

					HTL()->session->set( 'checkin', $dates[ 'checkin' ] );
					HTL()->session->set( 'checkout', $dates[ 'checkout' ] );
				}
			}
		}
	}
}
add_action( 'wp_loaded', 'htl_check_default_dates' );

/**
 * Get valid default checkin/checkout dates
 */
function htl_get_default_dates() {
	$dates = array();

	// Arrival date must be "XX" days from current date (default 0).
	$from = htl_get_option( 'booking_arrival_date', 0 );

	// Get minimum number of nights a guest can book
	$minimum_nights = apply_filters( 'hotelier_booking_minimum_nights', htl_get_option( 'booking_minimum_nights', 1 ) );

	// Set default checkout
	$to = $from + $minimum_nights;

	$checkin = new DateTime( current_time( 'Y-m-d' ) );
	$checkin->modify( "+$from days" );
	$checkout = new DateTime( current_time( 'Y-m-d' ) );
	$checkout->modify( "+$to days" );

	$dates[ 'checkin' ]  = $checkin->format( 'Y-m-d' );
	$dates[ 'checkout' ] = $checkout->format( 'Y-m-d' );

	return apply_filters( 'hotelier_get_default_dates', $dates );
}
