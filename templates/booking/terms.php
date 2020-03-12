<?php
/**
 * Terms and conditions checkbox
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/terms.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( htl_get_page_id( 'terms' ) > 0 ) : ?>
	<?php do_action( 'hotelier_before_terms_and_conditions' ); ?>

	<p class="form-row form-row--booking-terms">
		<input type="checkbox" class="input-checkbox input--booking-terms" name="booking_terms" <?php checked( isset( $_POST[ 'booking_terms' ] ), true ); ?> id="booking-terms" />

		<label for="booking-terms" class="checkbox label--booking-terms"><?php printf( wp_kses_post( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'wp-hotelier' ) ), esc_url( htl_get_page_permalink( 'terms' ) ) ); ?> <abbr class="required" title="' . esc_attr__( 'required', 'wp-hotelier'  ) . '">*</abbr></label>

		<input type="hidden" name="has_terms_field" value="1" />
	</p>

	<?php do_action( 'hotelier_after_terms_and_conditions' ); ?>
<?php endif; ?>
