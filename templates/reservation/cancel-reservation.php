<?php
/**
 * Cancel reservation button
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/cancel-reservation.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cancel_button_classes = apply_filters( 'hotelier_cancel_reservation_button_classes', array( 'button', 'button--cancel-reservation-button' ) );
?>

<p class="cancel-reservation">
	<a href="<?php echo esc_url( $reservation->get_booking_cancel_url() ); ?>" class="<?php echo esc_attr( implode( ' ', $cancel_button_classes ) ); ?>"><?php _e( 'Cancel reservation', 'wp-hotelier' ); ?></a>
</p>
