<?php
/**
 * Request Booking
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/book-button.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="request-booking" class="booking__section booking__section--request-booking">

	<div class="form-row">
		<?php do_action( 'hotelier_booking_before_submit' ); ?>

		<?php echo apply_filters( 'hotelier_book_button_html', '<input type="submit" class="button button--book-button" name="hotelier_booking_book_button" id="book-button" value="' . esc_attr( $button_text ) . '" />' ); ?>

		<input type="hidden" name="hotelier_booking_action" value="1" />

		<?php do_action( 'hotelier_booking_after_submit' ); ?>

		<?php wp_nonce_field( 'hotelier_process_booking' ); ?>
	</div>

</div>
