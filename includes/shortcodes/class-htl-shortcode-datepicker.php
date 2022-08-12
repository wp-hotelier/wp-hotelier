<?php
/**
 * Datepicker Shortcode Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Shortcodes
 * @package  Hotelier/Classes
 * @version  2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Shortcode_Datepicker' ) ) :

/**
 * HTL_Shortcode_Datepicker Class
 */
class HTL_Shortcode_Datepicker {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return HTL_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		self::datepicker( $atts );
	}

	/**
	 * Show the datepicker form
	 */
	private static function datepicker( $atts ) {
		$checkin  = HTL()->session->get( 'checkin' ) ? HTL()->session->get( 'checkin' ) :  null;
		$checkout = HTL()->session->get( 'checkout' ) ? HTL()->session->get( 'checkout' ) : null;

		// Enqueue the datepicker scripts
		wp_enqueue_script( 'hotelier-init-datepicker' );

		htl_get_template( 'global/datepicker.php', array( 'checkin' => $checkin, 'checkout' => $checkout, 'shortcode_atts' => $atts ) );
	}
}

endif;
