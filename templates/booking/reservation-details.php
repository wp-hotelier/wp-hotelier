<?php
/**
 * Booking details
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/reservation-details.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="reservation-details" class="booking__section booking__section--reservation-details">

	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Booking details', 'hotelier' ); ?></h3>
	</header>

	<?php do_action( 'hotelier_booking_before_booking_details' ); ?>

	<table class="table table--reservation-table reservation-table reservation-table--reservation-details hotelier-table">
		<tbody class="reservation-table__body">
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Check-in', 'hotelier' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $checkin ); ?></td>
			</tr>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Check-out', 'hotelier' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $checkout ); ?></td>
			</tr>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Pets', 'hotelier' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $pets_message ); ?></td>
			</tr>

			<?php if ( $cards ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Accepted credit cards', 'hotelier' ); ?></th>
				<td class="reservation-table__data reservation-table__data--credit-cards">
					<ul class="credit-cards__list">
					<?php foreach ( $cards as $card ) : ?>
						<li class="credit-cards__icon credit-cards__icon--<?php echo esc_attr( $card ); ?>"><?php echo esc_html( ucfirst( $card ) ); ?></li>
					<?php endforeach; ?>
					</ul>
				</td>
			</tr>
			<?php endif; ?>

		</tbody>
	</table>

	<?php do_action( 'hotelier_booking_after_booking_details' ); ?>

</div>
