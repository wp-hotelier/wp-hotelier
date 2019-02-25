<?php
/**
 * Reservation Items Meta Boxes.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Reservation_Items' ) ) :

/**
 * HTL_Meta_Box_Reservation_Items Class
 */
class HTL_Meta_Box_Reservation_Items {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $post, $thereservation;

		if ( ! is_object( $thereservation ) ) {
			$thereservation = htl_get_reservation( $post->ID );
		}

		$reservation = $thereservation;

		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/reservation/html-meta-box-reservation-items.php';
	}
}

endif;
