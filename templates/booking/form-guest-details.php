<?php
/**
 * Guest Details Form
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/form-guest-details.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="guest-details" class="booking__section booking__section--guest-details">

	<header class="section-header">
		<h3 class="<?php echo esc_attr( apply_filters( 'hotelier_booking_section_title_class', 'section-header__title' ) ); ?>"><?php echo esc_html( apply_filters( 'hotelier_booking_section_guest_details_title', __( 'Guest details', 'wp-hotelier' ) ) ); ?></h3>
	</header>

	<?php do_action( 'hotelier_booking_before_guest_details' ); ?>

	<div class="guest-details-fields">

		<?php foreach ( $booking->booking_fields[ 'address_fields' ] as $key => $field ) : ?>

			<?php htl_form_field( $key, $field, $booking->get_value( $key ) ); ?>

		<?php endforeach; ?>

	</div>

	<?php do_action( 'hotelier_booking_after_guest_details' ); ?>

</div>
