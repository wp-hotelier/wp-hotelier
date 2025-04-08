<?php
/**
 * Hotel Info.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  1.8.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Info' ) ) :

/**
 * HTL_Info Class
 */
class HTL_Info {

	/**
	 * Gets hotel name.
	 *
	 * @return string
	 */
	public static function get_hotel_name() {
		$hotel_name = htl_get_option( 'hotel_name', '' );

		return apply_filters( 'hotelier_get_hotel_name', $hotel_name );
	}

	/**
	 * Gets hotel address.
	 *
	 * @return string
	 */
	public static function get_hotel_address() {
		$hotel_address = htl_get_option( 'hotel_address', '' );

		return apply_filters( 'hotelier_get_hotel_address', $hotel_address );
	}

	/**
	 * Gets hotel postcode.
	 *
	 * @return string
	 */
	public static function get_hotel_postcode() {
		$hotel_postcode = htl_get_option( 'hotel_postcode', '' );

		return apply_filters( 'hotelier_get_hotel_postcode', $hotel_postcode );
	}

	/**
	 * Gets hotel locality.
	 *
	 * @return string
	 */
	public static function get_hotel_locality() {
		$hotel_locality = htl_get_option( 'hotel_locality', '' );

		return apply_filters( 'hotelier_get_hotel_locality', $hotel_locality );
	}

	/**
	 * Gets hotel telephone number.
	 *
	 * @return string
	 */
	public static function get_hotel_telephone() {
		$hotel_telephone = HTL_Formatting_Helper::validate_phone( htl_get_option( 'hotel_telephone', '' ) );

		if ( ! HTL_Formatting_Helper::is_phone( $hotel_telephone ) ) {
			$hotel_telephone = '';
		}

		return apply_filters( 'hotelier_get_hotel_telephone', $hotel_telephone );
	}

	/**
	 * Gets hotel fax number.
	 *
	 * @return string
	 */
	public static function get_hotel_fax() {
		$hotel_fax = htl_get_option( 'hotel_fax', '' );

		return apply_filters( 'hotelier_get_hotel_fax', $hotel_fax );
	}

	/**
	 * Gets hotel email address.
	 *
	 * @return string
	 */
	public static function get_hotel_email() {
		$hotel_email = htl_get_option( 'hotel_email', '' );

		if ( ! is_email( $hotel_email ) ) {
			$hotel_email = '';
		}

		return apply_filters( 'hotelier_get_hotel_email', $hotel_email );
	}

	/**
	 * Helper function to format hotel hours.
	 *
	 * @param array $hours The hours settings array
	 * @param string $filter_name The name of the filter to apply
	 * @return string Formatted time string
	 */
	private static function format_hotel_hours( $hours, $filter_name ) {
		$from           = isset( $hours['from'] ) ? $hours['from'] : 0;
		$to             = isset( $hours['to'] ) ? $hours['to'] : 0;
		$formatted_from = $from > 24 ? $from - 25 : $from;
		$formatted_from = sprintf( '%02d', $formatted_from );
		$formatted_from .= $from > 24 ? ':30' : ':00';
		$formatted_to   = $to > 24 ? $to - 25 : $to;
		$formatted_to   = sprintf( '%02d', $formatted_to );
		$formatted_to   .= $to > 24 ? ':30' : ':00';
		
		// Check if from and to times are the same
		if ( $from === $to ) {
			$formatted_hours = date_i18n( get_option( 'time_format' ), strtotime( $formatted_from ) );
		} else {
			$formatted_hours = date_i18n( get_option( 'time_format' ), strtotime( $formatted_from ) ) . ' - ' . date_i18n( get_option( 'time_format' ), strtotime( $formatted_to ) );
		}

		return apply_filters( $filter_name, $formatted_hours, $from, $to );
	}

	/**
	 * Gets hotel checkin hours.
	 *
	 * @return string
	 */
	public static function get_hotel_checkin() {
		$checkin = htl_get_option( 'hotel_checkin', array() );
		return self::format_hotel_hours( $checkin, 'hotelier_get_hotel_checkin' );
	}

	/**
	 * Gets hotel checkout hours.
	 *
	 * @return string
	 */
	public static function get_hotel_checkout() {
		$checkout = htl_get_option( 'hotel_checkout', array() );
		return self::format_hotel_hours( $checkout, 'hotelier_get_hotel_checkout' );
	}

	/**
	 * Gets hotel pets message.
	 *
	 * @return bool
	 */
	public static function get_hotel_pets_message() {
		$allowed_pets = htl_get_option( 'hotel_pets', false );
		$message      =  $allowed_pets ? htl_get_option( 'hotel_pets_message' ) : esc_html__( 'Pets are not allowed.', 'wp-hotelier' );

		return apply_filters( 'hotelier_get_hotel_pets_message', $message );
	}

	/**
	 * Gets hotel accepted credit cards.
	 *
	 * @return bool
	 */
	public static function get_hotel_accepted_credit_cards() {
		$cards = htl_get_option( 'hotel_accepted_cards', array() );
		$cards = array_keys( $cards );

		return apply_filters( 'hotelier_get_hotel_accepted_credit_cards', $cards );
	}
}

endif;
