<?php
/**
 * Hotelier Tax Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check if tax is enabled.
 *
 * @return bool
 */
function htl_is_tax_enabled() {
	$tax_enabled = htl_get_option( 'tax_enabled', false );

	return apply_filters( 'hotelier_is_tax_enabled', $tax_enabled );
}

/**
 * Check if tax is enabled on deposits.
 *
 * @return bool
 */
function htl_is_deposit_tax_enabled() {
	$tax_enabled = htl_get_option( 'tax_in_deposit', false );

	return apply_filters( 'hotelier_is_deposit_tax_enabled', $tax_enabled );
}

/**
 * Get tax rate.
 *
 * @return float
 */
function htl_get_tax_rate() {
	$tax_rate = (float) htl_get_option( 'tax_rate', 0 );

	return apply_filters( 'hotelier_get_tax_rate', $tax_rate );
}

/**
 * Calculate tax.
 *
 * Taxes are round up to the next cent. A filter is
 * provided for developers.
 *
 * @param  int $amount Amount without tax.
 * @return int
 */
function htl_calculate_tax( $amount ) {
	$tax_enabled = htl_is_tax_enabled();

	// Return early if tax is not enabled
	if ( ! $tax_enabled ) {
		return 0;
	}

	$tax_rate = htl_get_tax_rate();

	// Return early if tax rate is 0
	if ( ! $tax_rate ) {
		return 0;
	}

	$calculated_tax = ceil( $amount * ( $tax_rate / 100 ) );
	$calculated_tax = apply_filters( 'hotelier_calulate_tax', $calculated_tax, $amount, $tax_rate );

	return absint( $calculated_tax );
}
