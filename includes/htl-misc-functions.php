<?php
/**
 * Hotelier Misc Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get Guest IP
 *
 * Returns the IP address of the current visitor
 *
 * @return string $ip Guest's IP address
 */
function htl_get_ip() {

	$ip = '127.0.0.1';

	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return apply_filters( 'hotelier_get_ip', $ip );
}

/**
 * Get first and last day of current week range
 *
 * @param  string $marker
 * @param  int $weeks
 * @return array
 */
function htl_get_week_limits( $marker, $weeks ) {
	$marker = new DateTime( $marker );

	// Use the user's preference for first day of week
	$first_day_of_week = get_option( 'start_of_week' );

	$days = array(
		0 => 'sunday',
		1 => 'monday',
		2 => 'tuesday',
		3 => 'wednesday',
		4 => 'thursday',
		5 => 'friday',
		6 => 'saturday',
	);

	$min = new DateTime('last ' . $days[ $first_day_of_week ] . ' ' . $marker->format( 'Y-m-d' ) );

	if ( $marker->format('w') == $first_day_of_week ) {
		$min->modify('+7 days');
	}

	$max = clone( $min );
	$days_to_add = 6 * $weeks + $weeks - 1;

	return array(
		$min->format( 'Y-m-d' ),
		$max->modify( "+{$days_to_add} days" )->format( 'Y-m-d' )
	);
}
