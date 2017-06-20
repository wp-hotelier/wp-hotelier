<?php
/**
 * Email guest address (plain text)
 *
 * This template can be overridden by copying it to yourtheme/hotelier/emails/plain/email-guest-address.php
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "\n" . strtoupper( esc_html__( 'Guest address', 'wp-hotelier' ) ) . "\n\n";
echo preg_replace( '#<br\s*/?>#i', "\n", $reservation->get_formatted_guest_address() ) . "\n\n";
