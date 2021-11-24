<?php
/**
 * Cart errors page
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/cart-errors.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

htl_print_notices();

do_action( 'hotelier_before_cart_errors_page', $shortcode_atts ); ?>

<p class="cart-errors"><?php esc_html_e( 'There are some issues with the items in your cart (shown above). Please go back and resolve these issues before the booking.', 'wp-hotelier' ) ?></p>

<?php do_action( 'hotelier_cart_has_errors' ); ?>

<?php if ( ! htl_get_option( 'listing_disabled', false ) ) : ?>
	<p><a class="button button--backward" href="<?php echo esc_url( HTL()->cart->get_room_list_form_url() ); ?>"><?php esc_html_e( 'List of available rooms', 'wp-hotelier' ) ?></a></p>
<?php endif; ?>

<?php do_action( 'hotelier_after_cart_errors_page', $shortcode_atts );
