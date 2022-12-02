<?php
/**
 * Reservation Received Page - This is the page guests are sent to after completing their reservation
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/received.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="reservation-received">

	<?php
	do_action( 'hotelier_before_reservation_received_page', $shortcode_atts );

	if ( $reservation ) : ?>

		<div class="reservation-received__section reservation-received__section--response">

			<?php if ( $reservation->has_status( 'failed' ) ) : ?>

				<p class="reservation-response reservation-response--failed"><?php esc_html_e( 'Unfortunately your reservation cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'wp-hotelier' ); ?></p>

				<p class="reservation-response reservation-response--failed">
					<a href="<?php echo esc_url( $reservation->get_booking_payment_url() ); ?>" class="button button--pay-failed-reservation"><?php _e( 'Pay', 'wp-hotelier' ) ?></a>
				</p>

			<?php elseif ( $reservation->has_status( 'cancelled' ) ) : ?>

				<p class="reservation-response reservation-response--cancelled"><?php echo apply_filters( 'hotelier_reservation_cancelled_text', esc_html__( 'This reservation has been cancelled. The reservation was as follows.', 'wp-hotelier' ), $reservation ); ?></p>

				<?php do_action( 'hotelier_reservation_details', $reservation ); ?>

			<?php elseif ( $reservation->has_status( 'refunded' ) ) : ?>

				<p class="reservation-response reservation-response--refunded"><?php echo apply_filters( 'hotelier_reservation_refunded_text', esc_html__( 'This reservation has been refunded. The reservation was as follows.', 'wp-hotelier' ), $reservation ); ?></p>

				<?php do_action( 'hotelier_reservation_details', $reservation ); ?>

			<?php else : ?>

				<p class="reservation-response reservation-response--received"><?php echo apply_filters( 'hotelier_reservation_received_text', esc_html__( 'Thank you. Your reservation has been received.', 'wp-hotelier' ), $reservation ); ?></p>

				<?php do_action( 'hotelier_reservation_details', $reservation ); ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'hotelier_received_' . $reservation->payment_method, $reservation->id ); ?>
		<?php do_action( 'hotelier_received', $reservation->id ); ?>

	<?php else : ?>

		<div class="reservation-received__section reservation-received__section--response">

			<p class="reservation-response reservation-response--invalid"><?php esc_html_e( 'Invalid reservation.', 'wp-hotelier' ); ?></p>

			<?php if ( ! htl_get_option( 'listing_disabled', false ) ) : ?>
				<p><a class="button button--backward" href="<?php echo esc_url( HTL()->cart->get_room_list_form_url() ); ?>"><?php esc_html_e( 'List of available rooms', 'wp-hotelier' ) ?></a></p>
			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php do_action( 'hotelier_after_reservation_received_page', $shortcode_atts ); ?>

</div>
