<?php
/**
 * Reservation details
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/reservation-details.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<ul class="reservation-details__list">
	<li class="reservation-details__item reservation-details__item--number">
		<span class="reservation-details__label reservation-details__label--number"><?php esc_html_e( 'Reservation number:', 'wp-hotelier' ); ?></span>
		<strong class="reservation-details__data reservation-details__data--number"><?php echo $reservation->get_reservation_number(); ?></strong>
	</li>
	<li class="reservation-details__item reservation-details__item--date">
		<span class="reservation-details__label reservation-details__label--date"><?php esc_html_e( 'Date:', 'wp-hotelier' ); ?></span>
		<strong class="reservation-details__data reservation-details__data--date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $reservation->reservation_date ) ); ?></strong>
	</li>
	<li class="reservation-details__item reservation-details__item--checkin">
		<span class="reservation-details__label reservation-details__label--checkin"><?php esc_html_e( 'Check-in:', 'wp-hotelier' ); ?></span>
		<strong class="reservation-details__data reservation-details__data--checkin"><?php echo esc_html( $reservation->get_formatted_checkin() ); ?> <span>(<?php echo esc_html( HTL_Info::get_hotel_checkin() ); ?>)</span></strong>
	</li>
	<li class="reservation-details__item reservation-details__item--checkout">
		<span class="reservation-details__label reservation-details__label--checkout"><?php esc_html_e( 'Check-out:', 'wp-hotelier' ); ?></span>
		<strong class="reservation-details__data reservation-details__data--checkout"><?php echo esc_html( $reservation->get_formatted_checkout() ); ?> <span>(<?php echo esc_html( HTL_Info::get_hotel_checkout() ); ?>)</span></strong>
	</li>
	<li class="reservation-details__item reservation-details__item--nights">
		<span class="reservation-details__label reservation-details__label--nights"><?php esc_html_e( 'Nights:', 'wp-hotelier' ); ?></span>
		<strong class="reservation-details__data reservation-details__data--nights"><?php echo esc_html( $reservation->get_nights() ); ?></strong>
	</li>
	<li class="reservation-details__item reservation-details__item--special-requests">
		<strong class="reservation-details__label reservation-details__label--special-requests"><?php esc_html_e( 'Special requests:', 'wp-hotelier' ); ?></strong>
		<span class="reservation-details__data reservation-details__data--special-requests"><?php echo esc_html( $reservation->get_guest_special_requests() ? $reservation->get_guest_special_requests() : esc_html__( 'None', 'wp-hotelier' ) ); ?></span>
	</li>
</ul>
