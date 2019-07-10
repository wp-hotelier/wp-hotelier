<?php
/**
 * View: variations toolbar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="htl-ui-toolbar htl-ui-toolbar--variations <?php echo esc_attr( $position ); ?>">
	<span class="htl-ui-text-icon htl-ui-text-icon--left htl-ui-text-icon--collapse-variation"><?php esc_html_e( 'Close all', 'wp-hotelier' ); ?></span>

	<span class="htl-ui-text-icon htl-ui-text-icon--left htl-ui-text-icon--expand-variation"><?php esc_html_e( 'Expand all', 'wp-hotelier' ); ?></span>

	<button type="button" class="htl-ui-button htl-ui-button--add-room-rate"><?php esc_html_e( 'Add room rate', 'wp-hotelier' ); ?></button>
</div>
