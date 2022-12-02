<?php
/**
 * Booking Form - Where guests will complete their reservations
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/form-booking.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

htl_print_notices();

do_action( 'hotelier_before_booking_form', $booking, $shortcode_atts );

// extensions can hook into here to add their own pages
$booking_form_url = apply_filters( 'hotelier_booking_form_url', HTL()->cart->get_booking_form_url() ); ?>

<form id="booking-form" name="booking" method="post" class="booking form--booking" action="<?php echo esc_url( $booking_form_url ); ?>" enctype="multipart/form-data">

	<?php do_action( 'hotelier_begin_booking_form' ); ?>

	<?php if ( sizeof( $booking->booking_fields ) > 0 ) : ?>

		<?php do_action( 'hotelier_booking_guest_details' ); ?>

		<?php
		// show additional information fields
		if ( htl_get_option( 'booking_additional_information' ) ) :	?>

			<?php do_action( 'hotelier_booking_additional_information' ); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'hotelier_booking_details' ); ?>

	<?php do_action( 'hotelier_booking_table' ); ?>

	<?php do_action( 'hotelier_booking_payment' ); ?>

	<?php do_action( 'hotelier_book_button' ); ?>

	<?php do_action( 'hotelier_end_booking_form' ); ?>

</form>

<?php do_action( 'hotelier_after_booking_form', $booking, $shortcode_atts ); ?>
