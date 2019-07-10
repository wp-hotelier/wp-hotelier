<?php
/**
 * Room Settings.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Room_Settings' ) ) :

/**
 * HTL_Meta_Box_Room_Settings Class
 */
class HTL_Meta_Box_Room_Settings {

	/**
	 * Get price placeholder.
	 */
	public static function get_price_placeholder() {
		$thousands_sep = htl_get_price_thousand_separator();
		$decimal_sep   = htl_get_price_decimal_separator();
		$decimals      = htl_get_price_decimals();
		$placeholder   = number_format( '0', $decimals, $decimal_sep, $thousands_sep );

		return $placeholder;
	}

	/**
	 * Get deposit options.
	 */
	public static function get_deposit_options() {
		$options =  array(
			'100' => '100%',
			'90'  => '90%',
			'80'  => '80%',
			'70'  => '70%',
			'60'  => '60%',
			'50'  => '50%',
			'40'  => '40%',
			'30'  => '30%',
			'20'  => '20%',
			'10'  => '10%',
		);

		// extensions can hook into here to add their own options
		return apply_filters( 'hotelier_deposit_options', $options );
	}

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/room/html-meta-box-room-settings.php';
	}
}

endif;
