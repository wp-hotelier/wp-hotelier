<?php
/**
 * Cancel reservation button
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/cancel-reservation.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="cancel-reservation">
	<a href="<?php echo esc_url( $reservation->get_booking_cancel_url() ); ?>" class="button button--cancel-reservation-button"><?php _e( 'Cancel reservation', 'hotelier' ); ?></a>
</p>
