<?php
/**
 * Hotelier Meta Boxes Views.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Boxes_Views' ) ) :

/**
 * HTL_Meta_Boxes_Views Class
 */
class HTL_Meta_Boxes_Views {
	/**
	 * Seasonal prices view.
	 */
	public static function seasonal_price( $settings ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/room/settings/html-meta-box-room-view-seasonal-price.php';
	}

	/**
	 * Variations toolbar view.
	 */
	public static function variations_toolbar( $position ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/room/settings/html-meta-box-room-view-variations-toolbar.php';
	}

	/**
	 * Variation header view.
	 */
	public static function variation_header( $variations, $room_rates, $loop ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/room/settings/html-meta-box-room-view-variation-header.php';
	}

	/**
	 * Variation content view.
	 */
	public static function variation_content( $variations, $loop ) {
		include HTL_PLUGIN_DIR . 'includes/admin/meta-boxes/views/room/settings/html-meta-box-room-view-variation-content.php';
	}
}

endif;
