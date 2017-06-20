<?php
/**
 * Email hotel info (plain text)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/plain/email-hotel-info.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo esc_html( HTL_Info::get_hotel_name() ) . "\n";
echo esc_html( HTL_Info::get_hotel_address() . ", " . HTL_Info::get_hotel_postcode() . ", " . HTL_Info::get_hotel_locality() ) . "\n";
echo sprintf( esc_html__( 'Telephone: %s.', 'wp-hotelier' ), HTL_Info::get_hotel_telephone() ) . "\n";
echo sprintf( esc_html__( 'Fax: %s.', 'wp-hotelier' ), HTL_Info::get_hotel_fax() ) . "\n";
echo sprintf( esc_html__( 'Email: %s.', 'wp-hotelier' ), HTL_Info::get_hotel_email() ) . "\n\n";
