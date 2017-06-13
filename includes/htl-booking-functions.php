<?php
/**
 * Hotelier Booking Functions.
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
 * Generate unique key
 *
 * Returns the md5 hash of a string
 *
 * @param string $rate_name Room rate
 * @param string $rate_id Room rate ID
 * @return string
 */
function htl_generate_item_key( $room_id, $rate_id ) {
	return md5( $room_id . '_' . $rate_id );
}
