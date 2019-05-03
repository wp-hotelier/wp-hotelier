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
function htl_ui_print_notice( $message, $notice_type = 'info', $wrapper_class = array(), $class = array() ) {
	$wrapper_class = is_array( $wrapper_class ) && $wrapper_class ? implode( ' ', $wrapper_class ) : false;
	$class         = is_array( $class ) && $class ? implode( ' ', $class ) : false;
	?>

	<div class="htl-ui-notice htl-ui-notice--<?php echo esc_attr( $notice_type ); ?> <?php echo esc_attr( $wrapper_class ); ?>">
		<p class="htl-ui-notice__text htl-ui-notice__text--<?php echo esc_attr( $notice_type ); ?>__text <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $message ); ?></p>
	</div>

	<?php
}
