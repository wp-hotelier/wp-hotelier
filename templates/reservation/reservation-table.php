<?php
/**
 * Reservation table
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/reservation-table.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reservation = htl_get_reservation( $reservation_id );

?>

<div class="reservation-received__section reservation-received__section--reservation-details">

	<header class="section-header">
		<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_booking_section_reservation_details_title', __( 'Reservation details', 'wp-hotelier' ) ) ); ?></h3>
	</header>

	<table class="table table--reservation-table reservation-table hotelier-table">
		<thead class="reservation-table__heading">
			<tr class="reservation-table__row reservation-table__row--heading">
				<th class="reservation-table__room-name reservation-table__room-name--heading"><?php esc_html_e( 'Room', 'wp-hotelier' ); ?></th>
				<th class="reservation-table__room-qty reservation-table__room-qty--heading"><?php esc_html_e( 'Qty', 'wp-hotelier' ); ?></th>
				<th class="reservation-table__room-cost reservation-table__room-cost--heading"><?php esc_html_e( 'Cost', 'wp-hotelier' ); ?></th>
			</tr>
		</thead>
		<tbody class="reservation-table__body">
			<?php
				foreach( $reservation->get_items() as $item_id => $item ) {
					$room = $reservation->get_room_from_item( $item );

					htl_get_template( 'reservation/item.php', array(
						'reservation' => $reservation,
						'item_id'     => $item_id,
						'item'        => $item,
						'room'        => $room,
					) );
				}
			?>
		</tbody>
		<tfoot class="reservation-table__footer">
			<?php
			if ( $totals = $reservation->get_reservation_totals() ) :
				foreach ( $totals as $total ) : ?>
					<tr class="reservation-table__row reservation-table__row--footer">
						<th class="reservation-table__label reservation-table__label--total" colspan="2"><?php echo esc_html( $total[ 'label' ] ); ?></th>
						<td class="reservation-table__data reservation-table__data--total"><strong><?php echo wp_kses_post( $total[ 'value' ] ); ?></strong></td>
					</tr>
				<?php endforeach;
			endif; ?>
		</tfoot>
	</table>

	<?php if ( ! $reservation->can_be_cancelled() ) : ?>
		<div class="reservation-non-cancellable-disclaimer">
			<p class="reservation-non-cancellable-disclaimer__text">
				<?php esc_html_e( 'This reservation includes a non-cancellable and non-refundable room. You will be charged the total price if you cancel your booking.', 'wp-hotelier' ); ?>
			</p>
		</div>
	<?php endif; ?>

</div>

<?php do_action( 'hotelier_after_reservation_table', $reservation ); ?>
