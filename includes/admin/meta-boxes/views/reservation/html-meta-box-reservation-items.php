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
			<?php $discount_printed = false; ?>

			<?php if ( $reservation->has_tax() ) : ?>

				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-subtotal">
					<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Subtotal', 'wp-hotelier' ); ?>:</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--total">
						<?php echo htl_price( htl_convert_to_cents( $reservation->get_subtotal() ), $reservation->get_reservation_currency() ); ?>
					</td>
				</tr>

				<?php if ( $reservation->get_discount_total() > 0 ) : ?>
					<?php $discount_printed = true; ?>

					<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-discount-total">
						<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Discount', 'wp-hotelier' ); ?>:</td>
						<td class="htl-ui-table__cell htl-ui-table__cell--total">
							<?php echo $reservation->get_formatted_discount_total(); ?> <small><?php echo $reservation->get_coupon_code(); ?></small>
						</td>
					</tr>
				<?php endif; ?>

				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-tax-total">
					<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Tax total', 'wp-hotelier' ); ?>:</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--total">
						<?php echo htl_price( htl_convert_to_cents( $reservation->get_tax_total() ), $reservation->get_reservation_currency() ); ?>
					</td>
				</tr>

			<?php endif; ?>

			<?php if ( ! $discount_printed && $reservation->get_discount_total() > 0 ) : ?>
				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-subtotal">
					<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Subtotal', 'wp-hotelier' ); ?>:</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--total">
						<?php echo htl_price( htl_convert_to_cents( $reservation->get_subtotal() ), $reservation->get_reservation_currency() ); ?>
					</td>
				</tr>

				<tr class="htl-ui-table__row htl-ui-table__row--reservation-totals-discount-total">
					<td class="htl-ui-table__cell htl-ui-table__cell--label"><?php esc_html_e( 'Discount', 'wp-hotelier' ); ?>:</td>
					<td class="htl-ui-table__cell htl-ui-table__cell--total">
						<?php echo $reservation->get_formatted_discount_total(); ?> <small><?php echo $reservation->get_coupon_code(); ?></small>
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

									<span class="rooms-with-paid-deposit__amount">
										<?php echo htl_price( htl_convert_to_cents( $item[ 'deposit' ] ), $reservation->get_reservation_currency() ); ?>

										<?php if ( $reservation->get_discount_total() > 0 ) : ?>
											<small><?php esc_html_e( '(Discounts excluded)', 'wp-hotelier' ); ?></small>
										<?php endif; ?>

									</span>
								</li>
							<?php
							endif;
						endforeach; ?>
						</ul>

						<span class="rooms-with-paid-deposit-total <?php echo ( $reservation->get_paid_deposit() > 0 ) ? 'rooms-with-paid-deposit-total--paid' : 'rooms-with-paid-deposit-total--due'; ?>">
							<?php if ( $reservation->get_paid_deposit() > 0 ) : ?>
								<?php echo htl_price( htl_convert_to_cents( $reservation->get_paid_deposit(), $reservation->get_reservation_currency() ) ); ?>
							<?php else : ?>
								<?php echo htl_price( htl_convert_to_cents( $reservation->get_deposit(), $reservation->get_reservation_currency() ) ); ?>
							<?php endif; ?>

							<?php if ( htl_is_tax_enabled() && htl_is_deposit_tax_enabled() && htl_get_tax_rate() > 0 ) : ?>
								<span class="rooms-with-paid-deposit-total__tax"><?php esc_html_e( '(incl. tax)', 'wp-hotelier' ); ?></span>
							<?php endif; ?>
						</span>

						<?php if ( $reservation->requires_capture() ) : ?>
							<span class="deposit-needs-capture-info">
								<?php if ( $reservation->can_be_captured() ) : ?>
									<?php esc_html_e( 'Authorized only. Must be captured.', 'wp-hotelier' ); ?>
								<?php else : ?>
									<?php esc_html_e( 'Authorization expired. Cannot be captured.', 'wp-hotelier' ); ?>
								<?php endif; ?>
							</span>
						<?php endif; ?>
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

	<?php if ( $reservation->get_paid_deposit() > 0 && ( $reservation->get_paid_deposit() < $reservation->get_deposit() ) ) : ?>

		<?php
		$formatted_amount_captured = htl_price( htl_convert_to_cents( $reservation->get_paid_deposit() ), $reservation->get_reservation_currency() );
		$formatted_required_deposit  = htl_price( htl_convert_to_cents( $reservation->get_deposit() ), $reservation->get_reservation_currency() );
		 ?>
		<?php htl_ui_print_notice( sprintf( esc_html__( 'The captured amount (%s) does not match the required deposit (%s). The balance has been recalculated.', 'wp-hotelier' ), $formatted_amount_captured, $formatted_required_deposit ), 'info', array( 'reservation-totals-message' ) ); ?>

	<?php endif; ?>

	<?php if ( $reservation->get_status() == 'refunded' ) : ?>

		<?php htl_ui_print_notice( __( 'This reservation has been refunded', 'wp-hotelier' ), 'info', array( 'reservation-totals-message' ) ); ?>

	<?php else : ?>

		<?php if ( ! $reservation->can_be_cancelled() ) : ?>
			<?php htl_ui_print_notice( __( 'Non-refundable', 'wp-hotelier' ), 'info', array( 'reservation-totals-message' ) ); ?>
		<?php endif; ?>

		<?php if ( ! $reservation->get_remain_deposit_charge() ) : ?>

			<div class="reservation-totals-pay-actions">
				<?php if ( $reservation->can_be_refunded() ) : ?>
					<div class="htl-ui-modal htl-ui-modal--refund-deposit" id="modal-refund-deposit">
						<div class="htl-ui-modal__content">
							<h3 class="htl-ui-heading htl-ui-modal__title"><?php esc_html_e( 'Refund deposit', 'wp-hotelier' ); ?></h3>

							<label for="hotelier-refund-amount" class="htl-ui-label"><?php esc_html_e( 'Refund amount:', 'wp-hotelier' ); ?></label>

							<div>
								<input type="text" class="htl-ui-input htl-ui-input--text htl-ui-input--small htl-ui-input--price" id="hotelier-refund-amount" name="hotelier_refund_amount" placeholder="<?php echo esc_attr( HTL_Meta_Box_Room_Settings::get_price_placeholder() ); ?>" value="<?php echo esc_attr( HTL_Formatting_Helper::localized_amount( $reservation->get_paid_deposit() ) ); ?>" data-max-amount="<?php echo esc_attr( $reservation->get_paid_deposit() ); ?>">
							</div>

							<div class="htl-ui-setting__description htl-ui-setting__description--price">
								<?php echo sprintf( __( 'The max amount refundable for this reservation is %s.', 'wp-hotelier' ), '<strong>' . htl_price( htl_convert_to_cents( $reservation->get_paid_deposit() ), $reservation->get_reservation_currency() ) . '</strong>' ); ?>
							</div>

							<?php do_action( 'hotelier_after_refund_modal' ); ?>
						</div>

						<div class="htl-ui-modal__buttons">
							<button type="button" class="htl-ui-button htl-ui-button--secondary htl-ui-modal__cancel"><?php esc_html_e( 'Cancel', 'wp-hotelier' ); ?></button>

							<button type="submit" name="hotelier_refund_deposit" class="htl-ui-button htl-ui-button--refund-deposit htl-ui-modal__confirm" value="1"><?php esc_html_e( 'Refund', 'wp-hotelier' ); ?></button>
						</div>
					</div>

					<button type="button" class="htl-ui-button htl-ui-button--secondary htl-ui-button--open-modal" data-open-modal="modal-refund-deposit"><?php esc_html_e( 'Refund deposit', 'wp-hotelier' ); ?></button>
				<?php endif; ?>

				<?php if ( $reservation->can_be_captured() ) : ?>
					<div class="htl-ui-modal htl-ui-modal--capture-deposit" id="modal-capture-deposit">
						<div class="htl-ui-modal__content">
							<h3 class="htl-ui-heading htl-ui-modal__title"><?php esc_html_e( 'Capture deposit', 'wp-hotelier' ); ?></h3>

							<label for="hotelier-capture-deposit-amount" class="htl-ui-label"><?php esc_html_e( 'Capture amount:', 'wp-hotelier' ); ?></label>

							<div>
								<input type="text" class="htl-ui-input htl-ui-input--text htl-ui-input--small htl-ui-input--price" id="hotelier-capture-deposit-amount" name="hotelier_capture_deposit_amount" placeholder="<?php echo esc_attr( HTL_Meta_Box_Room_Settings::get_price_placeholder() ); ?>" value="<?php echo esc_attr( HTL_Formatting_Helper::localized_amount( $reservation->get_deposit() ) ); ?>" data-max-amount="<?php echo esc_attr( $reservation->get_deposit() ); ?>">
							</div>

							<div class="htl-ui-setting__description htl-ui-setting__description--price">
								<?php echo sprintf( __( 'The amount to capture must be less than or equal to the the original authorized amount. And you can only capture an authorized transaction once. The max amount capturable for this reservation is %s.', 'wp-hotelier' ), '<strong>' . htl_price( htl_convert_to_cents( $reservation->get_deposit() ), $reservation->get_reservation_currency() ) . '</strong>' ); ?>
							</div>

							<?php do_action( 'hotelier_after_capture_modal' ); ?>
						</div>

						<div class="htl-ui-modal__buttons">
							<button type="button" class="htl-ui-button htl-ui-button--secondary htl-ui-modal__cancel"><?php esc_html_e( 'Cancel', 'wp-hotelier' ); ?></button>

							<button type="submit" name="hotelier_capture_deposit" class="htl-ui-button htl-ui-button--capture-deposit htl-ui-modal__confirm" value="1"><?php esc_html_e( 'Capture', 'wp-hotelier' ); ?></button>
						</div>
					</div>

					<button type="button" class="htl-ui-button htl-ui-button--secondary htl-ui-button--open-modal" data-open-modal="modal-capture-deposit"><?php esc_html_e( 'Capture deposit', 'wp-hotelier' ); ?></button>
				<?php endif; ?>

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
