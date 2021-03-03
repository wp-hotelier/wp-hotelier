<?php
/**
 * Hotelier Coupon Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning coupons.
 *
 * @param  mixed $the_coupon Post object or post ID of the coupon.
 * @return HTL_Coupon
 */
function htl_get_coupon( $the_coupon = false ) {
	return new HTL_Coupon( $the_coupon );
}
