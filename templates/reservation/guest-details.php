<?php
/**
 * Guest details
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/guest-details.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="reservation-received__section reservation-received__section--guest-details">

	<header class="section-header">
		<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_received_section_guest_details_title', __( 'Guest details', 'wp-hotelier' ) ) ); ?></h3>
	</header>

	<table class="table table--guest-details hotelier-table">
		<?php if ( $reservation->get_formatted_guest_full_name() ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Name:', 'wp-hotelier' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $reservation->get_formatted_guest_full_name() ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $reservation->guest_email ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Email:', 'wp-hotelier' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $reservation->guest_email ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $reservation->guest_telephone ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Telephone:', 'wp-hotelier' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $reservation->guest_telephone ); ?></td>
			</tr>
		<?php endif; ?>

		<?php do_action( 'hotelier_reservation_after_guest_details', $reservation ); ?>
	</table>

</div>

<div class="reservation-received__section reservation-received__section--guest-address">
	<header class="section-header">
		<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_received_section_guest_address_title', __( 'Guest address', 'wp-hotelier' ) ) ); ?></h3>
	</header>

	<address class="address address--guest-address">
		<?php echo $reservation->get_formatted_guest_address(); ?>
	</address>
</div>
