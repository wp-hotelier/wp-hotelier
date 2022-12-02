<?php
/**
 * Pay for reservation form
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/form-pay.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'hotelier_before_pay_form' );
?>

<form id="pay-reservation-form" class="form form--pay-reservation" method="post">

	<?php do_action( 'hotelier_form_pay_before_reservation_table' ); ?>

	<div id="reservation-table" class="booking__section booking__section--reservation-table">

		<header class="section-header">
			<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_booking_section_reservation_table_title', __( 'Your reservation', 'wp-hotelier' ) ) ); ?></h3>
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
				if ( $totals = $reservation->get_totals_before_booking() ) :
					foreach ( $totals as $total ) : ?>
						<tr class="reservation-table__row reservation-table__row--footer">
							<th colspan="2" class="reservation-table__label reservation-table__label--total"><?php echo esc_html( $total[ 'label' ] ); ?></th>
							<td class="reservation-table__data reservation-table__data--total"><strong><?php echo wp_kses_post( $total[ 'value' ] ); ?></strong></td>
						</tr>
					<?php endforeach;
				endif; ?>
			</tfoot>
		</table>

		<?php do_action( 'hotelier_form_pay_after_reservation_table' ); ?>

	</div>

	<?php if ( $reservation->needs_payment() ) : ?>

		<?php do_action( 'hotelier_form_pay_before_payment_div' ); ?>

		<div id="payment" class="booking__section booking__section--payment">
			<header class="section-header">
				<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_booking_section_payment_title', __( 'Payment method', 'wp-hotelier' ) ) ); ?></h3>
			</header>

			<ul class="payment-methods">
				<?php
					if ( ! empty( $available_gateways ) ) {
						$single = ( count( $available_gateways ) == 1 ) ? true : false;

						foreach ( $available_gateways as $gateway ) {
							htl_get_template( 'booking/payment-method.php', array( 'gateway' => $gateway, 'single' => $single ) );
						}
					} else {
						echo '<li class="payment-method payment-method--error">' . esc_html__( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance.', 'wp-hotelier' ) . '</li>';
					}
				?>
			</ul>

		</div>

		<div id="request-booking" class="booking__section booking__section--request-booking">

			<div class="form-row">

				<?php do_action( 'hotelier_form_pay_before_submit' ); ?>

				<input type="hidden" name="hotelier_pay" value="1" />
				<input type="hidden" id="email" value="<?php echo esc_attr( $reservation->guest_email ); ?>" />

				<?php echo apply_filters( 'hotelier_book_button_html', '<input type="submit" class="button button--book-button" id="book-button" value="' . esc_attr( $reservation_button_text ) . '" />' ); ?>

				<?php do_action( 'hotelier_form_pay_after_submit' ); ?>

				<?php wp_nonce_field( 'hotelier-pay' ); ?>
			</div>

		</div>

		<?php do_action( 'hotelier_form_pay_after_payment_div' ); ?>

	<?php endif; ?>

</form>

<?php do_action( 'hotelier_after_pay_form' ); ?>
