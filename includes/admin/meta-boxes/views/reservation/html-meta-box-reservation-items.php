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

<div class="htl-ui-scope">

	<div class="hotelier-reservation-items-wrapper">
		<table cellpadding="0" cellspacing="0" class="htl-ui-table htl-ui-table--reservation-items">
			<thead class="htl-ui-table__head">
				<tr class="htl-ui-table__row htl-ui-table__row--head">
					<th class="htl-ui-table__cell htl-ui-table__cell--head htl-ui-table__cell--reservation-items-room-name" colspan="2"><?php esc_html_e( 'Room', 'wp-hotelier' ); ?></th>

					<?php do_action( 'hotelier_admin_reservation_item_headers', $reservation ); ?>

					<th class="htl-ui-table__cell htl-ui-table__cell--head htl-ui-table__cell--reservation-items-room-guests"><?php esc_html_e( 'Guests', 'wp-hotelier' ); ?></th>
					<th class="htl-ui-table__cell htl-ui-table__cell--head htl-ui-table__cell--reservation-items-room-price"><?php esc_html_e( 'Price', 'wp-hotelier' ); ?></th>
					<th class="htl-ui-table__cell htl-ui-table__cell--head htl-ui-table__cell--reservation-items-room-qty"><?php esc_html_e( 'Qty', 'wp-hotelier' ); ?></th>
					<th class="htl-ui-table__cell htl-ui-table__cell--head htl-ui-table__cell--reservation-items-room-total"><?php esc_html_e( 'Total', 'wp-hotelier' ); ?></th>
				</tr>
			</thead>

			<tbody class="htl-ui-table__body">
			<?php
				foreach ( $line_items as $item_id => $item ) {
					$_room     = $reservation->get_room_from_item( $item );
					$item_meta = $reservation->get_item_meta( $item_id );

					include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/reservation/html-meta-box-reservation-single-item.php';
				}
			?>
			</tbody>
		</table>
	</div>

	<div class="hotelier-reservation-items-wrapper">
		<table cellpadding="0" cellspacing="0" class="htl-ui-table htl-ui-table--reservation-totals">

			<?php if ( $reservation->has_tax() ) : ?>

				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-subtotal">
					<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Subtotal', 'wp-hotelier' ); ?>:</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--total">
						<?php echo htl_price( htl_convert_to_cents( $reservation->get_subtotal() ), $reservation->get_reservation_currency() ); ?>
					</td>
				</tr>

				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-tax-total">
					<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Tax total', 'wp-hotelier' ); ?>:</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--total">
						<?php echo htl_price( htl_convert_to_cents( $reservation->get_tax_total() ), $reservation->get_reservation_currency() ); ?>
					</td>
				</tr>

			<?php endif; ?>

			<?php if ( $reservation->has_room_with_deposit() ) : ?>

				<?php if ( ! $reservation->has_tax() ) : ?>

					<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-subtotal">
						<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Total charge', 'wp-hotelier' ); ?>:</td>
						<td class="htl-ui-table__cell htl-ui-table__cell--total">
							<?php echo htl_price( htl_convert_to_cents( $reservation->get_total() ), $reservation->get_reservation_currency() ); ?>
						</td>
					</tr>

				<?php endif; ?>

				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-deposit">

					<td class="htl-ui-table__cell htl-ui-table__cell--label">
						<?php if ( $reservation->get_paid_deposit() > 0 ) {
							echo esc_html__( 'Paid deposit', 'wp-hotelier' );
						} else {
							echo esc_html__( 'Deposit due', 'wp-hotelier' );
						} ?>:
					</td>

					<td class="htl-ui-table__cell htl-ui-table__cell--total rooms-with-paid-deposit">
						<ul class="rooms-with-paid-deposit__list">
						<?php foreach ( $line_items as $item ) :
							if ( $item[ 'deposit' ] > 0 ) :
								$rate_name = isset( $item[ 'rate_name' ] ) ? htl_get_formatted_room_rate( $item[ 'rate_name' ] ) : '';
								?>
								<li class="rooms-with-paid-deposit__item">

									<span class="rooms-with-paid-deposit__name"><?php echo esc_html( $item[ 'name' ] ); ?></span>

									<?php if ( $rate_name ) : ?>
										<span class="rooms-with-paid-deposit__rate"><?php echo esc_attr( $rate_name ); ?></span>
									<?php endif; ?>

									<?php if ( isset( $item[ 'percent_deposit' ] ) && $item[ 'percent_deposit' ] > 0 ) : ?>
										<span class="rooms-with-paid-deposit__percentage"><?php echo absint( $item[ 'percent_deposit' ] ); ?>%</span>
									<?php endif; ?>

									<span class="rooms-with-paid-deposit__amount"><?php echo htl_price( htl_convert_to_cents( $item[ 'deposit' ] ), $reservation->get_reservation_currency() ); ?></span>
								</li>
							<?php
							endif;
						endforeach; ?>
						</ul>

						<span class="rooms-with-paid-deposit-total <?php echo ( $reservation->get_paid_deposit() > 0 ) ? 'rooms-with-paid-deposit-total--paid' : 'rooms-with-paid-deposit-total--due'; ?>"><?php echo htl_price( htl_convert_to_cents( $reservation->get_deposit(), $reservation->get_reservation_currency() ) ); ?></span>
					</td>
				</tr>

				<?php if ( $reservation->get_remain_deposit_charge() ) : ?>

				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-remain-deposit-charge">
					<td colspan="2" class="htl-ui-table__cell htl-ui-table__cell--total">
						<span class="remain-deposit-charge-label"><?php printf( esc_html__( 'Remain deposit charged manually (%s)', 'wp-hotelier' ), date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $reservation->get_remain_deposit_charge_date() ) ); ?>: </span>
						<span class="remain-deposit-charge-amount">- <?php echo wp_kses_post( $reservation->get_formatted_remain_deposit_charge() ); ?></span>
					</td>
				</tr>

				<?php endif; ?>

			<?php endif; ?>

			<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-total">
				<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Balance due', 'wp-hotelier' ); ?>:</td>
				<td class="htl-ui-table__cell htl-ui-table__cell--total">
					<?php
					$reservation_balance_due = wp_kses_post( $reservation->get_formatted_balance_due() );

					echo ( $reservation->get_status() == 'refunded' ) ? '<del>' . $reservation_balance_due . '</del>' : $reservation_balance_due;
					?>
				</td>
			</tr>

		</table>
	</div>

	<?php if ( $reservation->get_status() == 'refunded' ) : ?>

		<?php htl_ui_print_notice( __( 'This reservation has been refunded', 'wp-hotelier' ), 'info', array( 'reservation-totals-message' ) ); ?>

	<?php else : ?>

		<?php if ( ! $reservation->can_be_cancelled() ) : ?>
			<?php htl_ui_print_notice( __( 'Non-refundable', 'wp-hotelier' ), 'info', array( 'reservation-totals-message' ) ); ?>
		<?php endif; ?>

		<?php if ( ! $reservation->get_remain_deposit_charge() ) : ?>

			<div class="reservation-totals-pay-actions">
				<?php if ( $reservation->can_be_charged() ) : ?>
					<button type="submit" name="hotelier_charge_remain_deposit" class="htl-ui-button htl-ui-button--secondary htl-ui-button--charge-remain-deposit" value="1"><?php esc_html_e( 'Charge remain deposit', 'wp-hotelier' ); ?></button>
				<?php endif; ?>

				<?php if ( $reservation->is_marked_as_paid() ) : ?>
					<button type="submit" name="hotelier_mark_as_paid_action" class="htl-ui-button" value="unpaid"><?php esc_html_e( 'Mark as unpaid', 'wp-hotelier' ); ?></button>
				<?php else : ?>
					<button type="submit" name="hotelier_mark_as_paid_action" class="htl-ui-button" value="paid"><?php esc_html_e( 'Mark as paid', 'wp-hotelier' ); ?></button>
				<?php endif; ?>
			</div>

		<?php endif; ?>

	<?php endif; ?>

</div>
