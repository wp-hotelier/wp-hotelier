<?php
/**
 * Room rate check availability button
 *
 * This template can be overridden by copying it to yourtheme/hotelier/single-room/content/rate/rate-button.php.
 *
 * @author  Benito Lopez <hello@lopezb.com>
 * @package Hotelier/Templates
 * @version 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( htl_get_option( 'booking_mode' ) == 'no-booking' ) {
	return;
}

?>

<p class="rate__check-availability"><a href="#hotelier-datepicker" class="button button--check-availability"><?php esc_html_e( 'Check availability', 'wp-hotelier' ) ?></a></p>
