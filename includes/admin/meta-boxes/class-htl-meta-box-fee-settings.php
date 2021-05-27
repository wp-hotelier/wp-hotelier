<?php
/**
 * Fee Settings.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Fee_Settings' ) ) :

/**
 * HTL_Meta_Box_Fee_Settings Class
 */
class HTL_Meta_Box_Fee_Settings {
	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/fee/html-meta-box-fee-settings.php';
	}
}

endif;
