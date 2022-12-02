<?php
/**
 * Booking table
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/reservation-table.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="reservation-table" class="booking__section booking__section--reservation-table">

	<header class="section-header">
		<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_booking_section_reservation_table_title', __( 'Your reservation', 'wp-hotelier' ) ) ); ?></h3>
	</header>

	<?php do_action( 'hotelier_booking_before_booking_table' ); ?>

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
				foreach ( HTL()->cart->get_cart() as $cart_item_key => $cart_item ) :
					$_room    = $cart_item[ 'data' ];
					$_room_id = $cart_item[ 'room_id' ];

					if ( $_room && $_room->exists() && $cart_item[ 'quantity' ] > 0 ) : ?>

						<?php
						$item_key        = htl_generate_item_key( $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ] );
						$item_has_extras = isset( $cart_item[ 'extras' ] ) && is_array( $cart_item[ 'extras' ] ) && count( $cart_item[ 'extras' ] ) > 0 ? true : false;
						?>

						<tr class="reservation-table__row reservation-table__row--body">
							<td class="reservation-table__room-name reservation-table__room-name--body">
								<?php do_action( 'hotelier_reservation_table_before_room_name', $_room, $item_key, $cart_item[ 'quantity' ] ); ?>

								<a class="reservation-table__room-link" href="<?php echo esc_url( get_permalink( $_room_id ) ); ?>"><?php echo esc_html( $_room->get_title() ); ?></a>

								<?php if ( $cart_item[ 'rate_name' ] ) : ?>
									<small class="reservation-table__room-rate"><?php printf( esc_html__( 'Rate: %s', 'wp-hotelier' ), htl_get_formatted_room_rate( $cart_item[ 'rate_name' ] ) ); ?></small>
								<?php endif; ?>

								<?php if ( ! $cart_item[ 'is_cancellable' ] ) : ?>
									<span class="reservation-table__room-non-cancellable"><?php esc_html_e( 'Non-refundable', 'wp-hotelier' ); ?></span>
								<?php endif; ?>

								<?php
									echo apply_filters( 'hotelier_cart_item_remove_link', sprintf(
										'<a href="%s" class="reservation-table__room-remove remove button">%s</a>',
										esc_url( htl_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove', 'wp-hotelier' )
									), $cart_item_key );
								?>

								<?php do_action( 'hotelier_reservation_table_guests', $_room, $item_key, $cart_item[ 'quantity' ] ); ?>
							</td>

							<td class="reservation-table__room-qty reservation-table__room-qty--body"><?php echo absint( $cart_item[ 'quantity' ] ); ?></td>

							<td class="reservation-table__room-cost reservation-table__room-cost--body">
								<?php if ( $item_has_extras ) : ?>
									<?php echo HTL()->cart->get_room_price( $cart_item[ 'total_without_extras' ] ); ?>
								<?php else : ?>
									<?php echo HTL()->cart->get_room_price( $cart_item[ 'total' ] ); ?>
								<?php endif; ?>

								<?php do_action( 'hotelier_reservation_table_after_price', $_room, $cart_item, $item_key ); ?>

								<?php if ( get_theme_support( 'htl-modal-price-breakdown' ) ) : ?>
									<?php if ( $nights > 1 && apply_filters( 'hotelier_show_price_breakdown', true, HTL()->session->get( 'checkin' ), HTL()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ) ) : ?>
										<span class="view-price-breakdown-modal"><?php esc_html_e( 'View price breakdown', 'wp-hotelier' ); ?><?php echo htl_cart_price_breakdown( HTL()->session->get( 'checkin' ), HTL()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ); ?></span>
									<?php endif; ?>
								<?php else : ?>
									<?php if ( $nights > 1 && apply_filters( 'hotelier_show_price_breakdown', true, HTL()->session->get( 'checkin' ), HTL()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ) ) : ?>
									<a class="view-price-breakdown" href="#<?php echo esc_attr( $item_key ); ?>" data-closed="<?php esc_html_e( 'View price breakdown', 'wp-hotelier' ); ?>" data-open="<?php esc_html_e( 'Hide price breakdown', 'wp-hotelier' ); ?>"><?php esc_html_e( 'View price breakdown', 'wp-hotelier' ); ?></a>
									<?php endif; ?>
								<?php endif; ?>
							</td>
						</tr>

						<?php if ( ! get_theme_support( 'htl-modal-price-breakdown' ) ) : ?>
							<?php if ( $nights > 1 && apply_filters( 'hotelier_show_price_breakdown', true, HTL()->session->get( 'checkin' ), HTL()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ) ) : ?>
							<tr class="reservation-table__row reservation-table__row--body reservation-table__row--price-breakdown">
								<td colspan="3" class="price-breakdown-wrapper">
									<?php echo htl_cart_price_breakdown( HTL()->session->get( 'checkin' ), HTL()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ); ?>
								</td>
							</tr>
							<?php endif; ?>
						<?php endif;

						if ( $item_has_extras ) : ?>
							<?php do_action( 'hotelier_reservation_table_extras', $_room, $item_key, $cart_item ); ?>
						<?php endif;
					endif;
				endforeach;
			?>

		</tbody>
		<tfoot class="reservation-table__footer">
			<?php
				do_action( 'hotelier_reservation_table_before_footer' );

				$coupon_printed = false;

				if ( htl_is_tax_enabled() && htl_get_tax_rate() > 0 ) : ?>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--subtotal"><?php esc_html_e( 'Subtotal:', 'wp-hotelier' ); ?></th>
						<td class="reservation-table__data reservation-table__data--subtotal"><strong><?php echo htl_cart_formatted_subtotal(); ?></strong></td>
					</tr>

					<?php if ( htl_coupons_enabled() ) : ?>

						<?php
						do_action( 'hotelier_reservation_table_coupon_form' );
						$coupon_printed = true;
						?>

					<?php endif; ?>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--tax-total"><?php esc_html_e( 'Tax total:', 'wp-hotelier' ); ?></th>
						<td class="reservation-table__data reservation-table__data--tax-total"><strong><?php echo htl_cart_formatted_tax_total(); ?></strong></td>
					</tr>

				<?php endif;

				if ( HTL()->cart->needs_payment() ) : ?>

					<?php if ( htl_coupons_enabled() && ! $coupon_printed ) : ?>

						<tr class="reservation-table__row reservation-table__row--footer">
							<th colspan="2" class="reservation-table__label reservation-table__label--subtotal"><?php esc_html_e( 'Subtotal:', 'wp-hotelier' ); ?></th>
							<td class="reservation-table__data reservation-table__data--subtotal"><strong><?php echo htl_cart_formatted_subtotal(); ?></strong></td>
						</tr>

						<?php
						do_action( 'hotelier_reservation_table_coupon_form' );
						$coupon_printed = true;
						?>

					<?php endif; ?>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--total"><?php esc_html_e( 'Total:', 'wp-hotelier' ); ?></th>
						<td class="reservation-table__data reservation-table__data--total"><strong><?php echo htl_cart_formatted_total(); ?></strong></td>
					</tr>

					<?php if ( htl_get_option( 'booking_mode' ) == 'instant-booking' ) : ?>

						<tr class="reservation-table__row reservation-table__row--footer">
							<th colspan="2" class="reservation-table__label reservation-table__label--total reservation-table__label--deposit"><?php esc_html_e( 'Deposit due now:', 'wp-hotelier' ); ?></th>
							<td class="reservation-table__data reservation-table__data--total reservation-table__data--deposit"><strong><?php echo htl_cart_formatted_required_deposit(); ?></strong></td>
						</tr>

					<?php else : ?>

						<tr class="reservation-table__row reservation-table__row--footer">
							<th colspan="2" class="reservation-table__label reservation-table__label--total reservation-table__label--deposit"><?php esc_html_e( 'Deposit due after confirm:', 'wp-hotelier' ); ?></th>
							<td class="reservation-table__data reservation-table__data--total reservation-table__data--deposit"><strong><?php echo htl_cart_formatted_required_deposit(); ?></strong></td>
						</tr>

					<?php endif; ?>

				<?php else : ?>

					<?php if ( htl_coupons_enabled() && ! $coupon_printed ) : ?>

						<tr class="reservation-table__row reservation-table__row--footer">
							<th colspan="2" class="reservation-table__label reservation-table__label--subtotal"><?php esc_html_e( 'Subtotal:', 'wp-hotelier' ); ?></th>
							<td class="reservation-table__data reservation-table__data--subtotal"><strong><?php echo htl_cart_formatted_subtotal(); ?></strong></td>
						</tr>

						<?php
						do_action( 'hotelier_reservation_table_coupon_form' );
						$coupon_printed = true;
						?>

					<?php endif; ?>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--total"><?php esc_html_e( 'Total:', 'wp-hotelier' ); ?></th>
						<td class="reservation-table__data reservation-table__data--total"><strong><?php echo htl_cart_formatted_total(); ?></strong></td>
					</tr>

				<?php endif;

				do_action( 'hotelier_reservation_table_after_footer' );
			?>
		</tfoot>
	</table>

	<?php if ( ! HTL()->cart->is_cancellable() ) : ?>
		<div class="reservation-non-cancellable-disclaimer">
			<p class="reservation-non-cancellable-disclaimer__text">
				<?php esc_html_e( 'This reservation includes a non-cancellable and non-refundable room. You will be charged the total price if you cancel your booking.', 'wp-hotelier' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<?php do_action( 'hotelier_booking_after_booking_table' ); ?>

</div>
