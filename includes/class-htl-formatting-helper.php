<?php
/**
 * Hotelier Formatting Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Formatting_Helper' ) ) :

/**
 * HTL_Formatting_Helper Class
 */
class HTL_Formatting_Helper {
	/**
	 * Format and store prices as integers
 	 *
 	 * Sanitize and remove locale formatting
	 */
	public static function sanitize_amount( $amount ) {
		if ( ! $amount ) {
			return 0;
		}

		$is_negative   = false;
		$thousands_sep = htl_get_price_thousand_separator();
		$decimal_sep   = htl_get_price_decimal_separator();

		// Sanitize the amount
		if ( $decimal_sep == ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
			if ( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
				$amount = str_replace( $thousands_sep, '', $amount );
			} elseif( empty( $thousands_sep ) && false !== ( $found = strpos( $amount, '.' ) ) ) {
				$amount = str_replace( '.', '', $amount );
			}

			$amount = str_replace( $decimal_sep, '.', $amount );
		} elseif( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		}

		if ( $amount < 0 ) {
			$is_negative = true;
		}

		$amount = preg_replace( '/[^0-9\.]/', '', $amount );

		if ( $is_negative ) {
			$amount *= -1;
		}

		// Amount can't be empty
		$amount = $amount ? $amount : 0;

		// Here we have a sanitized amount with . as a decimal separator
		// Store it as integer - e.g. 100.50 and 100.5 become 10050
		$amount = $amount * 100;

		return ( int ) $amount;
	}

	/**
	 * Format a price with Currency Locale settings
	 */
	public static function localized_amount( $amount ) {
		$thousands_sep = htl_get_price_thousand_separator();
		$decimal_sep   = htl_get_price_decimal_separator();
		$decimals      = htl_get_price_decimals();

		if ( $amount ) {
			$amount = $amount / 100;
			$amount = number_format( (double) $amount, $decimals, $decimal_sep, '' );
		} else {
			$amount = '';
		}

		return $amount;
	}

	/**
	 * Transforms the php.ini notation for numbers to an integer.
	 */
	public static function notation_to_int( $size ) {
		$l   = substr( $size, -1 );
		$ret = substr( $size, 0, -1 );

		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}

		return $ret;
	}

	/**
	 * Validates a phone number.
	 */
	public static function is_phone( $number ) {
		if ( 0 < strlen( trim( preg_replace( '/[\s\#0-9_\-\+\(\)]/', '', $number ) ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Formats a phone number.
	 */
	public static function validate_phone( $number ) {
		$tel = str_replace( '.', '-', $number );

		return $number;
	}

	/**
	 * Validates date in format YYYY-MM-DD.
	 * @param string $date
	 * @return bool
	 */
	public static function is_valid_date( $date ) {
		$date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';

		if ( ! preg_match( $date_regex, $date ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validates checkin and checkout dates.
	 *
	 * When passing $force, only the format of the dates
	 * and if checkout > checkin is checked.
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @param bool $force
	 * @return bool
	 */
	public static function is_valid_checkin_checkout( $checkin, $checkout, $force = false ) {
		// Check if $checkin and $checkout are valid dates
		if ( ! self::is_valid_date( $checkin ) || ! self::is_valid_date( $checkout ) ) {
			return false;
		}

		if ( ! $force ) {
			// Only allow reservations for "XX" months from current date (0 unlimited).
			$months_advance = htl_get_option( 'booking_months_advance', 0 );

			// Check if arrival date must be "XX" days from current date.
			$arrival_date = htl_get_option( 'booking_arrival_date', 0 );

			// Get minimum number of nights a guest can book
			$minimum_nights = apply_filters( 'hotelier_booking_minimum_nights', htl_get_option( 'booking_minimum_nights', 1 ) );

			// Get maximum number of nights a guest can book (0 unlimited)
			$maximum_nights = apply_filters( 'hotelier_booking_maximum_nights', htl_get_option( 'booking_maximum_nights', 0 ) );
		}

		// Get dates
		$curdate       = new DateTime( current_time( 'Y-m-d' ) );
		$checkin_temp  = new DateTime( $checkin );
		$checkout_temp = new DateTime( $checkout );

		// Check if the checkout date is greater than the checkin date
		if ( $checkin_temp >= $checkout_temp ) {
			return false;
		}

		if ( ! $force ) {
			// Check if the checkin date is greater than today
			if ( $checkin_temp < $curdate ) {
				return false;
			}

			// Check if arrival date is "XX" days from current date
			$diff = date_diff( $curdate, $checkin_temp );
			if ( $diff->days < $arrival_date ) {
				return false;
			}

			// Check if arrival date is "XX" months from current date
			if ( $months_advance !== 0 ) {
				if ( ( $diff->m + $diff->y*12 ) >= $months_advance ) {
					return false;
				}
			}

			// Check minimum and maximum days
			$diff_checkin_checkout = date_diff( $checkin_temp, $checkout_temp )->days;

			if ( ( $minimum_nights && $diff_checkin_checkout < $minimum_nights ) || ( $maximum_nights && $diff_checkin_checkout > $maximum_nights ) ) {
				return false;
			}

			return apply_filters( 'hotelier_is_valid_checkin_checkout', true, $checkin, $checkout );
		}

		return true;
	}

	/**
	 * Validates a date range.
	 *
	 * @todo Do we really need to check if the date is greater than today?
	 * @param string $checkin
	 * @param string $checkout
	 * @return bool
	 */
	public static function is_valid_date_range( $from, $to ) {
		// Check if $from and $to are valid dates
		if ( ! self::is_valid_date( $from ) || ! self::is_valid_date( $to ) ) {
			return false;
		}

		// Get dates
		$curdate  = new DateTime( current_time( 'Y-m-d' ) );
		$from  = new DateTime( $from );
		$to = new DateTime( $to );

		// Check if $from greater than today
		// Skip this check for now
		// This method is useful for the Disable Dates
		// extension also which allows past dates
		//if ( $from < $curdate ) {
			//return false;
		//}

		// Check if $to greater than $from
		if ( $from >= $to ) {
			return false;
		}

		return true;
	}

	/**
	 * Trim a string and append a suffix
	 * @param  string  $string
	 * @param  integer $chars
	 * @param  string  $suffix
	 * @return string
	 */
	public static function trim_string( $string, $chars = 200, $suffix = '...' ) {
		if ( strlen( $string ) > $chars ) {
			$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
		}
		return $string;
	}

	/**
	 * Sanitizes a string key for Hotelier Settings
	 * @param  string  $key
	 * @return string
	 */
	public static function sanitize_key( $key ) {
		$raw_key = $key;
		$key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

		return $key;
	}

	/**
	 * Sanitizes a list of comma separated IDs
	 * @param  string  $key
	 * @return string
	 */
	public static function sanitize_ids( $string ) {
		$string = $string ? implode( ',', wp_parse_id_list( $string ) ) : '';
		return $string;
	}

	/**
	 * Clean inputs using sanitize_text_field. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 * @param string|array $input
	 * @return string|array
	 */
	public static function clean_input( $input ) {
		if ( is_array( $input ) ) {
			return array_map( 'self::clean_input', $input );
		} else {
			return is_scalar( $input ) ? sanitize_text_field( $input ) : $input;
		}
	}
}

endif;
