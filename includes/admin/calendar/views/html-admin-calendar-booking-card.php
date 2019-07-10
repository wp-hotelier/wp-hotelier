<?php
/**
 * Calendar booking card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$_reservation_id = $cell[ 'id' ];
$_reservation    = htl_get_reservation( $_reservation_id );

?>

<div class="booking-calendar-card">

	<h4 class="booking-calendar-card__title"><?php echo esc_html( get_the_title( $_reservation_id ) ) ?></h4>

	<span class="booking-calendar-card__dates"><?php echo sprintf( esc_html__( 'Dates: %1$s - %2$s', 'wp-hotelier' ), $_reservation->get_checkin(), $_reservation->get_checkout() ); ?></span>

	<ul class="booking-calendar-card-details__list">
		<li class="booking-calendar-card-details__item">
			<strong class="booking-calendar-card-details__label"><?php esc_html_e( 'Number of nights:' ) ?></strong>
			<span class="booking-calendar-card-details__text"><?php echo sprintf( esc_html__( '%d-night stay', 'wp-hotelier' ), $_reservation->get_nights() ) ?></span>
		</li>

		<li class="booking-calendar-card-details__item">
			<strong class="booking-calendar-card-details__label"><?php esc_html_e( 'Room:' ) ?></strong>
			<span class="booking-calendar-card-details__text"><?php echo esc_html( get_the_title( $room_id ) ) ?></span>
		</li>
	</ul>

	<span class="htl-ui-text-icon-button htl-ui-text-icon-button--left htl-ui-text-icon-button--status htl-ui-text-icon-button--status-<?php echo esc_html( $_reservation->get_status() ); ?>"><?php echo esc_html( $_reservation->get_status() ); ?></span>

	<a class="htl-ui-icon booking-calendar-card-details__view-link" href="<?php echo esc_url( get_edit_post_link( $_reservation_id ) ); ?>"><?php esc_html_e( 'View reservation', 'wp-hotelier' ); ?></a>
</div>
