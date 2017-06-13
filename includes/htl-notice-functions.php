<?php
/**
 * Hotelier Message Functions.
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
 * Add and store a notice.
 */
function htl_add_notice( $message, $notice_type = 'notice' ) {
	$notices = HTL()->session->get( 'htl_notices', array() );
	$notices[ $notice_type ][] = apply_filters( 'hotelier_add_' . $notice_type, $message );

	HTL()->session->set( 'htl_notices', $notices );
}

/**
 * Print notices.
 */
function htl_print_notices() {
	$all_notices  = HTL()->session->get( 'htl_notices', array() );
	$notice_types = apply_filters( 'hotelier_notice_types', array( 'error', 'notice' ) );

	foreach ( $notice_types as $notice_type ) {
		if ( htl_notice_count( $notice_type ) > 0 ) {
			htl_get_template( "notices/{$notice_type}.php", array(
				'messages' => $all_notices[ $notice_type ]
			) );
		}
	}

	htl_clear_notices();
}

/**
 * Get the count of notices added.
 */
function htl_notice_count( $notice_type = '' ) {
	$notice_count = 0;
	$notices  = HTL()->session->get( 'htl_notices', array() );

	if ( isset( $notices[ $notice_type ] ) ) {

		$notice_count = absint( sizeof( $notices[ $notice_type ] ) );

	} elseif ( empty( $notice_type ) ) {

		foreach ( $notices as $notice ) {
			$notice_count += absint( sizeof( $notices ) );
		}

	}

	return $notice_count;
}

/**
 * Unset all notices.
 */
function htl_clear_notices() {
	HTL()->session->set( 'htl_notices', null );
}
