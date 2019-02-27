<?php
/**
 * Functions for UI.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Core
 * @package  Hotelier/Functions
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Print simple ui notice.
 */
function htl_ui_print_notice( $message, $notice_type = 'info' ) {
	echo '<div class="htl-ui-notice htl-ui-notice--' . $notice_type . '">' . wp_kses_post( $message ) . '</div>';
}
