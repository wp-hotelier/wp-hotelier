<?php
/**
 * Hotelier Price Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Return the thousand separator for prices
 * @return string
 */
function htl_get_price_thousand_separator() {
	$separator = stripslashes( htl_get_option( 'thousands_separator', ',' ) );
	return $separator;
}

/**
 * Return the decimal separator for prices
 * @return string
 */
function htl_get_price_decimal_separator() {
	$separator = stripslashes( htl_get_option( 'decimal_separator', '.' ) );
	return $separator ? $separator : '.';
}

/**
 * Return the number of decimals after the decimal point.
 * @return int
 */
function htl_get_price_decimals() {
	return absint( htl_get_option( 'price_num_decimals', 2 ) );
}

/**
 * Get full list of currency codes.
 */
function htl_get_currencies() {
	$currencies = array(
		'USD'  => esc_html__( 'US Dollars (&#36;)', 'wp-hotelier' ),
		'EUR'  => esc_html__( 'Euros (&euro;)', 'wp-hotelier' ),
		'GBP'  => esc_html__( 'Pounds Sterling (&pound;)', 'wp-hotelier' ),
		'AUD'  => esc_html__( 'Australian Dollars (&#36;)', 'wp-hotelier' ),
		'BRL'  => esc_html__( 'Brazilian Real (R&#36;)', 'wp-hotelier' ),
		'CAD'  => esc_html__( 'Canadian Dollars (&#36;)', 'wp-hotelier' ),
		'CZK'  => esc_html__( 'Czech Koruna', 'wp-hotelier' ),
		'DKK'  => esc_html__( 'Danish Krone', 'wp-hotelier' ),
		'HKD'  => esc_html__( 'Hong Kong Dollar (&#36;)', 'wp-hotelier' ),
		'HUF'  => esc_html__( 'Hungarian Forint', 'wp-hotelier' ),
		'ILS'  => esc_html__( 'Israeli Shekel (&#8362;)', 'wp-hotelier' ),
		'JPY'  => esc_html__( 'Japanese Yen (&yen;)', 'wp-hotelier' ),
		'MYR'  => esc_html__( 'Malaysian Ringgits', 'wp-hotelier' ),
		'MXN'  => esc_html__( 'Mexican Peso (&#36;)', 'wp-hotelier' ),
		'NZD'  => esc_html__( 'New Zealand Dollar (&#36;)', 'wp-hotelier' ),
		'NOK'  => esc_html__( 'Norwegian Krone', 'wp-hotelier' ),
		'PHP'  => esc_html__( 'Philippine Pesos', 'wp-hotelier' ),
		'PLN'  => esc_html__( 'Polish Zloty', 'wp-hotelier' ),
		'SGD'  => esc_html__( 'Singapore Dollar (&#36;)', 'wp-hotelier' ),
		'SEK'  => esc_html__( 'Swedish Krona', 'wp-hotelier' ),
		'CHF'  => esc_html__( 'Swiss Franc', 'wp-hotelier' ),
		'TWD'  => esc_html__( 'Taiwan New Dollars', 'wp-hotelier' ),
		'THB'  => esc_html__( 'Thai Baht (&#3647;)', 'wp-hotelier' ),
		'INR'  => esc_html__( 'Indian Rupee (&#8377;)', 'wp-hotelier' ),
		'TRY'  => esc_html__( 'Turkish Lira (&#8378;)', 'wp-hotelier' ),
		'RUB'  => esc_html__( 'Russian Rubles', 'wp-hotelier' )
	);

	return apply_filters( 'hotelier_currencies', $currencies );
}

/**
 * Get Base Currency Code.
 * @return string
 */
function htl_get_currency() {
	return apply_filters( 'hotelier_currency', htl_get_option( 'currency', 'USD' ) );
}

/**
 * Get Currency symbol.
 * @param string $currency (default: '')
 * @return string
 */
function htl_get_currency_symbol( $currency = '' ) {
	if ( ! $currency ) {
		$currency = htl_get_currency();
	}

	switch ( $currency ) {
		case 'AUD' :
		case 'CAD' :
		case 'HKD' :
		case 'MXN' :
		case 'NZD' :
		case 'SGD' :
		case 'USD' :
			$currency_symbol = '&#36;';
			break;
		case 'BRL' :
			$currency_symbol = '&#82;&#36;';
			break;
		case 'CHF' :
			$currency_symbol = '&#67;&#72;&#70;';
			break;
		case 'JPY' :
			$currency_symbol = '&yen;';
			break;
		case 'CZK' :
			$currency_symbol = '&#75;&#269;';
			break;
		case 'DKK' :
			$currency_symbol = 'DKK';
			break;
		case 'EUR' :
			$currency_symbol = '&euro;';
			break;
		case 'GBP' :
			$currency_symbol = '&pound;';
			break;
		case 'HUF' :
			$currency_symbol = '&#70;&#116;';
			break;
		case 'ILS' :
			$currency_symbol = '&#8362;';
			break;
		case 'INR' :
			$currency_symbol = 'Rs.';
			break;
		case 'MYR' :
			$currency_symbol = '&#82;&#77;';
			break;
		case 'NOK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'PHP' :
			$currency_symbol = '&#8369;';
			break;
		case 'PLN' :
			$currency_symbol = '&#122;&#322;';
			break;
		case 'RUB' :
			$currency_symbol = '&#1088;&#1091;&#1073;.';
			break;
		case 'SEK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'THB' :
			$currency_symbol = '&#3647;';
			break;
		case 'TRY' :
			$currency_symbol = '&#8378;';
			break;
		case 'TWD' :
			$currency_symbol = '&#78;&#84;&#36;';
			break;
		default :
			$currency_symbol = '';
			break;
	}

	return apply_filters( 'hotelier_currency_symbol', $currency_symbol, $currency );
}

if ( ! function_exists( 'htl_price' ) ) {
	/**
	 * Format the price with a currency symbol.
	 *
	 * @param float $price
	 * @param string $currency
	 * @return string
	 */
	function htl_price( $price, $currency = '' ) {
		$thousands_sep = htl_get_price_thousand_separator();
		$decimal_sep   = htl_get_price_decimal_separator();
		$decimals      = htl_get_price_decimals();
		$position      = htl_get_option( 'currency_position', 'before' );
		$price         = number_format( (double) $price, $decimals, $decimal_sep, $thousands_sep );
		$price         = ( $position == 'before' ) ? htl_get_currency_symbol( $currency ) . $price : $price . htl_get_currency_symbol( $currency );
		$return        = '<span class="amount">' . $price . '</span>';

		return apply_filters( 'hotelier_price', $return, $price );
	}
}

if ( ! function_exists( 'htl_price_raw' ) ) {
	/**
	 * Format the price with a currency symbol. Without HTML tags.
	 *
	 * @param float $price
	 * @param string $currency
	 * @return string
	 */
	function htl_price_raw( $price, $currency = '' ) {
		$thousands_sep = htl_get_price_thousand_separator();
		$decimal_sep   = htl_get_price_decimal_separator();
		$decimals      = htl_get_price_decimals();
		$position      = htl_get_option( 'currency_position', 'before' );
		$price         = number_format( (double) $price, $decimals, $decimal_sep, $thousands_sep );
		$price         = ( $position == 'before' ) ? htl_get_currency_symbol( $currency ) . $price : $price . htl_get_currency_symbol( $currency );
		$return        = $price;

		return apply_filters( 'hotelier_price_raw', $return, $price );
	}
}

/**
 * Count cents in prices (prices are stored as integers).
 *
 * @param int $amount
 * @return float
 */
function htl_convert_to_cents( $amount ) {
	$amount = $amount / 100;

	return apply_filters( 'hotelier_convert_to_cents', $amount );
}

/**
 * Calculates the deposit with a currency symbol.
 *
 * @param int $price
 * @param int $deposit
 * @param string $currency
 * @return string
 */
function htl_calculate_deposit( $price, $deposit, $currency = '' ) {
	$price = $price / 100;
	$price = ( $price * $deposit ) / 100;

	$price = htl_price( $price, $currency );

	return apply_filters( 'hotelier_calculate_deposit', $price );
}

/**
 * Gets seasonal prices schema.
 *
 * @return array
 */
function htl_get_seasonal_prices_schema() {
	$schema = htl_get_option( 'seasonal_prices_schema', array() );

	return $schema;
}
