<?php
/**
 * Calendar booking card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$_reservation_id       = $cell[ 'id' ];
$_reservation_nights   = $duration;
$_reservation_checkin  = $reservation[ 'checkin' ];
$_reservation_checkout = $reservation[ 'checkout' ];
$_reservation_status   = $reservation[ 'status' ];
$_reservation_duration = $duration;

?>

<div class="booking-calendar-card">

	<h4 class="booking-calendar-card__title"><?php echo esc_html( get_the_title( $_reservation_id ) ) ?></h4>

	<span class="booking-calendar-card__dates"><?php echo sprintf( esc_html__( 'Dates: %1$s - %2$s', 'wp-hotelier' ), $_reservation_checkin, $_reservation_checkout ); ?></span>

	<ul class="booking-calendar-card-details__list">
		<li class="booking-calendar-card-details__item">
			<strong class="booking-calendar-card-details__label"><?php esc_html_e( 'Number of nights:' ) ?></strong>
			<span class="booking-calendar-card-details__text"><?php echo sprintf( esc_html__( '%d-night stay', 'wp-hotelier' ), $_reservation_duration ) ?></span>
		</li>

		<li class="booking-calendar-card-details__item">
			<strong class="booking-calendar-card-details__label"><?php esc_html_e( 'Room:' ) ?></strong>
			<span class="booking-calendar-card-details__text"><?php echo esc_html( get_the_title( $room_id ) ) ?></span>
		</li>
	</ul>

	<span class="htl-ui-text-icon-button htl-ui-text-icon-button--left htl-ui-text-icon-button--status htl-ui-text-icon-button--status-<?php echo esc_html( $_reservation_status ); ?>"><?php echo esc_html( $_reservation_status ); ?></span>

	<a href="<?php echo esc_url( get_edit_post_link( $_reservation_id ) ); ?>"><?php esc_html_e( 'View reservation', 'wp-hotelier' ); ?></a>
</div>
