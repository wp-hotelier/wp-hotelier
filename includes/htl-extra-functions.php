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

/**
 * Get all available extras IDs.
 */
function htl_get_all_extras_ids() {
	$extras_ids = get_transient( 'hotelier_extras_ids' );

	// Valid cache found
	if ( false !== $extras_ids ) {
		return $extras_ids;
	}

	$extras = get_posts( array(
		'post_type'           => 'extra',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'fields'              => 'ids',
	) );

	set_transient( 'hotelier_extras_ids', $extras, DAY_IN_SECONDS * 30 );

	return $extras;
}
