<?php
/**
 * Cart errors page
 *
 * This template can be overridden by copying it to yourtheme/hotelier/booking/cart-errors.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php htl_print_notices(); ?>

<p class="cart-errors"><?php esc_html_e( 'There are some issues with the items in your cart (shown above). Please go back and resolve these issues before the booking.', 'wp-hotelier' ) ?></p>

<?php do_action( 'hotelier_cart_has_errors' ); ?>

<p><a class="button button--backward" href="<?php echo esc_url( HTL()->cart->get_room_list_form_url() ); ?>"><?php esc_html_e( 'List of available rooms', 'wp-hotelier' ) ?></a></p>
