<?php
/**
 * Hotelier Admin Reservations Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Reservations' ) ) :

/**
 * HTL_Admin_Reservations Class
 *
 * Creates the Reservations page.
 */
class HTL_Admin_Reservations {
	/**
	 * Reservations page.
	 *
	 * Handles the display of the Reservations page in admin.
	 */
	public static function output() {
		include_once 'views/html-admin-page-reservations.php';
	}

}

endif;
