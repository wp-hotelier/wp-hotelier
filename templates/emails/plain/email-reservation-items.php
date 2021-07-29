<?php
/**
 * Email reservation items (plain text)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/plain/email-reservation-items.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.2.0
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

	if ( htl_get_option( 'booking_number_of_guests_selection', true ) ) {
		// Adults/children info

		$adults   = isset( $item[ 'adults' ] ) ? maybe_unserialize( $item[ 'adults' ] ) : false;
		$children = isset( $item[ 'children' ] ) ? maybe_unserialize( $item[ 'children' ] ) : false;

		for ( $q = 0; $q < $item[ 'qty' ]; $q++ ) {
			$line_adults   = isset( $adults[ $q ] ) && ( $adults[ $q ] > 0 ) ? $adults[ $q ] : false;
			$line_children = isset( $children[ $q ] ) && ( $children[ $q ] > 0 ) ? $children[ $q ] : false;

			if ( $line_adults || $line_children ) {
				if ( $item[ 'qty' ] > 1 ) {
					echo "\n" . sprintf( esc_html__( 'Number of guests (Room %d):', 'wp-hotelier' ), $q + 1 );
				} else {
					echo "\n" . esc_html__( 'Number of guests:', 'wp-hotelier' );
				}

				if ( $line_adults ) {
					echo " " . sprintf( _n( '%s Adult', '%s Adults', $line_adults, 'wp-hotelier' ), $line_adults );
				}

				if ( $line_children ) {
					echo " " . sprintf( esc_html__( '%d Children', 'wp-hotelier' ), $line_children );
				}
			}
		}
	}

	// Allow other plugins to add additional room information here
	do_action( 'hotelier_email_reservation_item_meta', $item_id, $item, $reservation, true );

	$item_has_extras = false;

	if ( isset( $item[ 'extras' ] ) ) {
		$extras = maybe_unserialize( $item[ 'extras' ] );

		if ( is_array( $extras ) && count( $extras ) > 0 ) {
			$item_has_extras = true;
		}
	}

	if ( $item_has_extras ) {
		foreach ( $extras as $extra_id => $extra_data ) {
			$extra       = htl_get_extra( $extra_id );
			$extra_qty   = isset( $extra_data['qty'] ) ? $extra_data['qty'] : 1;
			$extra_price = $item[ 'qty' ] * $extra_data['price'] * $extra_qty;

			echo "\n\n" . esc_html( $extra->get_name() );

			if ( $extra_descritpion = $extra->get_description() ) {
				echo "\n" . esc_html( $extra_descritpion );
			}

			echo "\n" . sprintf( esc_html__( 'Cost: %s', 'wp-hotelier' ), htl_price( htl_convert_to_cents( $extra_price ) ) );
		}
	}

	echo "\n\n";

endforeach;
