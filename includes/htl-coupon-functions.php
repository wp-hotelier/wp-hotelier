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

/**
 * Check if coupons are enabled.
 *
 * @return bool
 */
function htl_coupons_enabled() {
	return apply_filters( 'hotelier_coupons_enabled', htl_get_option( 'enable_coupons' ) );
}

/**
 * Calculate coupon.
 *
 * @param  int $amount Amount without coupon.
 * @return int
 */
function htl_calculate_coupon( $amount, $coupon_id ) {
	$coupons_enabled = htl_coupons_enabled();

	// Return early if coupons are not enabled
	if ( ! $coupons_enabled ) {
		return 0;
	}

	$coupon = htl_get_coupon( $coupon_id );

	if ( $coupon->get_type() === 'fixed' ) {
		$amount_to_reduce = $coupon->get_amount();
	} else {
		$percentage_to_reduce = $coupon->get_amount();
		$amount_to_reduce     = ( $amount * $percentage_to_reduce ) / 100;
	}

	$calculated_coupon = ceil( $amount_to_reduce );
	$calculated_coupon = apply_filters( 'hotelier_calulate_coupon', $calculated_coupon, $amount, $coupon_id );

	return absint( $calculated_coupon );
}

/**
 * Get all available coupons.
 *
 * @return mixed
 */
function htl_get_all_coupons() {
	$all_coupons = array();

	$coupons = get_posts( array(
		'post_type'           => 'coupon',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1
	) );

	if ( is_array( $coupons ) && count( $coupons ) > 0 ) {
		foreach ( $coupons as $coupon ) {
			$_coupon = htl_get_coupon( $coupon->ID );

			$all_coupons[$coupon->ID] = array(
				'title' => $coupon->post_title,
				'code'  => $_coupon->get_code(),
			);
		}
	}

	return $all_coupons;
}

/**
 * Get coupon ID from code.
 *
 * @param  string $coupon_code Coupon code.
 * @return mixed
 */
function htl_get_coupon_id_from_code( $coupon_code ) {
	if ( empty( $coupon_code ) ) {
		return false;
	}

	$coupon_id = false;
	$coupons   = htl_get_all_coupons();

	if ( is_array( $coupons ) && count( $coupons ) > 0 ) {
		foreach ( $coupons as $coupon_key => $coupon ) {
			if ( isset( $coupon['code'] ) && strtolower( $coupon['code'] ) === strtolower( $coupon_code ) ) {
				$coupon_id = $coupon_key;
				break;
			}
		}
	}

	return $coupon_id;
}

/**
 * Check if we can apply this coupon.
 *
 * @param  int $coupon_id Coupon ID.
 * @return array
 */
function htl_can_apply_coupon( $coupon_id, $force = false ) {
	$can_apply = true;
	$reason    = false;

	$coupon = htl_get_coupon( $coupon_id );

	// Check if coupon exists
	if ( ! $coupon->exists() ) {
		$reason    = esc_html__( 'This coupon does not exists.', 'wp-hotelier' );
		$can_apply = false;
	}

	if ( $force ) {
		return array( 'can_apply' => true, 'reason' => '' );
	}

	// Check if coupon is active and enabled
	if ( ! $coupon->is_active() ) {
		$reason    = esc_html__( 'This coupon has expired.', 'wp-hotelier' );
		$can_apply = false;
	}

	$data = apply_filters(
		'hotelier_can_apply_coupon',
		array(
			'can_apply' => $can_apply,
			'reason'    => $reason
		),
		$coupon_id
	);

	return $data;
}
