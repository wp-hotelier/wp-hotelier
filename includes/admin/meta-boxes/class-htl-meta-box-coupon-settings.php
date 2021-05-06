<?php
/**
 * Coupon Settings.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Coupon_Settings' ) ) :

/**
 * HTL_Meta_Box_Coupon_Settings Class
 */
class HTL_Meta_Box_Coupon_Settings {
	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/coupon/html-meta-box-coupon-settings.php';
	}
}

endif;
