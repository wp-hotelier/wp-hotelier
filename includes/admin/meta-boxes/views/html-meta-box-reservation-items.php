<?php
/**
 * Shows the items (rooms) attached to the reservation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get line items
$line_items = $reservation->get_items();
?>

<div class="hotelier-reservation-items-wrapper">
	<table cellpadding="0" cellspacing="0" class="hotelier_reservation_items">
		<thead>
			<tr>
				<th class="room-name" colspan="2"><?php esc_html_e( 'Room', 'wp-hotelier' ); ?></th>

				<?php do_action( 'hotelier_admin_reservation_item_headers', $reservation ); ?>

				<th class="room-guests"><?php esc_html_e( 'Guests', 'wp-hotelier' ); ?></th>
				<th class="room-price"><?php esc_html_e( 'Price', 'wp-hotelier' ); ?></th>
				<th class="room-qty"><?php esc_html_e( 'Qty', 'wp-hotelier' ); ?></th>
				<th class="room-total"><?php esc_html_e( 'Total', 'wp-hotelier' ); ?></th>
			</tr>
		</thead>

		<tbody>
		<?php
			foreach ( $line_items as $item_id => $item ) {
				$_room     = $reservation->get_room_from_item( $item );
				$item_meta = $reservation->get_item_meta( $item_id );

				include( 'html-meta-box-reservation-single-item.php' );

				// do_action( 'hotelier_reservation_item_html', $item_id, $item, $reservation );
			}
		?>
		</tbody>
	</table>
</div>

<div class="hotelier-reservation-items-wrapper">
	<table class="hotelier-reservation-totals">

		<?php if ( $reservation->has_room_with_deposit() ) : ?>

			<tr class="subtotal">
				<td class="label"><?php esc_html_e( 'Total charge', 'wp-hotelier' ); ?>:</td>
				<td class="total">
					<?php echo htl_price( htl_convert_to_cents( $reservation->get_total() ), $reservation->get_reservation_currency() ); ?>
				</td>
			</tr>

			<tr class="deposit">

				<td class="label">
					<?php if ( $reservation->get_paid_deposit() > 0 ) {
						echo esc_html__( 'Paid deposit', 'wp-hotelier' );
					} else {
						echo esc_html__( 'Deposit due', 'wp-hotelier' );
					} ?>:
				</td>

				<td class="total">
					<ul>
					<?php foreach ( $line_items as $item ) :
						if ( $item[ 'deposit' ] > 0 ) :
							$rate_name = isset( $item[ 'rate_name' ] ) ? htl_get_formatted_room_rate( $item[ 'rate_name' ] ) : '';
							?>
							<li>
								<span class="item-name hastip" title="<?php echo esc_attr( $rate_name ); ?>"><?php echo esc_html( $item[ 'name' ] ); ?></span>
								<span class="deposit"><?php echo absint( $item[ 'deposit' ] ); ?>%</span>
								<span class="line-deposit"><?php echo htl_calculate_deposit( $item[ 'total' ], $item[ 'deposit' ], $reservation->get_reservation_currency() ); ?></span>
							</li>
						<?php
						endif;
					endforeach; ?>
					</ul>

					<span class="deposit-total <?php echo ( $reservation->get_paid_deposit() > 0 ) ? '' : 'due'; ?>"><?php echo htl_price( htl_convert_to_cents( $reservation->get_deposit(), $reservation->get_reservation_currency() ) ); ?></span>
				</td>
			</tr>

			<?php if ( $reservation->get_remain_deposit_charge() ) : ?>

			<tr class="remain-deposit-charge">
				<td colspan="2" class="total">
					<span class="remain-deposit-charge-label"><?php printf( esc_html__( 'Remain deposit charged manually (%s)', 'wp-hotelier' ), date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $reservation->get_remain_deposit_charge_date() ) ); ?>: </span>
					<span class="remain-deposit-charge-amount">- <?php echo esc_attr( $reservation->get_formatted_remain_deposit_charge() ); ?></span>
				</td>
			</tr>

			<?php endif; ?>

		<?php endif; ?>

		<tr class="total">
			<td class="label"><?php esc_html_e( 'Balance due', 'wp-hotelier' ); ?>:</td>
			<td class="total">
				<?php echo wp_kses_post( $reservation->get_formatted_balance_due() ); ?>
			</td>
		</tr>

	</table>
</div>

<?php if ( ! $reservation->can_be_cancelled() ) : ?>
	<div class="hotelier-reservation-no-cancellable-info">
		<span><?php esc_html_e( 'Non-refundable', 'wp-hotelier' ); ?></span>
	</div>
<?php endif; ?>

<?php if ( ! $reservation->get_remain_deposit_charge() ) : ?>

	<div class="hotelier-pay-actions">
		<?php if ( $reservation->can_be_charged() ) : ?>
			<button type="submit" name="hotelier_charge_remain_deposit" class="button charge-remain-deposit" value="1"><?php esc_html_e( 'Charge remain deposit', 'wp-hotelier' ); ?></button>
		<?php endif; ?>

		<?php if ( $reservation->is_marked_as_paid() ) : ?>
			<button type="submit" name="hotelier_mark_as_paid_action" class="button button-primary" value="unpaid"><?php esc_html_e( 'Mark as unpaid', 'wp-hotelier' ); ?></button>
		<?php else : ?>
			<button type="submit" name="hotelier_mark_as_paid_action" class="button button-primary" value="paid"><?php esc_html_e( 'Mark as paid', 'wp-hotelier' ); ?></button>
		<?php endif; ?>
	</div>

<?php endif; ?>
