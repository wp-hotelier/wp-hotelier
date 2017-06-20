<?php
/**
 * Email reservation items (HTML)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/email-reservation-items.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

foreach ( $items as $item_id => $item ) : ?>

	<tr>
		<td style="text-align:left;font-size:14px;line-height:20px;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;font-family:Helvetica,Arial;">
			<?php

			echo esc_html( $item[ 'name' ] );

			if ( isset( $item[ 'rate_name' ] ) ) {
				// Rate
				echo '<br><small style="text-align:left;font-size:12px;line-height:16px;color:#999999;font-family:Helvetica,Arial;">' . sprintf( esc_html__( 'Rate: %s', 'wp-hotelier' ), htl_get_formatted_room_rate( $item[ 'rate_name' ] ) ) . '</small>';
			}

			if ( ! $item[ 'is_cancellable' ] ) {
				// Non cancellable info
				echo '<br><small style="text-align:left;font-size:12px;line-height:16px;color:red;font-family:Helvetica,Arial;">' . esc_html__( 'Non-refundable', 'wp-hotelier' ) . '</small>';
			}

			// Allow other plugins to add additional room information here
			do_action( 'hotelier_reservation_item_meta', $item_id, $item, $reservation );
			?>
		</td>
		<td style="text-align:left;font-size:14px;line-height:20px;color:#999999;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;font-family:Helvetica,Arial;"><?php echo esc_html( $item[ 'qty' ] ); ?></td>
		<td style="text-align:left;font-size:14px;line-height:20px;color:#999999;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;font-family:Helvetica,Arial;"><?php echo $reservation->get_formatted_line_total( $item ); ?></td>
	</tr>

<?php endforeach; ?>
