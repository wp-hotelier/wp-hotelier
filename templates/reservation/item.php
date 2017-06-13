<?php
/**
 * Reservation item details
 *
 * This template can be overridden by copying it to yourtheme/hotelier/reservation/item.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr class="reservation-table__row reservation-table__row--body">
	<td class="reservation-table__room-name reservation-table__room-name--body"><?php
		$is_visible = $room && $room->is_visible( true );

		echo $is_visible ? sprintf( '<a class="reservation-table__room-link" href="%s">%s</a>', get_permalink( $item[ 'room_id' ] ), $item[ 'name' ] ) : $item[ 'name' ];

		if ( isset( $item[ 'rate_name' ] ) ) : ?>
			<small class="reservation-table__room-rate"><?php printf( esc_html__( 'Rate: %s', 'hotelier' ), htl_get_formatted_room_rate( $item[ 'rate_name' ] ) ); ?></small>
		<?php endif;

		if ( ! $item[ 'is_cancellable' ] ) : ?>
			<span class="reservation-table__room-non-cancellable"><?php echo esc_html_e( 'Non-refundable', 'hotelier' ); ?></span>
		<?php endif;

		// Allow other plugins to add additional room information here
		do_action( 'hotelier_reservation_item_meta', $item_id, $item, $reservation );
		?>
	</td>
	<td class="reservation-table__room-qty reservation-table__room-qty--body"><?php echo esc_html( $item[ 'qty' ] ); ?></td>
	<td class="reservation-table__room-cost reservation-table__room-cost--body"><?php echo $reservation->get_formatted_line_total( $item ); ?></td>
</tr>
