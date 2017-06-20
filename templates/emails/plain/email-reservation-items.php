<?php
/**
 * Email reservation items (plain text)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/plain/email-reservation-items.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

foreach ( $items as $item_id => $item ) :

	// Title
	echo esc_html( $item[ 'name' ] );

	if ( isset( $item[ 'rate_name' ] ) ) {
		// Rate
		echo "\n" . sprintf( esc_html__( 'Rate: %s', 'wp-hotelier' ), htl_get_formatted_room_rate( $item[ 'rate_name' ] ) );
	}

	if ( ! $item[ 'is_cancellable' ] ) {
		// Non cancellable info
		echo "\n" .  esc_html__( 'Non-refundable', 'wp-hotelier' );
	}

	// Quantity
	echo "\n" . sprintf( esc_html__( 'Quantity: %s', 'wp-hotelier' ), $item[ 'qty' ] );

	// Cost
	echo "\n" . sprintf( esc_html__( 'Cost: %s', 'wp-hotelier' ), $reservation->get_formatted_line_total( $item ) );

	// Allow other plugins to add additional room information here
	do_action( 'hotelier_reservation_item_meta', $item_id, $item, $reservation );

	echo "\n\n";

endforeach;
