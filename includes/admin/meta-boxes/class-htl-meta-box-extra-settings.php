<?php
/**
 * Extra Settings.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Extra_Settings' ) ) :

/**
 * HTL_Meta_Box_Extra_Settings Class
 */
class HTL_Meta_Box_Extra_Settings {
	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/extra/html-meta-box-extra-settings.php';
	}
}

endif;
