<?php
/**
 * Hotelier Admin Addons Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Addons' ) ) :

/**
 * HTL_Admin_Addons Class
 *
 * Creates the Extensions & Themes page.
 */
class HTL_Admin_Addons {
	/**
	 * Get Hotelier addons (extensions & themes)
	 *
	 * @return array of objects
	 */
	public static function get_addons() {
		if ( false === ( $sections = get_transient( 'hotelier_addons' ) ) ) {
			$raw_addons = wp_safe_remote_get( 'https://assets.wphotelier.com/api/addons.json', array( 'user-agent' => 'Hotelier Addons Page' ) );
			if ( ! is_wp_error( $raw_addons ) ) {
				$sections = json_decode( wp_remote_retrieve_body( $raw_addons ) );

				if ( $sections ) {
					set_transient( 'hotelier_addons', $sections, WEEK_IN_SECONDS );
				}
			}
		}

		$addons = array();

		if ( $sections ) {
			$addons = $sections;
		}

		return $addons;
	}

	/**
	 * Handles the display of the Reservations page in admin.
	 */
	public static function output() {
		$addons = self::get_addons();

		include_once 'views/html-admin-page-addons.php';
	}

}

endif;
