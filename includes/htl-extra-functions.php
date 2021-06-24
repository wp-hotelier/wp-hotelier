<?php
/**
 * Hotelier Extras Functions.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning extras.
 *
 * @param  mixed $the_extra Post object or post ID of the extra.
 * @return HTL_Extra
 */
function htl_get_extra( $the_extra = false ) {
	return new HTL_Extra( $the_extra );
}
