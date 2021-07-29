<?php
/**
 * Room guests
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/reservation-table-guests.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php for ( $q = 0; $q < $quantity; $q++ ) : ?>

	<div class="reservation-table__room-guests reservation-table__room-guests--booking">

		<?php if ( $quantity > 1 ) : ?>
			<span class="reservation-table__room-guests-label"><?php echo sprintf( esc_html__( 'Number of guests (Room %d):', 'wp-hotelier' ), $q + 1 ); ?></span>
		<?php else : ?>
			<span class="reservation-table__room-guests-label"><?php esc_html_e( 'Number of guests:', 'wp-hotelier' ); ?></span>
		<?php endif; ?>

		<?php if ( htl_get_option( 'booking_number_of_guests_selection_type', 'booking-page' ) === 'listing-page' || ! apply_filters( 'hotelier_booking_show_number_of_guests_selection', true, $room ) ) : ?>
			<?php
			$line_adults   = isset( $guests[$q] ) && isset( $guests[$q]['adults'] ) && ( $guests[$q]['adults'] > 0 ) ? $guests[$q]['adults'] : false;
			$line_children = isset( $guests[$q] ) && isset( $guests[$q]['children'] ) && ( $guests[$q]['children'] > 0 ) ? $guests[$q]['children'] : false;
			?>

			<?php if ( $line_adults ) : ?>
				<span class="reservation-table__room-guests-adults"><?php echo sprintf( _n( '%s Adult', '%s Adults', $line_adults, 'wp-hotelier' ), $line_adults ); ?></span>
			<?php endif; ?>

			<?php if ( $line_children ) : ?>
				<span class="reservation-table__room-guests-children"><?php echo sprintf( esc_html__( '%d Children', 'wp-hotelier' ), $line_children ); ?></span>
			<?php endif; ?>
		<?php else : ?>
			<?php
			$adults_options = array();

			for ( $i = 1; $i <= $adults; $i++ ) {
				$adults_options[ $i ] = $i;
			}

			$adults_std = htl_get_reservation_table_guests_default_adults_selection( $adults, $item_key, $q );
			$adults_std = apply_filters( 'hotelier_reservation_table_guests_default_selection_adults', $adults_std );

			$adults_args = array(
				'type'    => 'select',
				'label'   => esc_html__( 'Adults', 'wp-hotelier' ),
				'class'   => array(),
				'default' => $adults_std,
				'options' => $adults_options
			);

			htl_form_field( 'adults[' . $item_key . '][' . $q . ']', $adults_args );

			if ( $children > 0 ) {
				$children_options = array();

				for ( $i = 0; $i <= $children; $i++ ) {
					$children_options[ $i ] = $i;
				}

				$children_std = htl_get_reservation_table_guests_default_children_selection( 0, $item_key, $q );
				$children_std = apply_filters( 'hotelier_reservation_table_guests_default_selection_children', $children_std );

				$children_args = array(
					'type'    => 'select',
					'label'   => esc_html__( 'Children', 'wp-hotelier' ),
					'class'   => array(),
					'default' => $children_std,
					'options' => $children_options
				);

				htl_form_field( 'children[' . $item_key . '][' . $q . ']', $children_args );
			} ?>
		<?php endif; ?>
	</div>

<?php endfor; ?>
