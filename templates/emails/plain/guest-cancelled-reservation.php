<?php
/**
 * Guest cancelled reservation email (plain text)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/plain/guest-cancelled-reservation.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

echo sprintf( esc_html__( 'Hello %s, your reservation has been cancelled. The reservation was as follows.', 'wp-hotelier' ), $reservation->get_formatted_guest_full_name() ) . "\n\n";

echo "=====================================================================\n\n";

do_action( 'hotelier_email_hotel_info', $plain_text );

echo "==========\n\n";

echo sprintf( esc_html__( 'Check-in: %s', 'wp-hotelier' ), $reservation->get_formatted_checkin() ) . ' (' . HTL_Info::get_hotel_checkin() . ')' . "\n";
echo sprintf( esc_html__( 'Check-out: %s', 'wp-hotelier' ), $reservation->get_formatted_checkout() ) . ' (' . HTL_Info::get_hotel_checkout() . ')' . "\n";
echo sprintf( esc_html__( 'Nights: %s', 'wp-hotelier' ), $reservation->get_nights() ) . "\n\n";

echo "=====================================================================\n\n";

echo strtoupper( sprintf( esc_html__( 'Reservation number: %s', 'wp-hotelier' ), $reservation->get_reservation_number() ) ) . "\n";
echo date_i18n( get_option( 'date_format' ), strtotime( $reservation->reservation_date ) ) . "\n";

echo "\n" . $reservation->email_reservation_items_table( true );

echo "==========\n\n";

if ( $totals = $reservation->get_reservation_totals( true ) ) {
	foreach ( $totals as $total ) {
		$extra = isset( $total[ 'extra' ] ) ? '(' . $total[ 'extra' ] . ')' : '';
		echo esc_html( $total[ 'label' ] ) . " " . wp_kses_post( $total[ 'value' ] ) . " " . $extra . "\n";
	}
}

echo "\n" . sprintf( esc_html__( 'View reservation: %s', 'wp-hotelier'), admin_url( 'post.php?post=' . $reservation->id . '&action=edit' ) ) . "\n";

echo "\n=====================================================================\n\n";

do_action( 'hotelier_email_reservation_instructions', $reservation, $sent_to_admin, $plain_text );

do_action( 'hotelier_email_guest_details', $reservation, $sent_to_admin, $plain_text );

do_action( 'hotelier_email_reservation_meta', $reservation, $sent_to_admin, $plain_text );

echo "\n=====================================================================\n\n";

echo apply_filters( 'hotelier_email_footer_text', htl_get_option( 'emails_footer_text', 'Powered by WP Hotelier' ) );
